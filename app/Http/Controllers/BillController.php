<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayBillRequest;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use App\Services\BillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(private BillService $billService) {}

    // GET /bills
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->billService->getAll($request->only([
            'house_id', 'is_paid', 'fee_type_id', 'month', 'year',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data'    => collect($paginator->items())->map(
                fn (Bill $bill) => $this->billService->formatBillPublic($bill)
            )->values(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    // GET /bills/{id}
    public function show(string $id): JsonResponse
    {
        $bill = $this->billService->getById($id);

        if (!$bill) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tagihan berhasil diambil.',
            'data'    => $this->billService->formatBillPublic($bill),
        ]);
    }

    // POST /bills
    public function store(StoreBillRequest $request): JsonResponse
    {
        $result = $this->billService->create($request->validated());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat.',
            'data'    => $result['data'],
        ], 201);
    }

    // PUT /bills/{id}
    public function update(UpdateBillRequest $request, string $id): JsonResponse
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        $result = $this->billService->update($bill, $request->validated());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil diperbarui.',
            'data'    => $result['data'],
        ]);
    }

    // PATCH /bills/{id}/pay
    public function pay(PayBillRequest $request, string $id): JsonResponse
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        $result = $this->billService->pay($bill, $request->validated());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil ditandai sebagai lunas.',
            'data'    => $result['data'],
        ]);
    }

    // DELETE /bills/{id}
    public function destroy(string $id): JsonResponse
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        $result = $this->billService->delete($bill);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dihapus.',
        ]);
    }
}
