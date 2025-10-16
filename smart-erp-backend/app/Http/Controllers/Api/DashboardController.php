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
     */
    public function overview()
    {
        try {
            // Quick Stats
            $productsCount = Product::active()->count();
            $transactionsThisMonth = Transaction::thisMonth()->where('transactions.status', 'completed')->count();
            $employeesCount = Employee::active()->count();
            $pendingTickets = SupportTicket::pending()->count();

            // Growth calculations (compare with last month)
            $lastMonthTransactions = Transaction::whereMonth('transaction_date', Carbon::now()->subMonth()->month)
                ->whereYear('transaction_date', Carbon::now()->subMonth()->year)
                ->where('transactions.status', 'completed')
                ->count();
            
            $transactionGrowth = $lastMonthTransactions > 0 
                ? round((($transactionsThisMonth - $lastMonthTransactions) / $lastMonthTransactions) * 100, 1)
                : 0;

            // Attendance Rate
            $totalWorkDays = Carbon::now()->daysInMonth;
            $totalExpectedAttendance = $employeesCount * $totalWorkDays;
            $actualAttendance = Attendance::thisMonth()
                ->whereIn('attendances.status', ['present', 'late'])
                ->count();
            
            $attendanceRate = $totalExpectedAttendance > 0 
                ? round(($actualAttendance / $totalExpectedAttendance) * 100, 1)
                : 0;

            // Business Overview
            $businessOverview = [
                'products_count' => $productsCount,
                'transactions_count' => $transactionsThisMonth,
                'growth_rate' => $transactionGrowth,
                'growth_message' => $transactionGrowth > 0 
                    ? 'Penjualan meningkat dibanding bulan lalu' 
                    : 'Penjualan menurun dibanding bulan lalu'
            ];

            // People Overview
            $peopleOverview = [
                'employees_count' => $employeesCount,
                'attendance_rate' => $attendanceRate,
                'attendance_message' => $attendanceRate >= 90 
                    ? 'Kehadiran karyawan stabil bulan ini' 
                    : 'Perlu perhatian pada kehadiran karyawan'
            ];

            // Recent Feedbacks
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

            // AI Insights - Most Popular Product Category
            $popularCategory = Transaction::whereMonth('transaction_date', date('m'))
                ->whereYear('transaction_date', date('Y'))
                ->where('transactions.status', 'completed')
                ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->select('products.category', DB::raw('SUM(transaction_items.quantity) as total_sold'))
                ->groupBy('products.category')
                ->orderBy('total_sold', 'desc')
                ->first();

            $aiInsight = $popularCategory 
                ? "Produk {$popularCategory->category} paling banyak diminati"
                : "Belum ada data penjualan bulan ini";

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
                    'current_date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales chart data
     */
    public function salesChart(Request $request)
    {
        try {
            $period = $request->input('period', 'monthly');
            
            $chartData = [];
            
            if ($period === 'monthly') {
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $sales = Transaction::whereMonth('transaction_date', $date->month)
                        ->whereYear('transaction_date', $date->year)
                        ->where('status', 'completed')
                        ->sum('final_amount');
                    
                    $chartData[] = [
                        'period' => $date->locale('id')->format('M Y'),
                        'sales' => (float) $sales,
                        'transactions' => Transaction::whereMonth('transaction_date', $date->month)
                            ->whereYear('transaction_date', $date->year)
                            ->where('status', 'completed')
                            ->count()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data chart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        try {
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

            return response()->json([
                'success' => true,
                'data' => $lowStockProducts,
                'count' => $lowStockProducts->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data stok rendah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get financial summary
     */
    public function financialSummary(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $transactions = Transaction::whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->where('status', 'completed');

            $totalRevenue = $transactions->sum('final_amount');
            $totalTransactions = $transactions->count();
            $averageTransaction = $totalTransactions > 0 
                ? $totalRevenue / $totalTransactions 
                : 0;

            // Top selling products
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
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan keuangan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}