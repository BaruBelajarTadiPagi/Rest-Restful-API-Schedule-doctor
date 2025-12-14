<?php

namespace App\Repositories;

use App\Models\BookingTransaction;

class BookingTransactionRepository
{

    // manager query
    public function getAll()
    {
        return BookingTransaction::with(['doctor', 'doctor.hospital','doctor.specialist', 'user'])
            ->latest()
            ->paginate(10);
    }

    public function getByIdForManager($id)
    {
        return BookingTransaction::with(['doctor','doctor.hospital','doctor.specialist'])
            ->findOrFail($id);
    }

    public function updateStatusTransaction($idTransaction, $status)
    {
        $transaction = $this->getByIdForManager($idTransaction  );
        $transaction->update(['status' => $status]);
        return $transaction;
    }

    // costumer query
    public function getAllForUser($userId)
    {
        return BookingTransaction::where('user_id', $userId)
            ->with(['doctor','doctor.hospital','doctor.specialist'])
            ->latest()
            ->paginate(10);
    }

    public function getByIdForUser($id, $userId)
    {
        return BookingTransaction::where('id', $id)
            ->where('user_id', $userId)
            ->with(['doctor','doctor.hospital','doctor.specialist'])
            ->latest()
            ->paginate(10);
    }

    public function createForUser(array $data)
    {
        return BookingTransaction::create($data);
    }

    public function isTimeSlotTakenForDoctor(int $doctorId, string $date, string $time)
    {
        return BookingTransaction::where('doctor_id', $doctorId)
        ->where('started_at', $date)
        ->where('time_at', $time)
        ->exists();
    }

}
