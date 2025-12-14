<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        // ILMU
        //
        // Kali ini return nya berbeda karena kali ini
        // kita akan menampilkan semua data yang dibutuhkan apa saja
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo' => $this->photo,
            'about' => $this->about,
            'address' => $this->address,
            'city' => $this->city,
            'post_code' => $this->post_code,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // karena pada tampilan hospital kita tidak hanya menampilkan data sesuai di db table
            // tetapi menampilkan juga jumlah doctor nya di RS tersebut
            //
            // bagaimana data $this doctor dan specialist di peroleh ?
            // dari fun di model hospital saat ini
            // 'doctors_count' => $this->doctors->count(),
            // 'specialists_count' => $this->specialists->count(),
            'doctors_count' => $this->whenLoaded('doctors', fn() => $this->doctors->count()),
            'specialists_count' => $this->whenLoaded('specialists', fn() => $this->specialists->count()),

            // kemudian kita ingin menampilkan data hospital dengan detail ada berapa doctor (siapa saja)
            // dan juga berapa specialist yang tersedia di hospital tsb
            //
            // INGAT YA !? kita mau ambil seluruh data table db tersebut makanya pakai collection
            // data doctor dan specialist itu sudah kamu kirim sebelumnya melalui repositorynya ya
            // DI INGATT ASAL DATANYA / DILEMPAR DARI MANA ASALNYA ?!
            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
            'specialists' => SpecialistResource::collection($this->whenLoaded('specialists')),
        ];
    }
}
