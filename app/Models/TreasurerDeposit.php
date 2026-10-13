<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreasurerDeposit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'processed_at' => 'datetime',
        'date' => 'date',
    ];

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function treasurer()
    {
        return $this->belongsTo(User::class, 'treasurer_id');
    }

    public function mechanicCashBook()
    {
        return $this->belongsTo(CashBook::class, 'mechanic_cash_book_id');
    }

    public function treasurerCashBook()
    {
        return $this->belongsTo(CashBook::class, 'treasurer_cash_book_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
