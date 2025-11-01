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
    //  Get semua feedback + filter + search
    public function index(Request $request)
    {
        try {
            // Ambil data feedback + relasi customer & produk
            $query = CustomerFeedback::with(['customer', 'product']);

            //  Search komentar / nama customer
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

            //  Filter berdasarkan hasil sentimen AI
            if ($request->filled('sentiment')) {
                $query->where('ai_sentiment', $request->sentiment);
            }

            // Filter status feedback (pending/reviewed/archived)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            //  Filter rating (1–5)
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            //  Filter produk tertentu
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            //  Sorting data (default terbaru)
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            //  Pagination (default 12 per page)
            $perPage = $request->input('per_page', 12);
            $feedbacks = $query->paginate($perPage);

            // Format data untuk frontend
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

    // Simpan feedback baru
    public function store(Request $request)
    {
        // Validasi input user
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'product_id' => 'nullable|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'sentiment' => 'required|in:positive,neutral,negative',
            'message' => 'required|string|min:10'
        ]);

        // Jika gagal validasi
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            //  Cek customer berdasarkan email, kalau tidak ada → buat baru
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

            //  Simpan ke database
            $feedback = CustomerFeedback::create([
                'customer_id' => $customerId,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->message,
                'ai_sentiment' => $request->sentiment, // hasil analisa AI
                'status' => 'pending'
            ]);

            // Load relasi data untuk ditampilkan
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

    // Detail satu feedback
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

    // Update feedback (rating, pesan, status)
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

            // Update hanya field yang dikirim user
            $updateData = [];

            if ($request->filled('rating')) $updateData['rating'] = $request->rating;
            if ($request->filled('sentiment')) $updateData['ai_sentiment'] = $request->sentiment;
            if ($request->filled('message')) $updateData['comment'] = $request->message;
            if ($request->filled('status')) $updateData['status'] = $request->status;

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

    // Hapus feedback
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

    // Format data sebelum dikirim ke frontend
    private function transformFeedback($feedback)
    {
        return [
            'id' => $feedback->id,
            'customer_name' => $feedback->customer->name ?? 'Guest',
            'email' => $feedback->customer->email ?? null,
            'phone' => $feedback->customer->phone ?? null,
            'rating' => $feedback->rating,
            'sentiment' => $feedback->ai_sentiment,
            'message' => $feedback->comment,
            'product' => $feedback->product->name ?? null,
            'status' => $feedback->status,
            'created_at' => $feedback->created_at->toIso8601String()
        ];
    }
}
