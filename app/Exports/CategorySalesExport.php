<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategorySalesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private float $grandTotal = 0;

    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        $result = OrderItem::query()
            ->selectRaw('products.category_id, categories.name as category_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_revenue, COUNT(DISTINCT order_items.order_id) as order_count')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('orders.created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('orders.created_at', '<=', $this->dateTo))
            ->groupBy('products.category_id', 'categories.name')
            ->orderByRaw('SUM(order_items.subtotal) DESC')
            ->get();

        $this->grandTotal = (float) $result->sum('total_revenue');

        return $result;
    }

    public function headings(): array
    {
        return ['Category', 'Orders', 'Qty Sold', 'Revenue', '% Share', 'Avg Order Value'];
    }

    /**
     * @param  object  $row
     */
    public function map($row): array
    {
        return [
            $row->category_name,
            $row->order_count,
            $row->total_quantity,
            number_format((float) $row->total_revenue, 2),
            $this->grandTotal > 0 ? number_format((float) $row->total_revenue / $this->grandTotal * 100, 1).'%' : '0.0%',
            $row->order_count > 0 ? number_format((float) $row->total_revenue / $row->order_count, 2) : '0.00',
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
        return 'Category Sales';
    }
}
