<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        '_id', 'order', 'ref_number', 'discount', 'customer_id', 'status',
        'subtotal', 'tax', 'order_type', 'items', 'date', 'payment_type',
        'payment_info','customer', 'total', 'paid', 'change', 'till', 'user', 'user_id'
    ];

    protected $casts = [
        '_id' => 'string',
        'order' => 'string',
        'discount' => 'decimal:2',
        'customer_id' => 'string',
        'customer' => 'string',
        'status' => 'integer',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'order_type' => 'integer',
        'items' => 'array',
        'date' => 'datetime',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'change' => 'decimal:2',
        'till' => 'integer',
        'user_id' => 'integer'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', '_id');
    }
}
