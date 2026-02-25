<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    // GET /report/summary?year=2025
    public function summary(Request $request): JsonResponse
    {
        $year = $request->query('year');

        if (!$year || !is_numeric($year) || (int) $year < 2000) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter year wajib diisi dan harus berupa tahun yang valid (contoh: 2025).',
            ], 422);
        }

        $data = $this->reportService->summary((int) $year);

        return response()->json([
            'success' => true,
            'message' => "Rekap keuangan tahun $year berhasil diambil.",
            'data'    => $data,
        ]);
    }

    // GET /report/balances?month=1&year=2025
    public function balances(Request $request): JsonResponse
    {
        $month = $request->query('month');
        $year  = $request->query('year');

        $errors = [];
        if (!$month || !is_numeric($month) || (int) $month < 1 || (int) $month > 12) {
            $errors['month'] = ['Parameter month wajib diisi (1–12).'];
        }
        if (!$year || !is_numeric($year) || (int) $year < 2000) {
            $errors['year'] = ['Parameter year wajib diisi dan harus berupa tahun yang valid.'];
        }
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $errors,
            ], 422);
        }

        $data = $this->reportService->balances((int) $month, (int) $year);

        return response()->json([
            'success' => true,
            'message' => "Detail keuangan bulan $month/$year berhasil diambil.",
            'data'    => $data,
        ]);
    }
}
