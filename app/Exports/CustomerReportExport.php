<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        return User::query()
            ->where('role', 'customer')
            ->withCount(['orders as total_orders' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            }])
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            }], 'total_amount')
            ->whereHas('orders', function ($q) {
                $q->whereNotIn('status', ['cancelled']);
                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            })
            ->orderByDesc('total_spent')
            ->get();
    }

    public function headings(): array
    {
        return ['Customer', 'Email', 'Total Orders', 'Total Spent', 'Avg Order Value', 'Member Since'];
    }

    /**
     * @param  User  $user
     */
    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->total_orders,
            number_format((float) $user->total_spent, 2),
            $user->total_orders > 0 ? number_format((float) $user->total_spent / $user->total_orders, 2) : '0.00',
            $user->created_at->format('M d, Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Customer Report';
    }
}
