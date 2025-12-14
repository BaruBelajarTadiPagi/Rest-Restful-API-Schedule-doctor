<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
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
        $id = $this->route('doctors');
        return [
            'name' => 'required|string|unique:doctors,name,' . $id,
            'photo' => $this->isMethod('post') ? 'required|image|max:2048' : 'sometimes|image|max:2048',
            'about' => 'required|string',
            'yoe' => 'required|integer|min:0',

            // ILMU
            //
            // Jadi di request kita wajib menuliskan exists dari item tersebut
            // contoh pada specialist_id dan hospital_id, saat kita input data
            // pastikan data specialist dan hospital benar2 ada di RS kita, apabila
            // apabila kita menambahkan doctor di ploting rs dan specialist apa
            // sudah dilakukan pengecekan di sini
            //
            // karena hal tersebut bisa juga hacker melakukan inject data
            // apabila kita tidak aware akan hal itu
            'specialist_id' => 'required|exists:specialists,id',
            'hospital_id' => 'required|exists:hospitals,id',

            // begitu pula untuk teknik pada request ini memberikan data default
            // agar data yang di input ke db sesuai dengan permintaan / validasinya
            'gender' => 'required|in:Male,Female',
        ];
    }
}
