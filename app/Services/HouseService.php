<?php

namespace App\Services;

use App\Models\House;
use Illuminate\Pagination\LengthAwarePaginator;

class HouseService
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return House::orderBy('house_number')->paginate($perPage);
    }

    public function findById(string $id): ?House
    {
        return House::find($id);
    }

    public function create(array $data): House
    {
        return House::create($data)->fresh();
    }

    public function update(House $house, array $data): House
    {
        $house->update($data);
        return $house->fresh();
    }

    public function delete(House $house): array
    {
        // Cegah hapus jika rumah masih dihuni
        if ($house->is_occupied) {
            return [
                'success' => false,
                'message' => 'Rumah tidak dapat dihapus karena masih dihuni. Pastikan penghuni sudah pindah terlebih dahulu.',
            ];
        }

        // Cegah hapus jika masih ada tagihan (lunas maupun belum) untuk menjaga rekam keuangan
        $hasBills = $house->bills()->exists();

        if ($hasBills) {
            return [
                'success' => false,
                'message' => 'Rumah tidak dapat dihapus karena memiliki riwayat tagihan. Data keuangan harus tetap terjaga.',
            ];
        }

        $house->delete();

        return ['success' => true];
    }

    public function getResidentHistories(House $house, int $perPage = 15): LengthAwarePaginator
    {
        return $house->houseHistories()
            ->with('resident')
            ->orderBy('move_in_date', 'desc')
            ->paginate($perPage);
    }

    public function getPaymentHistories(House $house, int $perPage = 15): LengthAwarePaginator
    {
        return $house->bills()
            ->with(['resident', 'feeType', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function formatHouse(House $house, bool $withCurrentResident = false): array
    {
        $data = [
            'id'           => $house->id,
            'house_number' => $house->house_number,
            'address'      => $house->address,
            'is_occupied'  => $house->is_occupied,
            'created_at'   => $house->created_at?->toDateTimeString(),
        ];

        if ($withCurrentResident) {
            $current = $house->houseHistories()
                ->with('resident')
                ->where('is_active', true)
                ->first();

            $data['current_resident'] = $current ? [
                'history_id'   => $current->id,
                'move_in_date' => $current->move_in_date?->toDateString(),
                'resident'     => [
                    'id'           => $current->resident->id,
                    'full_name'    => $current->resident->full_name,
                    'phone_number' => $current->resident->phone_number,
                    'is_contract'  => $current->resident->is_contract,
                    'is_married'   => $current->resident->is_married,
                ],
            ] : null;
        }

        return $data;
    }

    public function formatResidentHistory(object $history): array
    {
        return [
            'id'            => $history->id,
            'move_in_date'  => $history->move_in_date?->toDateString(),
            'move_out_date' => $history->move_out_date?->toDateString(),
            'is_active'     => $history->is_active,
            'resident'      => $history->resident ? [
                'id'        => $history->resident->id,
                'full_name' => $history->resident->full_name,
            ] : null,
        ];
    }

    public function formatPaymentHistory(object $bill): array
    {
        $payment = $bill->payments->first();

        return [
            'bill_id'      => $bill->id,
            'fee_type'     => $bill->feeType?->fee_name,
            'period_start' => $bill->period_start?->toDateString(),
            'period_end'   => $bill->period_end?->toDateString(),
            'total_amount' => (int) $bill->total_amount,
            'is_paid'      => $bill->is_paid,
            'payment_date' => $payment?->payment_date?->toDateString(),
            'resident'     => $bill->resident ? [
                'id'        => $bill->resident->id,
                'full_name' => $bill->resident->full_name,
            ] : null,
        ];
    }
}
