<?php

namespace App\Services;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Repositories\DoctorRepository;
use App\Repositories\HospitalSpecialistRepository;

class DoctorService
{
    // disini kita ingin inject 2 repository pada service ini
    private $doctorRepository;
    private $hospitalSpecialistRepository;

    public function __construct(DoctorRepository $doctorRepository, HospitalSpecialistRepository $hospitalSpecialistRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->hospitalSpecialistRepository = $hospitalSpecialistRepository;
    }

    public function getAll(array $fields)
    {
        return $this->doctorRepository->getAll($fields);
    }

    public function getById($id, array $fields)
    {
        return $this->doctorRepository->getById($id, $fields);
    }

    // saat page doctor service kita kemungkinan terburuknya diacak2 oleh hacker itu tidak bisa
    // karena kita melakukan pengecekan pada service ini sebelum repository menerima request
    public function create(array $data)
    {
        if (!$this->hospitalSpecialistRepository->existForHospitalAndSpecialist
            ($data['hospital_id'], $data['specialist_id']))
            {
                throw ValidationException::withMessages
                (['specialist_id' => 'Specialist not available in the selected hospital']);
            }

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->doctorRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        if (!$this->hospitalSpecialistRepository->existForHospitalAndSpecialist
            ($data['hospital_id'], $data['specialist_id']))
            {
                throw ValidationException::withMessages
                (['specialist_id' => 'Specialist not available in the selected hospital']);
            }

        $doctor = $this->doctorRepository->getById($id, ['*']);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($doctor->photo)) {
                $this->deletePhoto($doctor->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->doctorRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $doctor = $this->doctorRepository->getById($id, ['photo']);

        // hapus foto lama
        if ($doctor->photo) {
            $this->deletePhoto($doctor->photo);
        }

        return $this->doctorRepository->delete($id);
    }

    private function uploadPhoto (UploadedFile $photo) : string
    {
        return $photo->store('doctors', 'public');
        // teknik ini saat foto di store maka yang dikirimkan link fotonya saja
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'doctors/' . basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function filterBySpecialistAndHospital(int $hospitalId, int $specialistId)
    {
        return $this->doctorRepository->filterBySpecialistAndHospital($hospitalId, $specialistId);
    }

    public function getAvailableSlot(int $doctorId)
    {
        $doctor = $this->doctorRepository->getById($doctorId, ['id']);

        $dates = collect([
            now()->addDays(1)->startOfDay(),
            now()->addDays(2)->startOfDay(),
            now()->addDays(3)->startOfDay(),
        ]);

        $timeSlots = ["10:30", "11:30", "13:30", "14:30", "15:30", "16:30"];

        // array kosong disini sebagai pembungkus data yang diipilih nantinya
        $availability = [];

        foreach($dates as $date)
        {
            $dateStr = $date->toDateString();

            $availability[$dateStr] = [];

            foreach ($timeSlots as $time)
            {
                $isTaken = $doctor->bookingTransactions()
                ->whereDate('started_at', $dateStr)
                ->whereTime('time_at', $time)
                ->exists();

                if (!$isTaken)
                {
                    $availability[$dateStr][] = $time;
                }
            }
        }
        return $availability;
    }
    // Service methods for Doctor entity
}
