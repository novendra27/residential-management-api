<?php

namespace App\Services;

use App\Models\House;
use App\Models\Resident;
use App\Models\ResidentHouseHistory;
use Illuminate\Support\Carbon;

class ResidentHouseHistoryService
{
    /**
     * Assign a resident to a house (move-in).
     *
     * Rules:
     * - House must not currently be occupied.
     * - Resident must not currently be an active resident in any other house.
     */
    public function assign(House $house, Resident $resident, string $moveInDate): array
    {
        // 1. Pastikan rumah belum dihuni
        if ($house->is_occupied) {
            return [
                'success' => false,
                'message' => 'Rumah sudah dihuni. Keluarkan penghuni aktif terlebih dahulu sebelum menambahkan penghuni baru.',
                'status'  => 422,
            ];
        }

        // 2. Pastikan penghuni tidak sedang aktif di rumah lain
        $alreadyActive = ResidentHouseHistory::where('resident_id', $resident->id)
            ->where('is_active', true)
            ->first();

        if ($alreadyActive) {
            return [
                'success' => false,
                'message' => 'Penghuni ini sudah terdaftar sebagai penghuni aktif di rumah lain dan tidak dapat ditugaskan.',
                'status'  => 422,
            ];
        }

        // 3. Buat history baru
        $history = ResidentHouseHistory::create([
            'resident_id'  => $resident->id,
            'house_id'     => $house->id,
            'move_in_date' => $moveInDate,
            'is_active'    => true,
            'created_at'   => now(),
        ]);

        // 4. Tandai rumah sebagai dihuni
        $house->update(['is_occupied' => true]);

        return [
            'success' => true,
            'data'    => $this->formatHistory($history->load('resident', 'house')),
        ];
    }

    /**
     * Update the active history record of a house.
     * Supports updating move_in_date and/or swapping the resident.
     */
    public function updateActive(House $house, array $data): array
    {
        $history = ResidentHouseHistory::where('house_id', $house->id)
            ->where('is_active', true)
            ->first();

        if (!$history) {
            return [
                'success' => false,
                'message' => 'Tidak ada penghuni aktif di rumah ini yang dapat diperbarui.',
                'status'  => 422,
            ];
        }

        $history->update(array_filter($data, fn ($v) => $v !== null));

        return [
            'success' => true,
            'data'    => $this->formatHistory($history->fresh()->load('resident', 'house')),
        ];
    }

    /**
     * Unassign the active resident from a house (move-out).
     */
    public function unassign(House $house, ?string $moveOutDate = null): array
    {
        $history = ResidentHouseHistory::where('house_id', $house->id)
            ->where('is_active', true)
            ->first();

        if (!$history) {
            return [
                'success' => false,
                'message' => 'Tidak ada penghuni aktif di rumah ini.',
                'status'  => 422,
            ];
        }

        // Tandai history sebagai tidak aktif
        $history->update([
            'is_active'     => false,
            'move_out_date' => $moveOutDate ?? Carbon::today()->toDateString(),
        ]);

        // Tandai rumah sebagai kosong
        $house->update(['is_occupied' => false]);

        return [
            'success' => true,
            'data'    => $this->formatHistory($history->fresh()->load('resident', 'house')),
        ];
    }

    public function formatHistory(ResidentHouseHistory $history): array
    {
        return [
            'id'            => $history->id,
            'move_in_date'  => $history->move_in_date?->toDateString(),
            'move_out_date' => $history->move_out_date?->toDateString(),
            'is_active'     => $history->is_active,
            'created_at'    => $history->created_at?->toDateTimeString(),
            'resident'      => $history->resident ? [
                'id'           => $history->resident->id,
                'full_name'    => $history->resident->full_name,
                'phone_number' => $history->resident->phone_number,
                'is_contract'  => $history->resident->is_contract,
                'is_married'   => $history->resident->is_married,
            ] : null,
            'house'         => $history->house ? [
                'id'           => $history->house->id,
                'house_number' => $history->house->house_number,
                'address'      => $history->house->address,
            ] : null,
        ];
    }
}
