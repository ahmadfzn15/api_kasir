<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function market()
    {
        return $this->belongsTo(Market::class, 'id_toko');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'id_kasir');
    }
}
