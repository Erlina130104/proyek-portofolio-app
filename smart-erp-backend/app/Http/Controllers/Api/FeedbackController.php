<?php

namespace App\Http\Controllers\Api;

// Import class-class yang diperlukan
use App\Http\Controllers\Controller; // Base controller dari Laravel
use App\Models\CustomerFeedback; // Model CustomerFeedback untuk interaksi dengan database
use Illuminate\Http\Request; // Class untuk handle HTTP request
use Illuminate\Support\Facades\Validator; // Facade untuk validasi data
use Illuminate\Support\Facades\DB; // Facade untuk query database mentah
use Illuminate\Support\Facades\Log; // Facade untuk logging

class CustomerFeedbackController extends Controller
{
    /**
     * METHOD: index()
     * FUNGSI: Menampilkan daftar feedback dengan fitur filtering, sorting, dan pagination
     * ENDPOINT: GET /api/feedback
     */
    public function index(Request $request)
    {
        try {
            // Membuat query builder dari model CustomerFeedback
            $query = CustomerFeedback::query();

            // FITUR PENCARIAN
            // Mengecek apakah parameter 'search' ada dan tidak kosong
            if ($request->filled('search')) {
                $search = $request->search; // Ambil nilai search dari request
                
                // Tambahkan kondisi WHERE dengan LIKE untuk mencari di beberapa kolom
                $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'LIKE', "%{$search}%") // Cari di nama customer
                      ->orWhere('message', 'LIKE', "%{$search}%")     // ATAU cari di pesan
                      ->orWhere('email', 'LIKE', "%{$search}%");      // ATAU cari di email
                });
            }

            // FILTER BERDASARKAN SENTIMENT
            // Jika parameter 'sentiment' ada (positive/neutral/negative)
            if ($request->filled('sentiment')) {
                $query->where('sentiment', $request->sentiment);
            }

            // FILTER BERDASARKAN RATING
            // Jika parameter 'rating' ada (1-5)
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            // FILTER BERDASARKAN STATUS
            // Jika parameter 'status' ada (pending/reviewed/resolved/closed)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // SORTING
            // Ambil parameter sort_by, default 'created_at' jika tidak ada
            $sortBy = $request->get('sort_by', 'created_at');
            // Ambil parameter sort_order, default 'desc' (descending) jika tidak ada
            $sortOrder = $request->get('sort_order', 'desc');
            // Terapkan sorting ke query
            $query->orderBy($sortBy, $sortOrder);

            // PAGINATION
            // Ambil jumlah data per halaman, default 12
            $perPage = $request->get('per_page', 12);
            
            // Jika per_page adalah 'all' atau lebih dari 9999, ambil semua data
            if ($perPage == 'all' || $perPage > 9999) {
                $feedbacks = $query->get(); // Ambil semua data tanpa pagination
                
                // Return response JSON dengan semua data
                return response()->json([
                    'success' => true,
                    'message' => 'Feedback retrieved successfully',
                    'data' => $feedbacks
                ], 200);
            }

            // Jika tidak, lakukan pagination dengan jumlah per halaman sesuai parameter
            $feedbacks = $query->paginate($perPage);

            // Return response JSON dengan data pagination
            return response()->json([
                'success' => true,
                'message' => 'Feedback retrieved successfully',
                'data' => [
                    'data' => $feedbacks->items(),           // Array data feedback
                    'total' => $feedbacks->total(),          // Total semua data
                    'per_page' => $feedbacks->perPage(),     // Jumlah per halaman
                    'current_page' => $feedbacks->currentPage(), // Halaman saat ini
                    'last_page' => $feedbacks->lastPage(),   // Halaman terakhir
                    'from' => $feedbacks->firstItem(),       // Index item pertama
                    'to' => $feedbacks->lastItem(),          // Index item terakhir
                ]
            ], 200);

        } catch (\Exception $e) {
            // Jika terjadi error, log error tersebut
            Log::error('Error fetching feedbacks: ' . $e->getMessage());
            
            // Return response error
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: store()
     * FUNGSI: Menyimpan feedback baru ke database
     * ENDPOINT: POST /api/feedback
     */
    public function store(Request $request)
    {
        try {
            // Log semua data yang diterima untuk debugging
            Log::info('Feedback store request received:', $request->all());

            // VALIDASI DATA
            // Membuat validator dengan aturan validasi
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',    // Wajib, string, max 255 karakter
                'message' => 'required|string|min:5',            // Wajib, string, min 5 karakter
                'rating' => 'required|integer|min:1|max:5',      // Wajib, integer 1-5
                'sentiment' => 'required|in:positive,neutral,negative', // Wajib, hanya 3 nilai ini
                'email' => 'nullable|email|max:255',             // Optional, harus format email
                'phone' => 'nullable|string|max:20',             // Optional, string max 20 karakter
            ], [
                // Custom error messages untuk setiap validasi
                'customer_name.required' => 'Customer name is required',
                'message.required' => 'Feedback message is required',
                'message.min' => 'Feedback message must be at least 5 characters',
                'rating.required' => 'Rating is required',
                'rating.min' => 'Rating must be between 1 and 5',
                'rating.max' => 'Rating must be between 1 and 5',
                'sentiment.required' => 'Sentiment is required',
                'sentiment.in' => 'Sentiment must be positive, neutral, or negative',
                'email.email' => 'Please provide a valid email address'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                // Log error validasi
                Log::warning('Validation failed:', $validator->errors()->toArray());
                
                // Return response error dengan detail validasi
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors() // Berisi detail error per field
                ], 422); // HTTP 422: Unprocessable Entity
            }

            // SIMPAN DATA KE DATABASE
            // Create record baru di tabel customer_feedbacks
            $feedback = CustomerFeedback::create([
                'customer_name' => $request->customer_name,
                'message' => $request->message,
                'rating' => (int) $request->rating, // Cast ke integer
                'sentiment' => $request->sentiment,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'pending' // Default status adalah pending
            ]);

            // Log sukses dengan ID feedback yang baru dibuat
            Log::info('Feedback created successfully:', ['id' => $feedback->id]);

            // Return response sukses dengan data feedback yang baru dibuat
            return response()->json([
                'success' => true,
                'message' => 'Feedback created successfully',
                'data' => $feedback
            ], 201); // HTTP 201: Created

        } catch (\Exception $e) {
            // Log error dengan pesan dan stack trace untuk debugging
            Log::error('Error creating feedback: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return response error
            return response()->json([
                'success' => false,
                'message' => 'Failed to create feedback',
                'error' => $e->getMessage()
            ], 500); // HTTP 500: Internal Server Error
        }
    }

    /**
     * METHOD: show()
     * FUNGSI: Menampilkan detail satu feedback berdasarkan ID
     * ENDPOINT: GET /api/feedback/{id}
     */
    public function show(string $id)
    {
        try {
            // Cari feedback berdasarkan ID
            // findOrFail akan throw exception jika tidak ditemukan
            $feedback = CustomerFeedback::findOrFail($id);

            // Return response sukses dengan data feedback
            return response()->json([
                'success' => true,
                'message' => 'Feedback retrieved successfully',
                'data' => $feedback
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Jika data tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404); // HTTP 404: Not Found
            
        } catch (\Exception $e) {
            // Jika error lainnya
            Log::error('Error fetching feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: update()
     * FUNGSI: Mengupdate data feedback yang sudah ada
     * ENDPOINT: PUT/PATCH /api/feedback/{id}
     */
    public function update(Request $request, string $id)
    {
        try {
            // Cari feedback berdasarkan ID
            $feedback = CustomerFeedback::findOrFail($id);

            // VALIDASI DATA
            // 'sometimes' berarti field hanya divalidasi jika ada di request
            $validator = Validator::make($request->all(), [
                'customer_name' => 'sometimes|required|string|max:255',
                'message' => 'sometimes|required|string|min:5',
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'sentiment' => 'sometimes|required|in:positive,neutral,negative',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:pending,reviewed,resolved,closed'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // UPDATE DATA
            // only() akan mengambil hanya field yang disebutkan dari request
            $feedback->update($request->only([
                'customer_name',
                'message',
                'rating',
                'sentiment',
                'email',
                'phone',
                'status'
            ]));

            // Log sukses
            Log::info('Feedback updated successfully:', ['id' => $feedback->id]);

            // Return response sukses dengan data terbaru
            // fresh() akan reload data dari database untuk memastikan data terbaru
            return response()->json([
                'success' => true,
                'message' => 'Feedback updated successfully',
                'data' => $feedback->fresh()
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error updating feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: destroy()
     * FUNGSI: Menghapus feedback dari database
     * ENDPOINT: DELETE /api/feedback/{id}
     */
    public function destroy(string $id)
    {
        try {
            // Cari feedback berdasarkan ID
            $feedback = CustomerFeedback::findOrFail($id);
            
            // Hapus feedback dari database
            $feedback->delete();

            // Log sukses
            Log::info('Feedback deleted successfully:', ['id' => $id]);

            // Return response sukses tanpa data
            return response()->json([
                'success' => true,
                'message' => 'Feedback deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error deleting feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: updateStatus()
     * FUNGSI: Mengupdate status feedback saja (endpoint khusus untuk update status)
     * ENDPOINT: PATCH /api/feedback/{id}/status
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            // Cari feedback berdasarkan ID
            $feedback = CustomerFeedback::findOrFail($id);

            // VALIDASI STATUS
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,reviewed,resolved,closed'
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update hanya kolom status
            $feedback->update(['status' => $request->status]);

            // Log sukses
            Log::info('Feedback status updated:', [
                'id' => $id, 
                'status' => $request->status
            ]);

            // Return response sukses dengan data terbaru
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $feedback->fresh()
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: insights()
     * FUNGSI: Mengambil data analytics dan insights dari feedback
     * ENDPOINT: GET /api/feedback/insights
     */
    public function insights()
    {
        try {
            // === STATISTIK DASAR ===
            
            // Hitung total semua feedback
            $total = CustomerFeedback::count();
            
            // Hitung jumlah feedback dengan sentiment positive
            $positive = CustomerFeedback::where('sentiment', 'positive')->count();
            
            // Hitung jumlah feedback dengan sentiment neutral
            $neutral = CustomerFeedback::where('sentiment', 'neutral')->count();
            
            // Hitung jumlah feedback dengan sentiment negative
            $negative = CustomerFeedback::where('sentiment', 'negative')->count();
            
            // Hitung persentase kepuasan pelanggan
            // Rumus: (jumlah positive / total) * 100
            // Jika total > 0, hitung persentase, jika tidak return 0
            $satisfaction = $total > 0 ? round(($positive / $total) * 100, 2) : 0;

            // === DISTRIBUSI RATING ===
            
            // Query untuk menghitung jumlah feedback per rating
            $ratingDistribution = CustomerFeedback::select(
                    'rating',                      // Kolom rating
                    DB::raw('count(*) as count')  // Hitung jumlah per rating
                )
                ->groupBy('rating')               // Group berdasarkan rating
                ->orderBy('rating', 'desc')       // Urutkan dari rating tertinggi
                ->get()
                ->pluck('count', 'rating')        // Buat array dengan key=rating, value=count
                ->toArray();

            // Isi rating yang tidak ada dengan 0
            // Loop dari rating 1 sampai 5
            for ($i = 1; $i <= 5; $i++) {
                if (!isset($ratingDistribution[$i])) {
                    $ratingDistribution[$i] = 0;
                }
            }
            // Sort array berdasarkan key (rating) secara ascending
            ksort($ratingDistribution);

            // === RATA-RATA RATING ===
            
            // Hitung rata-rata rating, jika null return 0
            $averageRating = CustomerFeedback::avg('rating') ?? 0;

            // === DISTRIBUSI STATUS ===
            
            // Query untuk menghitung jumlah feedback per status
            $statusDistribution = CustomerFeedback::select(
                    'status',                      // Kolom status
                    DB::raw('count(*) as count')  // Hitung jumlah per status
                )
                ->groupBy('status')               // Group berdasarkan status
                ->get()
                ->pluck('count', 'status')        // Buat array dengan key=status, value=count
                ->toArray();

            // === FEEDBACK TERBARU (7 HARI TERAKHIR) ===
            
            // Hitung jumlah feedback yang dibuat dalam 7 hari terakhir
            $recentFeedback = CustomerFeedback::where(
                    'created_at', 
                    '>=', 
                    now()->subDays(7)  // Dari tanggal sekarang dikurangi 7 hari
                )
                ->count();

            // === TRENDING SENTIMENT (30 HARI TERAKHIR) ===
            
            // Query untuk menghitung sentiment dalam 30 hari terakhir
            $trendingSentiment = CustomerFeedback::select(
                    'sentiment',
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', now()->subDays(30)) // Filter 30 hari terakhir
                ->groupBy('sentiment')
                ->get()
                ->pluck('count', 'sentiment')
                ->toArray();

            // === TREN BULANAN (6 BULAN TERAKHIR) ===
            
            // Query untuk mendapatkan data feedback per bulan
            $monthlyTrend = CustomerFeedback::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), // Format bulan YYYY-MM
                    DB::raw('count(*) as count'),                         // Jumlah feedback per bulan
                    DB::raw('AVG(rating) as avg_rating')                  // Rata-rata rating per bulan
                )
                ->where('created_at', '>=', now()->subMonths(6))         // Filter 6 bulan terakhir
                ->groupBy('month')                                        // Group berdasarkan bulan
                ->orderBy('month', 'asc')                                 // Urutkan dari bulan terlama
                ->get();

            // === RETURN RESPONSE ===
            
            return response()->json([
                'success' => true,
                'message' => 'Insights retrieved successfully',
                'data' => [
                    // Overview: Statistik ringkasan
                    'overview' => [
                        'total' => $total,                      // Total feedback
                        'positive' => $positive,                // Jumlah positive
                        'neutral' => $neutral,                  // Jumlah neutral
                        'negative' => $negative,                // Jumlah negative
                        'needs_action' => $neutral + $negative, // Yang perlu ditindaklanjuti
                        'satisfaction' => $satisfaction,        // Persentase kepuasan
                        'average_rating' => round($averageRating, 2), // Rata-rata rating (2 desimal)
                        'recent_feedback' => $recentFeedback    // Feedback 7 hari terakhir
                    ],
                    // Distribusi rating 1-5
                    'rating_distribution' => $ratingDistribution,
                    // Distribusi status (pending, reviewed, dll)
                    'status_distribution' => $statusDistribution,
                    // Trending sentiment 30 hari terakhir
                    'trending_sentiment' => $trendingSentiment,
                    // Data tren per bulan (6 bulan terakhir)
                    'monthly_trend' => $monthlyTrend,
                ]
            ], 200);

        } catch (\Exception $e) {
            // Jika terjadi error
            Log::error('Error getting insights: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get insights',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}