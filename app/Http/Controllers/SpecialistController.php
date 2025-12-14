<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialistRequest;
use App\Http\Resources\SpecialistResource;
use App\Services\SpecialistService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    private $specialistService;

    public function __construct(SpecialistService $specialistService)
    {
        $this->specialistService = $specialistService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'about', 'price'];
        $specialists = $this->specialistService->getAll($fields);
        return response()->json(SpecialistResource::collection($specialists));
    }

    public function show(int $id)
    {
        try
        {
            $fields = ['*'];
            $specialists = $this->specialistService->getById($id, $fields);

            // ILMU
            //
            // kenapa json dibawah ini menggunakan new SpecialistResource ?
            // karena kalau kita pakai collection seperti di baris 22, itu hanya
            // di peruntukan mengambil semua data,
            // sedangkan kita hanya ingin menampilkan data yang dipilih saja
            return response()->json(new SpecialistResource($specialists));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Specialist not found'
            ], 404);
        }
    }

    // function for manajer
    public function store(SpecialistRequest $request)
    {
        $specialists = $this->specialistService->create($request->validated());
        return response()->json(new SpecialistResource($specialists), 201);
    }

    public function update(SpecialistRequest $request, int $id)
    {
        try
        {
            $specialists = $this->specialistService->update($id, $request->validated());
            return response()->json(new SpecialistResource($specialists), 201);
        } catch (ModelNotFoundException $exception)
        {
            return response()->json([
                'message' => 'Specialist not found'
            ], 404);
        }
    }

    public function destroy(int $id)
    {
        try
        {
            $this->specialistService->delete($id);
            return response()->json([
                'message' => 'Specialist deleted Successfuly',
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Specialist not found'
            ], 404);
        }
    }

}
