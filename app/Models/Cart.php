<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Produto::class, 'product_id');
    }

    public function getFormattedTotalAttribute()
    {
        return 'R$ ' . number_format($this->product->preco * $this->quantity, 2, ',', '.');
    }
}
