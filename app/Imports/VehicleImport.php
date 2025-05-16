<?php

namespace App\Imports;

use App\Models\Vehicle;
use Laravel\Scout\ModelObserver;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class VehicleImport implements ToCollection
{
    public function __construct()
    {
        // Matikan Scout observer di constructor
        ModelObserver::disableSyncingFor(Vehicle::class);
        Log::info('Scout indexing dinonaktifkan untuk proses import');
    }

    public function collection(Collection $rows)
    {
        try {
            // Periksa apakah baris pertama adalah header
            $hasHeader = $this->isHeaderRow($rows->first());
            $startIndex = $hasHeader ? 1 : 0;

            // Hitung total baris yang akan diproses
            $totalRows = $rows->count() - $startIndex;
            Log::info("Memproses {$totalRows} baris data");

            // Proses data baris per baris
            for ($i = $startIndex; $i < $rows->count(); $i++) {
                $row = $rows[$i];

                try {
                    Vehicle::create([
                        'no_kontrak'      => $row[1] ?? null, // Indeks dimulai dari 0
                        'nama_konsumen'   => $row[2] ?? null,
                        'no_polisi'       => $row[3] ?? null,
                        'no_rangka'       => $row[4] ?? null,
                        'no_mesin'        => $row[5] ?? null,
                        'merk_tipe'       => $row[6] ?? null,
                        'past_due'        => $row[7] ?? 0,
                        'nama_resort'     => $row[8] ?? null,
                        'nama_sector'     => $row[9] ?? null,
                        'nama_sub_sector' => $row[10] ?? null,
                        'product'         => $row[11] ?? null,
                    ]);
                } catch (\Exception $e) {
                    Log::warning("Error pada baris #" . ($i + 1) . ": " . $e->getMessage());
                    // Lanjutkan ke baris berikutnya meskipun ada error
                    continue;
                }
            }

            Log::info('Import data selesai');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan fatal saat import: ' . $e->getMessage());
            throw $e; // Re-throw exception untuk ditangani di controller
        }
    }

    /**
     * Periksa apakah baris ini adalah header
     * 
     * @param mixed $row
     * @return bool
     */
    private function isHeaderRow($row)
    {
        // Periksa apakah baris ini kemungkinan header berdasarkan konten
        // Misalnya, jika berisi kata "no_kontrak", "nama_konsumen", dll.
        if (!$row) return false;

        $headerKeywords = ['kontrak', 'konsumen', 'polisi', 'rangka', 'mesin'];

        foreach ($headerKeywords as $keyword) {
            foreach ($row as $cell) {
                if (is_string($cell) && stripos($cell, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function __destruct()
    {
        // Aktifkan kembali Scout observer di destructor
        ModelObserver::enableSyncingFor(Vehicle::class);
        Log::info('Scout indexing diaktifkan kembali setelah import');
    }
}
