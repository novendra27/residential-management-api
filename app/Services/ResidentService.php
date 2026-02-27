<?php

namespace App\Services;

use App\Models\Resident;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ResidentService
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Resident::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(string $id): ?Resident
    {
        return Resident::find($id);
    }

    public function create(array $data, ?UploadedFile $ktpPhoto = null): Resident
    {
        if ($ktpPhoto) {
            $data['ktp_photo'] = $ktpPhoto->store('ktp', 'public');
        }

        return Resident::create($data)->fresh();
    }

    public function update(Resident $resident, array $data, ?UploadedFile $ktpPhoto = null): Resident
    {
        if ($ktpPhoto) {
            // Hapus foto lama jika ada
            if ($resident->ktp_photo) {
                Storage::disk('public')->delete($resident->ktp_photo);
            }
            $data['ktp_photo'] = $ktpPhoto->store('ktp', 'public');
        }

        $resident->update($data);
        return $resident->fresh();
    }

    public function delete(Resident $resident): array
    {
        // Cegah hapus jika masih penghuni aktif di suatu rumah
        $hasActiveHistory = $resident->houseHistories()
            ->where('is_active', true)
            ->exists();

        if ($hasActiveHistory) {
            return [
                'success' => false,
                'message' => 'Penghuni tidak dapat dihapus karena masih terdaftar sebagai penghuni aktif di suatu rumah.',
            ];
        }

        // Cegah hapus jika masih punya tagihan yang belum lunas
        $hasUnpaidBills = $resident->bills()
            ->where('is_paid', false)
            ->exists();

        if ($hasUnpaidBills) {
            return [
                'success' => false,
                'message' => 'Penghuni tidak dapat dihapus karena masih memiliki tagihan yang belum lunas.',
            ];
        }

        if ($resident->ktp_photo) {
            Storage::disk('public')->delete($resident->ktp_photo);
        }

        $resident->delete();

        return ['success' => true];
    }

    public function formatResident(Resident $resident): array
    {
        return [
            'id'           => $resident->id,
            'full_name'    => $resident->full_name,
            'ktp_photo'    => $resident->ktp_photo
                                ? url('storage/' . $resident->ktp_photo)
                                : null,
            'is_contract'  => $resident->is_contract,
            'phone_number' => $resident->phone_number,
            'is_married'   => $resident->is_married,
            'created_at'   => $resident->created_at?->toDateTimeString(),
        ];
    }
}
