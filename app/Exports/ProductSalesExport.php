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

class ProductSalesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private float $grandTotal = 0;

    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?int $categoryId = null,
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        $query = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->whereHas('order', function ($q) {
                $q->whereNotIn('status', ['cancelled']);

                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }

                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            });

        if ($this->categoryId) {
            $query->whereHas('product', fn ($q) => $q->where('category_id', $this->categoryId));
        }

        $result = $query->groupBy('product_id')
            ->orderByRaw('SUM(subtotal) DESC')
            ->with('product.category')
            ->get();

        $this->grandTotal = (float) $result->sum('total_revenue');

        return $result;
    }

    public function headings(): array
    {
        return ['#', 'Product', 'Category', 'Qty Sold', 'Revenue', '% Share', 'Avg Price', 'Stock'];
    }

    /**
     * @param  object  $row
     */
    public function map($row): array
    {
        static $rank = 0;
        $rank++;

        return [
            $rank,
            $row->product->name,
            $row->product->category->name ?? 'N/A',
            $row->total_quantity,
            number_format((float) $row->total_revenue, 2),
            $this->grandTotal > 0 ? number_format((float) $row->total_revenue / $this->grandTotal * 100, 1).'%' : '0.0%',
            $row->total_quantity > 0 ? number_format((float) $row->total_revenue / $row->total_quantity, 2) : '0.00',
            $row->product->stock,
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
        return 'Product Sales';
    }
}
