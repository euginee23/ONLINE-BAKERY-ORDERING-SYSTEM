<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?string $status = null,
        public ?string $orderType = null,
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        $query = Order::with(['user', 'items.product'])->latest();

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->orderType) {
            $query->where('type', $this->orderType);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Order #', 'Date', 'Customer', 'Email', 'Type', 'Status', 'Items', 'Total'];
    }

    /**
     * @param  Order  $order
     */
    public function map($order): array
    {
        return [
            $order->id,
            $order->created_at->format('M d, Y h:i A'),
            $order->user->name,
            $order->user->email,
            $order->type->label(),
            $order->status->label(),
            $order->items->count(),
            number_format((float) $order->total_amount, 2),
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
        return 'Orders Report';
    }
}
