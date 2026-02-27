<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    // POST /payments
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $result = $this->paymentService->create($request->validated());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat.',
            'data'    => $result['data'],
        ], 201);
    }
}
