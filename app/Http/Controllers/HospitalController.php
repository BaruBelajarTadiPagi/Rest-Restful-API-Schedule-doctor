<?php

namespace App\Http\Controllers;

use App\Http\Requests\HospitalRequest;
use App\Http\Resources\HospitalResource;
use App\Services\HospitalService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    private HospitalService $hospitalService;

    public function __construct(HospitalService $hospitalService) {
        $this->hospitalService = $hospitalService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'address', 'photo', 'city', 'phone'];
        $hospitals = $this->hospitalService->getAll($fields);
        return response()->json(HospitalResource::collection($hospitals));
    }

    public function show(int $id)
    {
        try
        {
            $fields = ['*'];
            $hospitals = $this->hospitalService->getById($id, $fields);
            // dd($hospitals);
            return response()->json(new HospitalResource($hospitals));
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Hospitals not found'
            ], 404);
        }
    }

    public function store(HospitalRequest $request)
    {
        $hospitals = $this->hospitalService->create($request->validated());
        return response()->json(new HospitalResource($hospitals), 201);
    }

    public function update(HospitalRequest $request, int $id)
    {
        try
        {
            $hospitals = $this->hospitalService->update($id, $request->validated());
            return response()->json(new HospitalResource($hospitals), 201);
        } catch (ModelNotFoundException $exception)
        {
            return response()->json([
                'message' => 'Hospitals not found'
            ], 404);
        }
    }

    public function destroy(int $id)
    {
        try
        {
            $this->hospitalService->delete($id);
            return response()->json([
                'message' => 'Hospitals deleted Successfuly',
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Hospitals not found'
            ], 404);
        }
    }
}
