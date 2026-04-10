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
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->orderType, fn ($q) => $q->where('type', $this->orderType));

        return $query->selectRaw("
                DATE(created_at) as date,
                SUM(CASE WHEN type = 'delivery' AND status != 'cancelled' THEN 1 ELSE 0 END) as delivery_count,
                SUM(CASE WHEN type = 'pickup' AND status != 'cancelled' THEN 1 ELSE 0 END) as pickup_count,
                SUM(CASE WHEN status != 'cancelled' THEN 1 ELSE 0 END) as order_count,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
                SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as revenue
            ")
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();
    }

    public function headings(): array
    {
        return ['Date', 'Delivery', 'Pickup', 'Total Orders', 'Cancelled', 'Revenue', 'Avg Order Value'];
    }

    /**
     * @param  object  $row
     */
    public function map($row): array
    {
        return [
            Carbon::parse($row->date)->format('M d, Y'),
            $row->delivery_count,
            $row->pickup_count,
            $row->order_count,
            $row->cancelled_count,
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
