<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DailySalesReportNotification;

class SendDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:report-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily sales report to admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $reportDate = Carbon::today();

        $this->info("Generating daily sales report for {$reportDate->format('Y-m-d')}...");

        $completedCarts = Cart::query()
            ->where('status', Cart::COMPLETED)
            ->whereDate('updated_at', $reportDate)
            ->with(['items.product'])
            ->get();

        $salesData      = [];
        $totalItemsSold = 0;
        $totalRevenue   = 0;
        $totalOrders    = $completedCarts->count();

        if (! $completedCarts->isEmpty()) {
            foreach ($completedCarts as $cart) {
                foreach ($cart->items as $item) {
                    $productId = $item->product_id;
                    $product   = $item->product;

                    if (! isset($salesData[$productId])) {
                        $salesData[$productId] = [
                            'product_name'  => $product->name,
                            'quantity_sold' => 0,
                            'unit_price'    => $product->price,
                            'total_revenue' => 0,
                        ];
                    }

                    $salesData[$productId]['quantity_sold'] += $item->quantity;
                    $itemRevenue = $item->quantity * $product->price;
                    $salesData[$productId]['total_revenue'] += $itemRevenue;

                    $totalItemsSold += $item->quantity;
                    $totalRevenue   += $itemRevenue;
                }
            }
        }

        $summary = [
            'total_orders'     => $totalOrders,
            'total_items_sold' => $totalItemsSold,
            'total_revenue'    => $totalRevenue,
        ];

        // Generate CSV only when there are sales
        $csvPath = ! empty($salesData) ? $this->generateCsv($salesData, $reportDate) : null;

        $admin = User::where('is_admin', true)->first();

        if (! $admin) {
            $this->error('No admin user found. Cannot send report.');
            if ($csvPath) {
                File::delete($csvPath);
            }

            return Command::FAILURE;
        }

        try {
            Notification::send($admin, new DailySalesReportNotification(
                array_values($salesData),
                $summary,
                $csvPath,
                $reportDate,
            ));

            $message = empty($salesData)
                ? 'Daily sales report sent successfully (no sales today).'
                : 'Daily sales report sent successfully!';
            $this->info($message);
        } catch (\Exception $e) {
            $this->error("Failed to send report: {$e->getMessage()}");
            if ($csvPath) {
                File::delete($csvPath);
            }

            return Command::FAILURE;
        }

        if ($csvPath) {
            File::delete($csvPath);
        }

        return Command::SUCCESS;
    }

    /**
     * Generate CSV file from sales data.
     */
    private function generateCsv(array $salesData, Carbon $reportDate): string
    {
        $tempDir = storage_path('app/temp');
        if (! File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $filename = "daily-sales-report-{$reportDate->format('Y-m-d')}.csv";
        $filePath = $tempDir . '/' . $filename;

        $file = fopen($filePath, 'w');

        fputcsv($file, ['Product Name', 'Quantity Sold', 'Unit Price', 'Total Revenue']);

        foreach ($salesData as $data) {
            fputcsv($file, [
                $data['product_name'],
                $data['quantity_sold'],
                number_format($data['unit_price'], 2),
                number_format($data['total_revenue'], 2),
            ]);
        }

        fclose($file);

        return $filePath;
    }
}
