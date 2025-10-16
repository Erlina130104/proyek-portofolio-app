<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerFeedbackController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = CustomerFeedback::with(['customer', 'product']);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('comment', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($customerQuery) use ($search) {
                          $customerQuery->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Sentiment filter
            if ($request->filled('sentiment')) {
                $query->where('ai_sentiment', $request->sentiment);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Rating filter
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            // Product filter
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->input('per_page', 12);
            $feedbacks = $query->paginate($perPage);

            // Transform data untuk frontend
            $transformedData = $feedbacks->getCollection()->map(function($feedback) {
                return $this->transformFeedback($feedback);
            });

            $feedbacks->setCollection($transformedData);

            return response()->json([
                'success' => true,
                'data' => $feedbacks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'product_id' => 'nullable|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'sentiment' => 'required|in:positive,neutral,negative',
            'message' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Try to find or create customer if email provided
            $customerId = null;
            if ($request->filled('email')) {
                $customer = Customer::firstOrCreate(
                    ['email' => $request->email],
                    [
                        'name' => $request->customer_name,
                        'phone' => $request->phone,
                    ]
                );
                $customerId = $customer->id;
            }

            // Create feedback
            $feedback = CustomerFeedback::create([
                'customer_id' => $customerId,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->message,
                'ai_sentiment' => $request->sentiment,
                'status' => 'pending'
            ]);

            // Load relationships
            $feedback->load(['customer', 'product']);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas feedback Anda!',
                'data' => $this->transformFeedback($feedback)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $feedback = CustomerFeedback::with(['customer', 'product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $this->transformFeedback($feedback)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update feedback
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'sentiment' => 'sometimes|in:positive,neutral,negative',
            'message' => 'sometimes|string|min:10',
            'status' => 'sometimes|in:pending,reviewed,archived'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $feedback = CustomerFeedback::findOrFail($id);
            
            $updateData = [];
            
            if ($request->filled('rating')) {
                $updateData['rating'] = $request->rating;
            }
            
            if ($request->filled('sentiment')) {
                $updateData['ai_sentiment'] = $request->sentiment;
            }
            
            if ($request->filled('message')) {
                $updateData['comment'] = $request->message;
            }
            
            if ($request->filled('status')) {
                $updateData['status'] = $request->status;
            }

            $feedback->update($updateData);
            $feedback->load(['customer', 'product']);

            return response()->json([
                'success' => true,
                'message' => 'Feedback berhasil diperbarui',
                'data' => $this->transformFeedback($feedback)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete feedback
     */
    public function destroy($id)
    {
        try {
            $feedback = CustomerFeedback::findOrFail($id);
            $feedback->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feedback berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform feedback untuk format frontend
     */
    private function transformFeedback($feedback)
    {
        return [
            'id' => $feedback->id,
            'customer_name' => $feedback->customer ? $feedback->customer->name : 'Guest',
            'user' => $feedback->customer ? $feedback->customer->name : 'Guest',
            'email' => $feedback->customer ? $feedback->customer->email : null,
            'phone' => $feedback->customer ? $feedback->customer->phone : null,
            'rating' => $feedback->rating,
            'sentiment' => $feedback->ai_sentiment ?? ($feedback->rating >= 4 ? 'positive' : ($feedback->rating >= 3 ? 'neutral' : 'negative')),
            'message' => $feedback->comment,
            'feedback' => $feedback->comment,
            'product' => $feedback->product ? $feedback->product->name : null,
            'product_id' => $feedback->product_id,
            'status' => $feedback->status,
            'ai_insights' => $feedback->ai_insights,
            'created_at' => $feedback->created_at->toIso8601String(),
            'date' => $feedback->created_at->toIso8601String(),
        ];
    }

    /**
     * Analyze sentiment from feedback comment
     */
    private function analyzeSentiment($comment, $rating)
    {
        $positiveWords = ['bagus', 'baik', 'puas', 'senang', 'recommended', 'mantap', 'oke', 'top', 'memuaskan', 'excellent', 'hebat', 'keren', 'amazing', 'great', 'good', 'love', 'best'];
        $negativeWords = ['buruk', 'jelek', 'kecewa', 'lambat', 'tidak', 'kurang', 'mengecewakan', 'payah', 'rusak', 'cacat', 'bad', 'terrible', 'poor', 'worst', 'hate', 'disappointed'];

        $comment = strtolower($comment);
        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($positiveWords as $word) {
            if (stripos($comment, $word) !== false) {
                $positiveCount++;
            }
        }

        foreach ($negativeWords as $word) {
            if (stripos($comment, $word) !== false) {
                $negativeCount++;
            }
        }

        // Combine with rating
        if ($rating >= 4 && $positiveCount > $negativeCount) {
            return 'positive';
        } elseif ($rating <= 2 || $negativeCount > $positiveCount) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    /**
     * Get AI insights from customer feedbacks
     */
    public function insights()
    {
        try {
            $feedbacks = CustomerFeedback::with('product')->get();
            
            // Calculate average rating
            $avgRating = $feedbacks->avg('rating');

            // Sentiment distribution
            $sentimentDist = [
                'positive' => $feedbacks->where('ai_sentiment', 'positive')->count(),
                'neutral' => $feedbacks->where('ai_sentiment', 'neutral')->count(),
                'negative' => $feedbacks->where('ai_sentiment', 'negative')->count()
            ];

            // Most mentioned products
            $productMentions = $feedbacks->filter(function($f) {
                    return $f->product_id !== null;
                })
                ->groupBy('product_id')
                ->map(function($group) {
                    return [
                        'product' => $group->first()->product,
                        'count' => $group->count(),
                        'avg_rating' => round($group->avg('rating'), 1)
                    ];
                })
                ->sortByDesc('count')
                ->take(5)
                ->values();

            // Generate insights
            $insights = [];

            if ($avgRating >= 4) {
                $insights[] = 'Pelanggan umumnya puas dengan produk dan layanan';
            } elseif ($avgRating < 3) {
                $insights[] = 'Perlu peningkatan kualitas produk dan layanan';
            }

            if ($sentimentDist['negative'] > $sentimentDist['positive']) {
                $insights[] = 'Terdapat banyak feedback negatif yang perlu ditindaklanjuti';
            }

            // Find products with low ratings
            $lowRatedProducts = Product::whereHas('feedbacks')
                ->with(['feedbacks' => function($q) {
                    $q->select('product_id', DB::raw('AVG(rating) as avg_rating'))
                      ->groupBy('product_id')
                      ->havingRaw('AVG(rating) < 3');
                }])
                ->get()
                ->filter(function($product) {
                    return $product->feedbacks->isNotEmpty();
                });

            if ($lowRatedProducts->count() > 0) {
                $productNames = $lowRatedProducts->pluck('name')->implode(', ');
                $insights[] = "Produk dengan rating rendah: {$productNames}";
            }

            // Popular products
            if ($productMentions->count() > 0) {
                $topProduct = $productMentions->first();
                if (isset($topProduct['product'])) {
                    $insights[] = "Produk paling banyak dikomentari: {$topProduct['product']['name']}";
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'average_rating' => round($avgRating, 1),
                    'sentiment_distribution' => $sentimentDist,
                    'top_mentioned_products' => $productMentions,
                    'insights' => $insights,
                    'total_feedbacks' => $feedbacks->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate insights',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update feedback status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewed,archived'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $feedback = CustomerFeedback::findOrFail($id);
            $feedback->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status feedback berhasil diperbarui',
                'data' => $feedback
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}