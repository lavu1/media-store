<?php
// WordPress API Configuration
$client_id = '109141';
$client_secret = 'smBiTuQN3VMJYeYGCbFQVNER4a6hDTkMnhba4shuBmzDf0DXka1zEHWzloqxHzgH';
$username = 'lavum27@gmail.com';
$password = 'tZZ=g3wD%Lk)4U=';
$site_id = '248777176';

// Function to get access token
function getAccessToken($client_id, $client_secret, $username, $password) {
    $token_url = 'https://public-api.wordpress.com/oauth2/token';

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $token_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: WordPress-Stats-Dashboard/1.0'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($http_code !== 200) {
        return [
            'success' => false,
            'error' => 'Token HTTP Error: ' . $http_code . ' - ' . $error,
            'access_token' => null
        ];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Token JSON decode error: ' . json_last_error_msg(),
            'access_token' => null
        ];
    }

    if (!isset($data['access_token'])) {
        return [
            'success' => false,
            'error' => 'No access token in response',
            'access_token' => null
        ];
    }

    return [
        'success' => true,
        'error' => null,
        'access_token' => $data['access_token']
    ];
}

// Function to get WordPress stats
function getWordPressStats($site_id, $access_token) {
    $api_url = "https://public-api.wordpress.com/rest/v1.1/sites/{$site_id}/stats";

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
            'User-Agent: WordPress-Stats-Dashboard/1.0'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($http_code !== 200) {
        return [
            'success' => false,
            'error' => 'Stats HTTP Error: ' . $http_code . ' - ' . $error,
            'data' => null
        ];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Stats JSON decode error: ' . json_last_error_msg(),
            'data' => null
        ];
    }

    return [
        'success' => true,
        'error' => null,
        'data' => $data
    ];
}

// Main execution flow
$token_response = getAccessToken($client_id, $client_secret, $username, $password);

if (!$token_response['success']) {
    $error_message = $token_response['error'];
    $stats_data = null;
    $visits_data = null;
    $report_date = date('Y-m-d');
} else {
    $access_token = $token_response['access_token'];

    // Now get the stats data using the access token
    $api_response = getWordPressStats($site_id, $access_token);

    if (!$api_response['success']) {
        $error_message = $api_response['error'];
        $stats_data = null;
        $visits_data = null;
        $report_date = date('Y-m-d');
    } else {
        $stats_data = $api_response['data']['stats'] ?? null;
        $visits_data = $api_response['data']['visits']['data'] ?? null;
        $report_date = $api_response['data']['date'] ?? date('Y-m-d');

        // Reverse the visits data to show today first
        if ($visits_data && is_array($visits_data)) {
            $visits_data = array_reverse($visits_data);
        }
    }
}
?>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: none;
        }
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px 20px;
        }
        .highlight-number {
            font-size: 1.1em;
            font-weight: bold;
            color: #667eea;
        }
        .positive-trend {
            color: #28a745;
            font-weight: bold;
        }
        .negative-trend {
            color: #dc3545;
            font-weight: bold;
        }
        .last-updated {
            font-size: 0.9em;
            color: #6c757d;
            text-align: right;
        }
        .error-alert {
            margin: 20px 0;
        }
        .today-highlight {
            background-color: #e3f2fd !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">📊 Stats Dashboard</h1>
                <?php if (isset($report_date)): ?>
                <div class="last-updated">Last updated: <?php echo htmlspecialchars($report_date); ?></div>
                <?php endif; ?>
            </div>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger error-alert" role="alert">
                <h4 class="alert-heading">⚠️ API Request Failed</h4>
                <p class="mb-0"><?php echo htmlspecialchars($error_message); ?></p>
                <hr>
                <p class="mb-0">Please check your credentials and try again.</p>
            </div>
            <?php else: ?>

                <!-- Summary Statistics Table -->
            <div class="card stats-card">
                <div class="table-header">
                    <h3 class="mb-0">📈 Summary Statistics</h3>
                </div>
                <div class="card-body">
                    <table id="summaryTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Today</th>
                            <th>Yesterday</th>
                            <th>All Time</th>
                            <th>Best Day</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if ($stats_data): ?>
                        <tr>
                            <td><strong>Visitors</strong></td>
                            <td class="highlight-number"><?php echo htmlspecialchars($stats_data['visitors_today']); ?></td>
                            <td><?php echo htmlspecialchars($stats_data['visitors_yesterday']); ?></td>
                            <td class="highlight-number"><?php echo htmlspecialchars($stats_data['visitors']); ?></td>
                            <td>
                                    <?php echo htmlspecialchars($stats_data['views_best_day']); ?>
                                (<?php echo htmlspecialchars($stats_data['views_best_day_total']); ?>)
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Views</strong></td>
                            <td class="highlight-number"><?php echo htmlspecialchars($stats_data['views_today']); ?></td>
                            <td><?php echo htmlspecialchars($stats_data['views_yesterday']); ?></td>
                            <td class="highlight-number"><?php echo htmlspecialchars($stats_data['views']); ?></td>
                            <td>
                                    <?php echo htmlspecialchars($stats_data['views_best_day']); ?>
                                (<?php echo htmlspecialchars($stats_data['views_best_day_total']); ?>)
                            </td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daily Visits Table -->
            <div class="card stats-card">
                <div class="table-header">
                    <h3 class="mb-0">📅 Daily Visits </h3>
                </div>
                <div class="card-body">
                    <table id="dailyVisitsTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Views</th>
                            <th>Visitors</th>
                            <th>Views per Visitor</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if ($visits_data && is_array($visits_data)): ?>
                            <?php foreach ($visits_data as $index => $visit): ?>
                            <?php
                            $date = $visit[0];
                            $views = $visit[1];
                            $visitors = $visit[2];
                            $views_per_visitor = $visitors > 0 ? round($views / $visitors, 2) : 0;
                            $is_today = $date === $report_date;
                            ?>
                        <tr class="<?php echo $is_today ? 'today-highlight' : ''; ?>">
                            <td>
                                    <?php echo htmlspecialchars($date); ?>
                                    <?php if ($is_today): ?>
                                <span class="badge bg-primary">Today</span>
                                <?php endif; ?>
                                    <?php if ($index === 0 && !$is_today): ?>
                                <span class="badge bg-info">Latest</span>
                                <?php endif; ?>
                            </td>
                            <td class="highlight-number"><?php echo htmlspecialchars($views); ?></td>
                            <td class="highlight-number"><?php echo htmlspecialchars($visitors); ?></td>
                            <td>
                                                <span class="badge bg-<?php echo $views_per_visitor >= 2 ? 'success' : ($views_per_visitor > 0 ? 'warning' : 'secondary'); ?>">
                                                    <?php echo $views_per_visitor; ?>
                                                </span>
                            </td>
                            <td>
                                    <?php if ($views > 0): ?>
                                <span class="positive-trend">● Active</span>
                                <?php else: ?>
                                <span class="negative-trend">● No activity</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No visits data available</td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#summaryTable').DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
            order: [[0, 'asc']]
        });

        // Daily visits table - show today first (no sorting on load)
        $('#dailyVisitsTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            order: [], // No initial sorting - preserve the order from PHP
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [
                { orderable: true, targets: '_all' }
            ]
        });

        // Auto-refresh every 5 minutes
        setInterval(function() {
            location.reload();
        }, 300000); // 5 minutes
    });
</script>
</body>
</html>
