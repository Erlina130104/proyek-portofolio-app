<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = SupportTicket::with(['user', 'assignedTo']);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ticket_code', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->input('per_page', 15);
            $tickets = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $tickets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tiket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'category' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['status'] = 'open';

            $ticket = SupportTicket::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tiket support berhasil dibuat',
                'data' => $ticket->load(['user', 'assignedTo'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tiket support',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $ticket = SupportTicket::with(['user', 'assignedTo'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'category' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tiket support berhasil diperbarui',
                'data' => $ticket->load(['user', 'assignedTo'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tiket support',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tiket support berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tiket support',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assign(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->update([
                'assigned_to' => $request->assigned_to,
                'status' => 'in_progress'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil di-assign',
                'data' => $ticket->load(['user', 'assignedTo'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal assign tiket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status tiket berhasil diperbarui',
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status tiket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics()
    {
        try {
            $stats = [
                'total_tickets' => SupportTicket::count(),
                'open_tickets' => SupportTicket::where('status', 'open')->count(),
                'in_progress_tickets' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),
                'closed_tickets' => SupportTicket::where('status', 'closed')->count(),
                'urgent_tickets' => SupportTicket::where('priority', 'urgent')
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count(),
                'by_priority' => [
                    'low' => SupportTicket::where('priority', 'low')->count(),
                    'medium' => SupportTicket::where('priority', 'medium')->count(),
                    'high' => SupportTicket::where('priority', 'high')->count(),
                    'urgent' => SupportTicket::where('priority', 'urgent')->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik tiket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function categories()
    {
        try {
            $categories = SupportTicket::select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category');

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}