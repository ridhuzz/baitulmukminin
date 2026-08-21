<?php

namespace App\Filament\Resources\TransaksiKeuanganResource\Pages;

use App\Filament\Resources\TransaksiKeuanganResource;
use App\Filament\Widgets\RingkasanKeuanganWidget;
use App\Models\TransaksiKeuangan;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTransaksiKeuangan extends ListRecords
{
    protected static string $resource = TransaksiKeuanganResource::class;

    protected static ?string $title = 'Manajemen Keuangan';

    public function getSubheading(): ?string
    {
        return 'Pencatatan kas masuk, keluar, dan laporan rekening.';
    }

    protected function getHeaderWidgets(): array
    {
        return [RingkasanKeuanganWidget::class];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Transaksi Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'pemasukan' => Tab::make('Pemasukan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis_transaksi', 'pemasukan')),
            'pengeluaran' => Tab::make('Pengeluaran')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis_transaksi', 'pengeluaran')),
            'menunggu' => Tab::make('Menunggu Approval')
                ->badge(TransaksiKeuangan::where('status_approval', 'menunggu')->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_approval', 'menunggu')),
        ];
    }
}
