<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignResidentRequest;
use App\Http\Requests\UnassignResidentRequest;
use App\Http\Requests\UpdateResidentHistoryRequest;
use App\Models\Resident;
use App\Services\HouseService;
use App\Services\ResidentHouseHistoryService;
use Illuminate\Http\JsonResponse;

class ResidentHouseHistoryController extends Controller
{
    public function __construct(
        private HouseService $houseService,
        private ResidentHouseHistoryService $historyService
    ) {}

    /**
     * POST /houses/{id}/assign
     * Menugaskan penghuni ke rumah (penghuni masuk).
     */
    public function assign(AssignResidentRequest $request, string $id): JsonResponse
    {
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found.',
            ], 404);
        }

        $resident = Resident::find($request->resident_id);

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Resident not found.',
            ], 404);
        }

        $result = $this->historyService->assign($house, $resident, $request->move_in_date);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resident assigned to house successfully.',
            'data'    => $result['data'],
        ], 201);
    }

    /**
     * PUT /houses/{id}/assign
     * Memperbarui data hunian aktif (move_in_date atau mengganti penghuni).
     */
    public function updateAssignment(UpdateResidentHistoryRequest $request, string $id): JsonResponse
    {
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found.',
            ], 404);
        }

        $result = $this->historyService->updateActive(
            $house,
            $request->only(['resident_id', 'move_in_date'])
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resident assignment updated successfully.',
            'data'    => $result['data'],
        ]);
    }

    /**
     * DELETE /houses/{id}/assign
     * Mencabut penghuni aktif dari rumah (penghuni keluar).
     */
    public function unassign(UnassignResidentRequest $request, string $id): JsonResponse
    {
        $house = $this->houseService->findById($id);

        if (!$house) {
            return response()->json([
                'success' => false,
                'message' => 'House not found.',
            ], 404);
        }

        $result = $this->historyService->unassign($house, $request->move_out_date ?? null);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resident unassigned from house successfully.',
            'data'    => $result['data'],
        ]);
    }
}
