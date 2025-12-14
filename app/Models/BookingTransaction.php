<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class BookingTransaction extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'status',
        'started_at',
        'time_at',
        'sub_total',
        'tax_total',
        'grand_total',
        'proof',
    ];

    // di laravel ada namanya cast, yaitu untuk mengubah tipe data seperti
    // started_at dan time_at menjadi string
    // dan dimana kita bisa menggunakan format tanggal yang baik

    protected $casts = [
        'started_at' => 'date',
        'time_at' => 'dateTime:H:i'
    ];

    // model relasi lama untuk manggil model lain
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // model relasi baru untuk manggil model lain, laravel sudah tau mana yang kita pakai nanti
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // nantinya kita mendapatkan full url foto alamat pembayaran (proof) dan
    // penyimpanannya ada di storage
    public function getProofAttribute($value)
    {
        if (!$value) {
            return null; // kalau ga ada fotonya
        }

        return url(Storage::url($value));
        // contoh nanti : domainkita.com/storage/namafile.jpg
    }
}
