<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $id = $this->route('specialists');

        return [
            // apabila kita mau edit data dan
            // name di field db tidak diganti, maka dia akan lolos dari unique
            // contoh :
            // apabila data dari name itu 'kulit' dan data about dan photo di ganti,
            // maka hanya mengganti data about dan photo saja
            // karena data di name yaitu 'kulit' tidak berubah, dan lolos dari validasi
            'name' => 'required|string|unique:specialists,name,'.$id,

            // apabila foto tersebut dalam kondisi method 'post' di form nya / alias belum di isi datanya,
            // maka validator memberi tahu kalau wajib menambahkan foto (adanya permintaan required)
            // apabila suatu kondisi berada di form edit, validator untuk field foto, itu berupa 'sometimes'accepted
            // yang artinya, kamu bebas menambahkan foto baru atau pakai foto lama (tidak di ubah) tetapi apabila foto kosong
            // maka validator memberikan warning kalau foto tersebut wajib di isi saat klik update
            'photo' => $this->isMethod('post') ? 'required|image|max:2048' : 'sometimes|image|max:2048',
            'about' => 'required|string',
            'price' => 'required|numeric|min:0',
        ];
    }
}
