<?php

namespace App\Services;

use App\Repositories\BookingTransactionRepository;
use App\Repositories\DoctorRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class BookingTransactionService
{
    private $bookingTransactionRepository;
    private $doctorRepository;

    public function __construct
    (
        BookingTransactionRepository $bookingTransactionRepository,
        DoctorRepository $doctorRepository
    )
    {
        $this->bookingTransactionRepository = $bookingTransactionRepository;
        $this->doctorRepository = $doctorRepository;
    }

    // Manager Services
    public function getAll()
    {
        return $this->bookingTransactionRepository->getAll();
    }

    public function getByIdForManager($idTransaction)
    {
        return $this->bookingTransactionRepository->getByIdForManager($idTransaction);
    }

    public function updateStatusTransaction(int $idTransaction, string $status)
    {
        if(!in_array($status, ['Approved', 'Rejected']))
        {
            throw ValidationException::withMessages([
                'status' => ['Invalid status value!']
            ]);
        }

        return $this->bookingTransactionRepository->updateStatusTransaction($idTransaction, $status);
    }

    // Customer Services
    public function getAllForUser(int $userId)
    {
        return $this->bookingTransactionRepository->getAllForUser($userId);
    }

    public function getByIdForUser (int $idTransaction, int $userId)
    {
        return $this->bookingTransactionRepository->getByIdForUser($idTransaction, $userId);
    }

    public function createForUser(array $data)
    {
        $data['user_id'] = auth()->id();

        // tambahan dari chatgpt karena ada masalah dateTime:H:i ga bisa
        // $data['time_at'] = Carbon::createFromFormat(
        //     'Y-m-d H:i',
        //     "{$data['started_at']} {$data['time_at']}"
        // );
        $data['time_at'] = Carbon::createFromFormat('H:i', $data['time_at'])->format('H:i:s');

        // pengecekan apakah doctor masih ada waktu di tanggal tersebut atau tidak
        if ($this->bookingTransactionRepository->isTimeSlotTakenForDoctor(
                $data['doctor_id'],
                $data['started_at'],
                $data['time_at']
            ))
        {
            throw ValidationException::withMessages([
                'time_at' => ['Waktu yang dipilih untuk dokter ini sudah terisi.']
            ]);
        }

        $doctor = $this->doctorRepository->getById($data['doctor_id'], ['*']);

        $price = $doctor->specialist->price;

        // tax ini adalah pajak dari harga specialist tersebut (dlm contoh 10%)
        $tax = (int) round($price * 0.10);
        $grandTotal = $price + $tax;

        $data['sub_total'] = $price;
        $data['tax_total'] = $tax;
        $data['grand_total'] = $grandTotal;
        $data['status'] = 'Waiting';

        if(isset($data['proof']) && $data['proof'] instanceof UploadedFile )
        {
            $data['proof'] = $this->uploadProof($data['proof']);
        }

        // dd($data['started_at'], $data['time_at']);

        return $this->bookingTransactionRepository->createForUser($data);
    }

    private function uploadProof(UploadedFile $file)
    {
        return $file->store('proof', 'public');
    }

}
