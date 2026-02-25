<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHouseRequest;
use App\Http\Requests\UpdateHouseRequest;
use App\Services\HouseService;
use Illuminate\Http\JsonResponse;

class HouseController extends Controller
{
    public function __construct(private HouseService $houseService) {}

    public function index(): JsonResponse
    {
        $paginator = $this->houseService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Houses retrieved successfully',
            'data'    => array_map(
                fn ($h) => $this->houseService->formatHouse($h),
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
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'House retrieved successfully',
            'data'    => $this->houseService->formatHouse($house, withCurrentResident: true),
        ]);
    }

    public function residentHistories(string $id): JsonResponse
    {
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found',
            ], 404);
        }

        $paginator = $this->houseService->getResidentHistories($house);

        return response()->json([
            'success' => true,
            'message' => 'Resident histories retrieved successfully',
            'data'    => array_map(
                fn ($h) => $this->houseService->formatResidentHistory($h),
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

    public function paymentHistories(string $id): JsonResponse
    {
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found',
            ], 404);
        }

        $paginator = $this->houseService->getPaymentHistories($house);

        return response()->json([
            'success' => true,
            'message' => 'Payment histories retrieved successfully',
            'data'    => array_map(
                fn ($b) => $this->houseService->formatPaymentHistory($b),
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

    public function store(StoreHouseRequest $request): JsonResponse
    {
        $house = $this->houseService->create(
            $request->only(['house_number', 'address', 'is_occupied'])
        );

        return response()->json([
            'success' => true,
            'message' => 'House created successfully',
            'data'    => $this->houseService->formatHouse($house),
        ], 201);
    }

    public function update(UpdateHouseRequest $request, string $id): JsonResponse
    {
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found',
            ], 404);
        }

        $house = $this->houseService->update(
            $house,
            $request->only(['house_number', 'address', 'is_occupied'])
        );

        return response()->json([
            'success' => true,
            'message' => 'House updated successfully',
            'data'    => $this->houseService->formatHouse($house),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found',
            ], 404);
        }

        $result = $this->houseService->delete($house);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'House deleted successfully',
        ]);
    }
}
