<?php

namespace App\Http\Controllers;

use App\Models\DeviceFileLink;
use App\Models\ServiceRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\PDF;

\PhpOffice\PhpWord\Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
\PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');


class ServiceRequest extends Controller
{

    public function index()
    {
        $serviceRequests = ServiceRequests::where('type','Share me Jobs')->where('status','pending')->orderBy('id','asc')->get();
        return response()->json($serviceRequests);
    }

    public function show($id)
    {
        $serviceRequests = ServiceRequests::find($id);

        if (!$serviceRequests) {
            return response()->json(['error' => 'service Requests item not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($serviceRequests);
    }
    public function update(Request $request,$id)
    {
        //$id = $request->input('id');
        $serviceRequests = ServiceRequests::find($id);

        if (!$serviceRequests) {
            return response()->json(['error' => 'service Requests item not found'], Response::HTTP_NOT_FOUND);
        }
        $validated = $request->validate([
            'days' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

//dd($validated);
        $validated['days'] = $validated['days'] ?? '1';
        $validated['status'] = $validated['status'] ?? 'pending';
        $serviceRequests->update($validated);
        return response()->json($serviceRequests);
    }

    public function destroy($id)
    {
        $inventory = ServiceRequests::find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], Response::HTTP_NOT_FOUND);
        }

        $inventory->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable',
            'days' => 'nullable',
            'name' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'education_background' => 'nullable',
            'work_experience' => 'nullable',
            'skills' => 'nullable',
            'status' => 'nullable',
            'cv_file_path' => 'nullable',
           // 'img' => 'nullable', // image file
            'additional_notes' => 'nullable'
        ]);

        $validated['phone'] = $this->sanitizePhoneNumber($validated['phone']);

        // Handle image upload if exists
        if ($request->hasFile('cv_file_path')) {
            $file = $request->file('cv_file_path');
            //$fileName = time().'_'.$file->getClientOriginalName();
            $fileName = time().'_inventory_image.'.$file->extension();

            // store in public/images
            //$file->move(public_path('images'), $fileName);
            //$file->move(base_path('images'), $fileName);
            $path = $request->file('cv_file_path')->store('', 'public');


            // generate full URL (works on localhost too)
            $validated['cv_file_path'] = URL::to('/images/'.$path);
            //dd($validated['img']);
        }
        // dd($validated);
            $inventory = ServiceRequests::create($validated);

        return response()->json($inventory, Response::HTTP_CREATED);
    }
    public function sanitizePhoneNumber(?string $phone): ?string
    {
        if (!$phone || $phone === 'null') {
            return null; // Keep null values as null
        }

        // Remove all non-digit characters first (spaces, slashes, +, etc.)
        $cleaned = preg_replace('/\D/', '', $phone);

        // Handle multiple numbers separated by slashes - take first one
        if (str_contains($cleaned, '/')) {
            $cleaned = explode('/', $cleaned)[0];
        }

        // Handle multiple numbers with spaces - take first one
        if (strlen($cleaned) > 12) {
            // If it's too long, it might contain multiple numbers
            // Try to find a valid 9-digit number after the country code
            if (preg_match('/(260)?(\d{9})/', $cleaned, $matches)) {
                $cleaned = '260' . $matches[2];
            } else {
                // If no clear pattern, take first 12 digits
                $cleaned = substr($cleaned, 0, 12);
            }
        }

        // Convert to standard +260 format
        if (str_starts_with($cleaned, '260') && strlen($cleaned) === 12) {
            // Already in correct format (without +)
            return '+' . $cleaned;
        } elseif (str_starts_with($cleaned, '0') && strlen($cleaned) === 10) {
            // Format: 0XXXXXXXXX -> +260XXXXXXXX
            return '+260' . substr($cleaned, 1);
        } elseif (strlen($cleaned) === 9) {
            // Format: XXXXXXXXX -> +260XXXXXXXX
            return '+260' . $cleaned;
        } elseif (str_starts_with($cleaned, '260') && strlen($cleaned) > 12) {
            // Has 260 prefix but too long - take first 12 digits
            return '+' . substr($cleaned, 0, 12);
        } elseif (strlen($cleaned) === 12 && !str_starts_with($cleaned, '260')) {
            // 12 digits but wrong prefix
            return '+260' . substr($cleaned, 3);
        }

        // Final validation
        if (strlen($cleaned) !== 12 || !str_starts_with($cleaned, '260')) {
            \Log::warning("Invalid phone number format: {$phone} -> {$cleaned}");
            return null;
        }

        return '+' . $cleaned;
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:cover_letter,cv',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'experience' => 'nullable|string',
            'skills' => 'nullable|string',
            'education' => 'nullable|string',
            'company' => 'nullable|string',
            'tone' => 'nullable|string',
        ]);

        // Build the text prompt dynamically
        if ($validated['type'] === 'cover_letter') {
            $prompt = sprintf(
                "Write a professional cover letter for %s applying for the position of %s. ".
                "%s has %s of experience in %s. Education: %s. ".
                "The letter should be written in a %s tone, suitable for submission to %s. ".
                "End the letter with 'Sincerely, %s'.",
                $validated['name'],
                $validated['position'] ?? 'an IT role',
                $validated['name'],
                $validated['experience'] ?? '5 years',
                $validated['skills'] ?? 'software development and IT systems',
                $validated['education'] ?? 'BSc in Information Technology (Merit)',
                $validated['tone'] ?? 'formal and confident',
                $validated['company'] ?? 'a technology company',
                $validated['name']
            );
        } else {
            $prompt = sprintf(
                "Generate a complete professional CV for %s with %s of experience in %s. ".
                "Education: %s. Include sections for Contact Info, Profile Summary, Skills, Experience, and Education. ".
                "Write in a clean and structured format.",
                $validated['name'],
                $validated['experience'] ?? '5 years',
                $validated['skills'] ?? 'software development, IT systems, and coding',
                $validated['education'] ?? 'BSc in Information Technology (Merit)'
            );
        }

        // Send the request to Pollinations.ai
        $response = Http::get('https://text.pollinations.ai/'.$prompt);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to generate text'], 500);
        }

        return response()->json([
            'type' => $validated['type'],
            'prompt' => $prompt,
            'result' => $response->body(),
        ]);
    }
/*
    public function generatenew(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:cover_letter,cv',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'experience' => 'nullable|string',
            'skills' => 'nullable|string',
            'education' => 'nullable|string',
            'company' => 'nullable|string',
            'tone' => 'nullable|string',
            'format' => 'nullable|string|in:pdf,docx,txt',
            'device_id' => 'required|string',
        ]);

        // --- Build the prompt ---
        $prompt = $this->buildPrompt($validated);


        // --- Request from Pollinations ---
        $response = Http::get('https://text.pollinations.ai/'.$prompt);
        $result = $response->successful() ? trim($response->body()) : 'Failed to generate text.';

        // --- Save file ---
        $extension = $validated['format'] ?? 'txt';
        $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $extension;
        $path = storage_path('app/public/generated/' . $fileName);

        // Create directory if not exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        // Generate file
        if ($extension === 'pdf') {
                $html = '
        <html>
            <head><meta charset="utf-8"><style>body{font-family: DejaVu Sans, sans-serif;}</style></head>
            <body>' . nl2br(e($result)) . '</body>
        </html>';

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                file_put_contents($path, $pdf->output());

        } elseif ($extension === 'docx') {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            foreach (explode("\n", $result) as $line) {
                if (preg_match('/^\*\*(.*?)\*\*\/', $line, $m)) {
                    $section->addText($m[1], ['bold' => true]);
                } else {
                    $section->addText($line);
                }
            }
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($path);
        } else {
            //Storage::disk('public')->put('generated/' . $fileName, $result);
            return response()->json([
                'type' => $validated['type'],
                'prompt' => $prompt,
                'result' => $response->body(),
            ]);
        }

        // --- Create a public URL ---
        $fileUrl = URL::to('/storage/generated/' . $fileName);

        // --- Save to database for that device ---
        DeviceFileLink::create([
            'device_id' => $validated['device_id'],
            'file_url' => $fileUrl,
            'file_type' => $validated['type'],
        ]);

        // --- Return JSON response ---
        return response()->json([
            'status' => 'success',
            'type' => $validated['type'],
            'format' => $extension,
            'file_link' => $fileUrl,
            'text_result' => $result,
        ]);
    }
*/
    public function generatenew(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:cover_letter,cv',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'experience' => 'nullable|string',
            'skills' => 'nullable|string',
            'education' => 'nullable|string',
            'company' => 'nullable|string',
            'tone' => 'nullable|string',
            'format' => 'nullable|string|in:pdf,docx,txt',
            'device_id' => 'required|string',
        ]);

        // --- Build the prompt ---
        $prompt = $this->buildPrompt($validated);

        // --- Request from Pollinations ---
        $response = Http::get('https://text.pollinations.ai/' . $prompt);
        $result = $response->successful() ? trim($response->body()) : 'Failed to generate text.';

        // --- Save file in public/generated ---
        $extension = $validated['format'] ?? 'txt';
        $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $extension;
        $destinationPath = public_path('generated'); // public/generated

        // Create directory if not exists
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fullPath = $destinationPath . '/' . $fileName;

        // Generate file
        if ($extension === 'pdf') {
            $html = '
        <html>
            <head><meta charset="utf-8"><style>body{font-family: DejaVu Sans, sans-serif;}</style></head>
            <body>' . nl2br(e($result)) . '</body>
        </html>';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            file_put_contents($fullPath, $pdf->output());

        } elseif ($extension === 'docx') {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            foreach (explode("\n", $result) as $line) {
                if (preg_match('/^\*\*(.*?)\*\*/', $line, $m)) {
                    $section->addText($m[1], ['bold' => true]);
                } else {
                    $section->addText($line);
                }
            }
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($fullPath);

        } else {
            // For txt files
            file_put_contents($fullPath, $result);
        }

        // --- Create a public URL ---
        $fileUrl = url('public/generated/' . $fileName);

        // --- Save to database for that device ---
        DeviceFileLink::create([
            'device_id' => $validated['device_id'],
            'file_url' => $fileUrl,
            'file_type' => $validated['type'],
        ]);

        // --- Return JSON response ---
        return response()->json([
            'status' => 'success',
            'type' => $validated['type'],
            'format' => $extension,
            'file_link' => $fileUrl,
            'text_result' => $result,
        ]);
    }

    private function buildPrompt(array $data)
    {
        if ($data['type'] === 'cover_letter') {
            return sprintf(
                "Write a professional cover letter for %s applying for the position of %s. %s has %s of experience in %s. Education: %s. The letter should be written in a %s tone, suitable for submission to %s. End with 'Sincerely, %s'.",
                $data['name'],
                $data['position'] ?? 'an IT role',
                $data['name'],
                $data['experience'] ?? '5 years',
                $data['skills'] ?? 'software development and IT systems',
                $data['education'] ?? 'BSc in Information Technology (Merit)',
                $data['tone'] ?? 'formal and confident',
                $data['company'] ?? 'a technology company',
                $data['name']
            );
        }

        return sprintf(
            "Generate a complete professional CV for %s with %s of experience in %s. Education: %s. Include sections for Contact Info, Profile Summary, Skills, Experience, and Education. Write in a clean and structured format.",
            $data['name'],
            $data['experience'] ?? '5 years',
            $data['skills'] ?? 'software development, IT systems, and coding',
            $data['education'] ?? 'BSc in Information Technology (Merit)'
        );
    }
}
