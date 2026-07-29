<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBook extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBengkel($query)
    {
        return $query->where('category', 'bengkel');
    }

    public function scopeBendahara($query)
    {
        return $query->where('category', 'bendahara');
    }
}
