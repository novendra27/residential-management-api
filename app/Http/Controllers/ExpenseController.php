<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $expenseService) {}

    // GET /expenses
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->expenseService->getAll($request->only(['month', 'year', 'is_monthly']));

        return response()->json([
            'success' => true,
            'message' => 'Data pengeluaran berhasil diambil.',
            'data'    => collect($paginator->items())
                ->map(fn (Expense $e) => $this->expenseService->formatExpensePublic($e))
                ->values(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    // GET /expenses/{id}
    public function show(string $id): JsonResponse
    {
        $expense = $this->expenseService->getById($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pengeluaran berhasil diambil.',
            'data'    => $this->expenseService->formatExpensePublic($expense),
        ]);
    }

    // POST /expenses
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $result = $this->expenseService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil ditambahkan.',
            'data'    => $result['data'],
        ], 201);
    }

    // PUT /expenses/{id}
    public function update(UpdateExpenseRequest $request, string $id): JsonResponse
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan.',
            ], 404);
        }

        $result = $this->expenseService->update($expense, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diperbarui.',
            'data'    => $result['data'],
        ]);
    }

    // DELETE /expenses/{id}
    public function destroy(string $id): JsonResponse
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan.',
            ], 404);
        }

        $this->expenseService->delete($expense);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus.',
        ]);
    }
}
