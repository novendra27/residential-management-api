<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResidentRequest;
use App\Http\Requests\UpdateResidentRequest;
use App\Services\ResidentService;
use Illuminate\Http\JsonResponse;

class ResidentController extends Controller
{
    public function __construct(private ResidentService $residentService) {}

    public function index(): JsonResponse
    {
        $paginator = $this->residentService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Residents retrieved successfully',
            'data'    => array_map(
                fn ($r) => $this->residentService->formatResident($r),
                $paginator->items()
            ),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $resident = $this->residentService->findById($id);

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Resident not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resident retrieved successfully',
            'data'    => $this->residentService->formatResident($resident),
        ]);
    }

    public function store(StoreResidentRequest $request): JsonResponse
    {
        $resident = $this->residentService->create(
            $request->only(['full_name', 'is_contract', 'phone_number', 'is_married']),
            $request->file('ktp_photo')
        );

        return response()->json([
            'success' => true,
            'message' => 'Resident created successfully',
            'data'    => $this->residentService->formatResident($resident),
        ], 201);
    }

    public function update(UpdateResidentRequest $request, string $id): JsonResponse
    {
        $resident = $this->residentService->findById($id);

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Resident not found',
            ], 404);
        }

        // PHP tidak mem-parsing multipart/form-data untuk PUT secara native.
        // Support dua cara: JSON body langsung, atau POST + _method=PUT dengan multipart.
        $data = $request->only(['full_name', 'is_contract', 'phone_number', 'is_married']);

        $resident = $this->residentService->update(
            $resident,
            array_filter($data, fn ($v) => $v !== null),
            $request->file('ktp_photo')
        );

        return response()->json([
            'success' => true,
            'message' => 'Resident updated successfully',
            'data'    => $this->residentService->formatResident($resident),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $resident = $this->residentService->findById($id);

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Resident not found',
            ], 404);
        }

        $result = $this->residentService->delete($resident);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resident deleted successfully',
        ]);
    }
}
