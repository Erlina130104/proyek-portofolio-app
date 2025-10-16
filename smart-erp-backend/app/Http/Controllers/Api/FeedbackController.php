<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of feedback with filters and pagination
     */
    public function index(Request $request)
    {
        try {
            $query = CustomerFeedback::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Sentiment filter
            if ($request->filled('sentiment')) {
                $query->where('sentiment', $request->sentiment);
            }

            // Rating filter
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 12);
            
            if ($perPage == 'all' || $perPage > 9999) {
                $feedbacks = $query->get();
                return response()->json([
                    'success' => true,
                    'message' => 'Feedback retrieved successfully',
                    'data' => $feedbacks
                ], 200);
            }

            $feedbacks = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Feedback retrieved successfully',
                'data' => [
                    'data' => $feedbacks->items(),
                    'total' => $feedbacks->total(),
                    'per_page' => $feedbacks->perPage(),
                    'current_page' => $feedbacks->currentPage(),
                    'last_page' => $feedbacks->lastPage(),
                    'from' => $feedbacks->firstItem(),
                    'to' => $feedbacks->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching feedbacks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created feedback
     */
    public function store(Request $request)
    {
        try {
            Log::info('Feedback store request received:', $request->all());

            // Validation rules
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'message' => 'required|string|min:5',
                'rating' => 'required|integer|min:1|max:5',
                'sentiment' => 'required|in:positive,neutral,negative',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
            ], [
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

            if ($validator->fails()) {
                Log::warning('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create feedback
            $feedback = CustomerFeedback::create([
                'customer_name' => $request->customer_name,
                'message' => $request->message,
                'rating' => (int) $request->rating,
                'sentiment' => $request->sentiment,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'pending'
            ]);

            Log::info('Feedback created successfully:', ['id' => $feedback->id]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback created successfully',
                'data' => $feedback
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating feedback: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified feedback
     */
    public function show(string $id)
    {
        try {
            $feedback = CustomerFeedback::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Feedback retrieved successfully',
                'data' => $feedback
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified feedback
     */
    public function update(Request $request, string $id)
    {
        try {
            $feedback = CustomerFeedback::findOrFail($id);

            // Validation rules
            $validator = Validator::make($request->all(), [
                'customer_name' => 'sometimes|required|string|max:255',
                'message' => 'sometimes|required|string|min:5',
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'sentiment' => 'sometimes|required|in:positive,neutral,negative',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:pending,reviewed,resolved,closed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update feedback
            $feedback->update($request->only([
                'customer_name',
                'message',
                'rating',
                'sentiment',
                'email',
                'phone',
                'status'
            ]));

            Log::info('Feedback updated successfully:', ['id' => $feedback->id]);

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
     * Remove the specified feedback
     */
    public function destroy(string $id)
    {
        try {
            $feedback = CustomerFeedback::findOrFail($id);
            $feedback->delete();

            Log::info('Feedback deleted successfully:', ['id' => $id]);

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
     * Update feedback status
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $feedback = CustomerFeedback::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,reviewed,resolved,closed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $feedback->update(['status' => $request->status]);

            Log::info('Feedback status updated:', ['id' => $id, 'status' => $request->status]);

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
     * Get feedback analytics and insights
     */
    public function insights()
    {
        try {
            // Basic statistics
            $total = CustomerFeedback::count();
            $positive = CustomerFeedback::where('sentiment', 'positive')->count();
            $neutral = CustomerFeedback::where('sentiment', 'neutral')->count();
            $negative = CustomerFeedback::where('sentiment', 'negative')->count();
            
            // Calculate satisfaction percentage
            $satisfaction = $total > 0 ? round(($positive / $total) * 100, 2) : 0;

            // Rating distribution
            $ratingDistribution = CustomerFeedback::select('rating', DB::raw('count(*) as count'))
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->get()
                ->pluck('count', 'rating')
                ->toArray();

            // Fill missing ratings with 0
            for ($i = 1; $i <= 5; $i++) {
                if (!isset($ratingDistribution[$i])) {
                    $ratingDistribution[$i] = 0;
                }
            }
            ksort($ratingDistribution);

            // Average rating
            $averageRating = CustomerFeedback::avg('rating') ?? 0;

            // Status distribution
            $statusDistribution = CustomerFeedback::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            // Recent feedback count (last 7 days)
            $recentFeedback = CustomerFeedback::where('created_at', '>=', now()->subDays(7))->count();

            // Trending sentiment (last 30 days)
            $trendingSentiment = CustomerFeedback::select('sentiment', DB::raw('count(*) as count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('sentiment')
                ->get()
                ->pluck('count', 'sentiment')
                ->toArray();

            // Monthly feedback trend (last 6 months)
            $monthlyTrend = CustomerFeedback::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count'),
                    DB::raw('AVG(rating) as avg_rating')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Insights retrieved successfully',
                'data' => [
                    'overview' => [
                        'total' => $total,
                        'positive' => $positive,
                        'neutral' => $neutral,
                        'negative' => $negative,
                        'needs_action' => $neutral + $negative,
                        'satisfaction' => $satisfaction,
                        'average_rating' => round($averageRating, 2),
                        'recent_feedback' => $recentFeedback
                    ],
                    'rating_distribution' => $ratingDistribution,
                    'status_distribution' => $statusDistribution,
                    'trending_sentiment' => $trendingSentiment,
                    'monthly_trend' => $monthlyTrend,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting insights: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get insights',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}