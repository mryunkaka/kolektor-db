<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_payments';
    protected $table = 'payments';

    protected $fillable = [
        'id_users',
        'nominal',
        'duration',
        'status',
        'unique_code',
        'payment_proof',
        'bank_destination'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->expires_at)) {
                $payment->expires_at = now()->addMinutes(10);
            }
        });
    }
}
