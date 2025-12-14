<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Http\Requests\SpecialistHospitalDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    private $doctorService;

    public function __construct(DoctorService $doctorService) {
        $this->doctorService = $doctorService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'gender', 'yoe', 'specialist_id', 'hospital_id'];
        $hospitals = $this->doctorService->getAll($fields);
        return response()->json(DoctorResource::collection($hospitals));
    }

    public function show(int $id)
    {
        try
        {
            $fields = ['*'];
            $hospitals = $this->doctorService->getById($id, $fields);
            return response()->json(new DoctorResource($hospitals));
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Hospitals not found'
            ], 404);
        }
    }

    public function store(DoctorRequest $request)
    {
        $hospitals = $this->doctorService->create($request->validated());
        return response()->json(new DoctorResource($hospitals), 201);
    }

    public function update(DoctorRequest $request, int $id)
    {
        try
        {
            $hospitals = $this->doctorService->update($id, $request->validated());
            return response()->json(new DoctorResource($hospitals), 201);
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
            $this->doctorService->delete($id);
            return response()->json([
                'message' => 'Hospitals deleted Successfuly',
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Hospitals not found'
            ], 404);
        }
    }

    public function filterBySpecialistAndHospital(SpecialistHospitalDoctorRequest $request)
    {
        $validated = $request->validated();

        $doctors = $this->doctorService->filterBySpecialistAndHospital(
            $validated['hospital_id'],
            $validated['specialist_id'],
        );

        return DoctorResource::collection($doctors);
    }

    public function availableSlots(int $doctorId)
    {
        try
        {
            $availability = $this->doctorService->getAvailableSlot($doctorId);
            return response()->json(['data' => $availability]);
        }catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Doctor not Found'], 404 );
        }
    }
}
