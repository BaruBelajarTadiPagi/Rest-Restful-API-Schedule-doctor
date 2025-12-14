<?php

namespace App\Services;

use App\Repositories\SpecialistRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SpecialistService
{
    // specialistRepository ini merupakan penghubung service dengan repository
    // dimana bisnis logic diatur di service sedangkan data akses layer untuk proses data
    // diatur di repository agar function di repository bisa digunakan di service
    private SpecialistRepository $specialistRepository;

    public function __construct(SpecialistRepository $specialistRepository)
    {
        $this->specialistRepository = $specialistRepository;
    }

    // jadi apabila ada request dari controller itu tidak langsung ke repository tetapi melewati
    // service terlebih dahulu
    public function getAll(array $fields)
    {
        return $this->specialistRepository->getAll($fields);
    }

    public function getById($id, array $fields)
    {
        return $this->specialistRepository->getById($id, $fields);
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->specialistRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['*'];
        $specialist = $this->specialistRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // hapus foto lama
            if ($specialist->photo) {
                $this->deletePhoto($specialist->photo);
            }
            // upload foto baru
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->specialistRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $specialist = $this->specialistRepository->getById($id, ['photo']);

        // hapus foto lama
        if ($specialist->photo) {
            $this->deletePhoto($specialist->photo);
        }

        return $this->specialistRepository->delete($id);
    }

    // teknik baris => (UploadedFile $photo) : string <= dengan teknik
    // tambahan : string itu menandakan bahwa function ini akan mengembalikan
    // nilai berupa string alias datanya harus berupa string
    private function uploadPhoto (UploadedFile $photo) : string
    {
        return $photo->store('specialists', 'public');
        // teknik ini saat foto di store maka yang dikirimkan link fotonya saja
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'specialists/' . basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
