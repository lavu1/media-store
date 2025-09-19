<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        '_id', 'name'
    ];

    protected $casts = [
        '_id' => 'string'
    ];

    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'category_id', '_id');
    }
}
