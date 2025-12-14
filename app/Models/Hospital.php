<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Hospital extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'photo',
        'about',
        'address',
        'city',
        'post_code',
        'phone',
    ];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function specialists()
    {
        // disini menggunakan many to many, karena penggunaan ke tabel hospital_specialist
        // jadi apabila hospital spesialis ini dapat banyak data spesialist
        return $this->belongsToMany(Specialist::class, 'hospital_specialists');
    }

    public function getPhotoAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return url(Storage::url($value));
    }
}
