<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\BookingTransaction;
use App\Services\BookingTransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyOrderController extends Controller
{
    private $bookingTransactionService;

    public function __construct(BookingTransactionService $bookingTransactionService) {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $orders = $this->bookingTransactionService->getAllForUser($userId);
        return response()->json(TransactionResource::collection($orders));
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $userId = $user->id;
        try
        {
            $orders = $this->bookingTransactionService->getByIdForUser($id, $userId);
            return response()->json(new TransactionResource($orders));
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    public function store(BookingTransactionRequest $request)
    {
        // dd($request->all());
        $transaction = $this->bookingTransactionService->createForUser($request->validated());
        return response()->json(new TransactionResource($transaction), 201);
    }
}
