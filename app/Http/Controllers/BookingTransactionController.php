<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Services\BookingTransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    private $bookingTransactionService;

    public function __construct(BookingTransactionService $bookingTransactionService) {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    public function index()
    {
        $transaction = $this->bookingTransactionService->getAll();
        return response()->json(TransactionResource::collection($transaction));
    }

    public function show(int $id)
    {
        try
        {
            $transaction = $this->bookingTransactionService->getByIdForManager($id);
            return response()->json(new TransactionResource($transaction));

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }
    }

    public function updateStatus(int $id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        try
        {
            $transaction = $this->bookingTransactionService->updateStatusTransaction($id, $request['status']);
            return response()->json([
                'messagge' => 'Transaction status updated successfully.',
                'data' => new TransactionResource($transaction)
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }
    }
}
