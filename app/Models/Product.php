<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'harga_satuan',
        'status',
        'exp_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_product_category', 'id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'id_product_size', 'id');
    }


}
