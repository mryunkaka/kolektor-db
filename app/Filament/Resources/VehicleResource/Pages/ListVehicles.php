<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Imports\VehicleImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VehicleResource;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-document-arrow-up')
                ->form([
                    FileUpload::make('file')
                        ->label('Pilih File Excel')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
                            'application/vnd.ms-excel', // xls
                            'application/vnd.oasis.opendocument.spreadsheet', // ods
                            'text/csv', // csv
                            '.xlsx',
                            '.xls',
                            '.csv',
                            '.ods',
                        ])
                        ->maxSize(5120) // 5 MB
                        ->disk('local')
                        ->directory('imports/vehicles')
                        ->preserveFilenames()
                        ->visibility('private')
                        ->required(),
                ])
                ->modalWidth('md')
                ->modalHeading('Impor Data Kendaraan')
                ->modalButton('Import')
                ->action(function (array $data): void {
                    logger()->info('Mulai proses import kendaraan.', ['data' => $data]);

                    if (!isset($data['file']) || empty($data['file'])) {
                        logger()->error('File tidak tersedia atau kosong.');
                        return;
                    }

                    try {
                        $filePath = Storage::disk('local')->path($data['file']);
                        logger()->info('Path file terdeteksi.', ['filePath' => $filePath]);

                        if (!file_exists($filePath)) {
                            logger()->error('File tidak ditemukan di path yang diberikan.', ['filePath' => $filePath]);
                            return;
                        }

                        logger()->info('Memulai proses import dengan Maatwebsite Excel.', ['filePath' => $filePath]);
                        Excel::import(new VehicleImport, $filePath);
                        logger()->info('Proses import selesai.');

                        Notification::make()
                            ->title('Impor Berhasil')
                            ->body('Data kendaraan berhasil diimpor.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        logger()->error('Terjadi kesalahan saat import kendaraan.', [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        Notification::make()
                            ->title('Gagal Impor')
                            ->body('Terjadi kesalahan saat mengimpor data kendaraan.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
    /**
     * Override the getTableQuery method to customize the query
     * for the table in the ListRecords page.
     *
     * @return Builder
     */
    protected function getTableQuery(): Builder
    {
        // Cek apakah ada input pencarian
        $search = $this->getTableSearch();

        // Jika tidak ada pencarian, kembalikan query kosong
        if (blank($search)) {
            return VehicleResource::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Jika ada pencarian, biarkan query berjalan normal
        return VehicleResource::getEloquentQuery();
    }
}
