<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\SupportTicket;
use App\Models\CustomerFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview statistics
     * → fungsi ini menampilkan ringkasan data dashboard (produk, transaksi, karyawan, feedback)
     */
    public function overview()
    {
        try {
            // Hitung jumlah produk aktif
            $productsCount = Product::active()->count();

            // Hitung jumlah transaksi selesai bulan ini
            $transactionsThisMonth = Transaction::thisMonth()->where('transactions.status', 'completed')->count();

            // Hitung jumlah karyawan aktif
            $employeesCount = Employee::active()->count();

            // Hitung tiket support yang belum ditangani
            $pendingTickets = SupportTicket::pending()->count();

            // Hitung transaksi bulan lalu untuk perbandingan pertumbuhan
            $lastMonthTransactions = Transaction::whereMonth('transaction_date', Carbon::now()->subMonth()->month)
                ->whereYear('transaction_date', Carbon::now()->subMonth()->year)
                ->where('transactions.status', 'completed')
                ->count();
            
            // Hitung persentase pertumbuhan transaksi
            $transactionGrowth = $lastMonthTransactions > 0 
                ? round((($transactionsThisMonth - $lastMonthTransactions) / $lastMonthTransactions) * 100, 1)
                : 0;

            // Hitung total hari kerja bulan ini
            $totalWorkDays = Carbon::now()->daysInMonth;

            // Hitung total kehadiran yang seharusnya
            $totalExpectedAttendance = $employeesCount * $totalWorkDays;

            // Hitung kehadiran aktual yang hadir atau terlambat
            $actualAttendance = Attendance::thisMonth()
                ->whereIn('attendances.status', ['present', 'late'])
                ->count();
            
            // Hitung rasio kehadiran dalam persen
            $attendanceRate = $totalExpectedAttendance > 0 
                ? round(($actualAttendance / $totalExpectedAttendance) * 100, 1)
                : 0;

            // Data ringkasan bisnis
            $businessOverview = [
                'products_count' => $productsCount,
                'transactions_count' => $transactionsThisMonth,
                'growth_rate' => $transactionGrowth,
                'growth_message' => $transactionGrowth > 0 
                    ? 'Penjualan meningkat dibanding bulan lalu' 
                    : 'Penjualan menurun dibanding bulan lalu'
            ];

            // Data ringkasan SDM & kehadiran
            $peopleOverview = [
                'employees_count' => $employeesCount,
                'attendance_rate' => $attendanceRate,
                'attendance_message' => $attendanceRate >= 90 
                    ? 'Kehadiran karyawan stabil bulan ini' 
                    : 'Perlu perhatian pada kehadiran karyawan'
            ];

            // Ambil 3 feedback pelanggan terbaru
            $recentFeedbacks = CustomerFeedback::with(['customer', 'product'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($feedback) {
                    return [
                        'id' => $feedback->id,
                        'customer_name' => $feedback->customer->name ?? 'Anonymous',
                        'comment' => $feedback->comment,
                        'rating' => $feedback->rating,
                        'created_at' => $feedback->created_at->diffForHumans()
                    ];
                });

            // Analisa kategori produk paling laku bulan ini
            $popularCategory = Transaction::whereMonth('transaction_date', date('m'))
                ->whereYear('transaction_date', date('Y'))
                ->where('transactions.status', 'completed')
                ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->select('products.category', DB::raw('SUM(transaction_items.quantity) as total_sold'))
                ->groupBy('products.category')
                ->orderBy('total_sold', 'desc')
                ->first();

            // Insight sederhana berdasarkan kategori paling laku
            $aiInsight = $popularCategory 
                ? "Produk {$popularCategory->category} paling banyak diminati"
                : "Belum ada data penjualan bulan ini";

            // Kirim semua data sebagai response JSON
            return response()->json([
                'success' => true,
                'data' => [
                    'quick_stats' => [
                        'products' => [
                            'count' => $productsCount,
                            'growth' => '+12%'
                        ],
                        'transactions' => [
                            'count' => $transactionsThisMonth,
                            'growth' => $transactionGrowth > 0 ? "+{$transactionGrowth}%" : "{$transactionGrowth}%"
                        ],
                        'employees' => [
                            'count' => $employeesCount,
                            'growth' => '0%'
                        ],
                        'pending_tickets' => [
                            'count' => $pendingTickets,
                            'status' => $pendingTickets > 10 ? 'Urgent' : 'Normal'
                        ]
                    ],
                    'business_overview' => $businessOverview,
                    'people_overview' => $peopleOverview,
                    'support_feedback' => [
                        'pending_tickets' => $pendingTickets,
                        'recent_feedbacks' => $recentFeedbacks,
                        'ai_insight' => $aiInsight
                    ],
                    // Tampilkan tanggal sekarang format Indonesia
                    'current_date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY')
                ]
            ]);
        } catch (\Exception $e) {
            // Kalau terjadi error, kirim pesan error
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

        /**
     * Get sales chart data
     * → fungsi ini buat grafik penjualan (default 6 bulan terakhir)
     */
    public function salesChart(Request $request)
    {
        try {
            // Ambil periode chart (default bulanan)
            $period = $request->input('period', 'monthly');
            
            $chartData = [];
            
            if ($period === 'monthly') {
                // Loop 6 bulan terakhir
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);

                    // Total penjualan bulan itu (uang masuk)
                    $sales = Transaction::whereMonth('transaction_date', $date->month)
                        ->whereYear('transaction_date', $date->year)
                        ->where('status', 'completed')
                        ->sum('final_amount');
                    
                    $chartData[] = [
                        // Nama periode seperti "Jan 2025"
                        'period' => $date->locale('id')->format('M Y'),

                        // Total revenue bulan itu
                        'sales' => (float) $sales,

                        // Jumlah transaksi selesai
                        'transactions' => Transaction::whereMonth('transaction_date', $date->month)
                            ->whereYear('transaction_date', $date->year)
                            ->where('status', 'completed')
                            ->count()
                    ];
                }
            }

            // Kirim data chart ke frontend
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            // Error handling
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data chart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get low stock alerts
     * → fungsi ini cek produk yang hampir habis (stok rendah)
     */
    public function lowStockAlerts()
    {
        try {
            // Ambil produk dengan stok di bawah batas (misal < 10)
            $lowStockProducts = Product::active()
                ->lowStock(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'stock' => $product->stock,
                        'category' => $product->category
                    ];
                });

            // Kirim data produk rendah stok
            return response()->json([
                'success' => true,
                'data' => $lowStockProducts,
                'count' => $lowStockProducts->count()
            ]);
        } catch (\Exception $e) {
            // Error handling
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data stok rendah',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get financial summary
     * → Ringkasan keuangan: total pemasukan, transaksi, avg, top produk
     */
    public function financialSummary(Request $request)
    {
        try {
            // Ambil bulan & tahun (default sekarang)
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            // Transaksi selesai periode ini
            $transactions = Transaction::whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->where('status', 'completed');

            // Total revenue bulan ini
            $totalRevenue = $transactions->sum('final_amount');

            // Total transaksi selesai
            $totalTransactions = $transactions->count();

            // Rata2 nilai transaksi
            $averageTransaction = $totalTransactions > 0 
                ? $totalRevenue / $totalTransactions 
                : 0;

            // Ambil produk terlaris
            $topProducts = Transaction::whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->where('transactions.status', 'completed')
                ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('SUM(transaction_items.quantity) as total_sold'),
                    DB::raw('SUM(transaction_items.subtotal) as total_revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_sold', 'desc')
                ->limit(5)
                ->get();

            // Kirim ringkasan
            return response()->json([
                'success' => true,
                'data' => [
                    'total_revenue' => (float) $totalRevenue,
                    'total_transactions' => $totalTransactions,
                    'average_transaction' => (float) $averageTransaction,
                    'top_products' => $topProducts,
                    'period' => [
                        'month' => $month,
                        'year' => $year
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            // Error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan keuangan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
