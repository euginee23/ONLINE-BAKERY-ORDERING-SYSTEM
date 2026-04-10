<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?string $orderType = null,
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        $query = Order::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as order_count, SUM(total_amount) as revenue')
            ->whereNotIn('status', ['cancelled']);

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->orderType) {
            $query->where('type', $this->orderType);
        }

        return $query->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();
    }

    public function headings(): array
    {
        return ['Date', 'Total Orders', 'Revenue', 'Avg Order Value'];
    }

    /**
     * @param  object  $row
     */
    public function map($row): array
    {
        return [
            Carbon::parse($row->date)->format('M d, Y'),
            $row->order_count,
            number_format((float) $row->revenue, 2),
            $row->order_count > 0 ? number_format((float) $row->revenue / $row->order_count, 2) : '0.00',
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
        return 'Sales Summary';
    }
}
