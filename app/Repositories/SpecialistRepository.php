<?php

namespace App\Repositories;

use App\Models\Specialist;

class SpecialistRepository
{
    public function getAll(array $fields)
    {
        return Specialist::select($fields)->latest()->with('hospitals','doctors')->paginate(10);

        // return Specialist::select($fields) kenapa tekniknya seperti ini?
        // karena dengan kita menentukan fields yang ingin kita ambil
        // maka query yang dihasilkan akan lebih efisien dan cepat
        // daripada kita mengambil semua fields dengan menggunakan *
        // karena dengan mengambil semua fields, maka database akan mengambil
        // semua data yang ada di tabel, padahal kita hanya butuh beberapa fields saja

        // ->with('hospital','doctor')->paginate(10) teknik ini disebut QUERY OPTIMIZATION
        // yang efisien dalam query
        // karena dengan kita menggunakan eager loading (with) maka sistem tidak perlu
        // menunggu query apa yang dibutuhkan dan dijalankan satu per satu tetapi sudah menyiapkannya
        // lebih dulu

        // last minute 4:33
    }

    public function getById($id, array $fields)
    {
        return Specialist::select($fields)
        ->with([
            'hospitals' => function ($query) use ($id)
            {
                $query->withCount(['doctors as doctors_count' => function ($query) use ($id) {
                    $query->where('specialist_id', $id);
                }]);
            },
            'doctors' => function ($query) use ($id)
            {
                $query->where('specialist_id', $id)
                    ->with('hospitals:id,name,city,post_code');
            }
        ])
        ->findOrFail($id);
    }

    public function create(array $data)
    {
        return Specialist::create($data);
    }

    public function update(int $id, array $data)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->update($data);
        return $specialist;
    }

    public function delete(int $id)
    {
        $specialist = Specialist::findOrFail($id);
        return $specialist->delete();
    }
}
