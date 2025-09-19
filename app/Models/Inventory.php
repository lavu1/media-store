<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        '_id', 'barcode', 'expiration_date', 'price', 'category_id',
        'quantity', 'name', 'stock', 'min_stock', 'img'
    ];

    protected $casts = [
        '_id' => 'string',
        'barcode' => 'string',
        'expiration_date' => 'date',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'stock' => 'integer',
        'min_stock' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', '_id');
    }
}
