<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_vehicles';
    protected $table = 'vehicles';

    protected $fillable = [
        'no_kontrak',
        'nama_konsumen',
        'no_polisi',
        'no_rangka',
        'no_mesin',
        'merk_tipe',
        'past_due',
        'nama_resort',
        'nama_sector',
        'nama_sub_sector',
        'product'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }
}
