<?php

namespace App\Http\Controllers\Api;

// Import class-class yang diperlukan
use App\Http\Controllers\Controller; // Base controller dari Laravel
use App\Models\SupportTicket; // Model SupportTicket untuk interaksi dengan database
use App\Models\User; // Model User (diimport tapi tidak digunakan langsung di controller ini)
use Illuminate\Http\Request; // Class untuk handle HTTP request
use Illuminate\Support\Facades\Validator; // Facade untuk validasi data

class SupportTicketController extends Controller
{
    /**
     * METHOD: index()
     * FUNGSI: Mengambil daftar tiket support dengan fitur filtering, sorting, dan pagination
     * ENDPOINT: GET /api/support-tickets
     */
    public function index(Request $request)
    {
        try {
            // Membuat query builder dari model SupportTicket
            // with() melakukan eager loading relasi 'user' dan 'assignedTo'
            // Ini akan mengambil data user pembuat tiket dan user yang di-assign dalam 1 query
            // untuk menghindari N+1 query problem
            $query = SupportTicket::with(['user', 'assignedTo']);

            // FILTER BERDASARKAN STATUS
            // Jika parameter 'status' ada (open/in_progress/resolved/closed)
            if ($request->has('status')) {
                // Filter tiket dengan status yang sesuai
                $query->where('status', $request->status);
            }

            // FILTER BERDASARKAN PRIORITY
            // Jika parameter 'priority' ada (low/medium/high/urgent)
            if ($request->has('priority')) {
                // Filter tiket dengan prioritas yang sesuai
                $query->where('priority', $request->priority);
            }

            // FILTER BERDASARKAN KATEGORI
            // Jika parameter 'category' ada
            if ($request->has('category')) {
                // Filter tiket dengan kategori yang sesuai
                $query->where('category', $request->category);
            }

            // FILTER BERDASARKAN USER YANG DI-ASSIGN
            // Jika parameter 'assigned_to' ada (ID user)
            if ($request->has('assigned_to')) {
                // Filter tiket yang di-assign ke user tertentu
                $query->where('assigned_to', $request->assigned_to);
            }

            // FITUR PENCARIAN
            // Jika parameter 'search' ada
            if ($request->has('search')) {
                $search = $request->search; // Ambil nilai search dari request
                
                // Tambahkan kondisi WHERE dengan LIKE untuk mencari di beberapa kolom
                $query->where(function($q) use ($search) {
                    $q->where('ticket_code', 'like', "%{$search}%")    // Cari di kode tiket
                      ->orWhere('title', 'like', "%{$search}%")        // ATAU cari di judul
                      ->orWhere('description', 'like', "%{$search}%"); // ATAU cari di deskripsi
                });
            }

            // SORTING
            // Ambil parameter sort_by, default 'created_at' jika tidak ada
            $sortBy = $request->input('sort_by', 'created_at');
            // Ambil parameter sort_order, default 'desc' (terbaru dulu) jika tidak ada
            $sortOrder = $request->input('sort_order', 'desc');
            // Terapkan sorting ke query
            $query->orderBy($sortBy, $sortOrder);

            // PAGINATION
            // Ambil jumlah data per halaman, default 15
            $perPage = $request->input('per_page', 15);
            // Lakukan pagination dengan jumlah per halaman sesuai parameter
            $tickets = $query->paginate($perPage);

            // Return response JSON dengan data pagination
            // Laravel paginate() sudah otomatis include: data, current_page, total, dll
            return response()->json([
                'success' => true,
                'data' => $tickets
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error, return response error
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tiket',
                'error' => $e->getMessage()
            ], 500); // HTTP 500: Internal Server Error
        }
    }

    /**
     * METHOD: store()
     * FUNGSI: Membuat tiket support baru
     * ENDPOINT: POST /api/support-tickets
     */
    public function store(Request $request)
    {
        // VALIDASI DATA
        // Membuat validator dengan aturan validasi untuk form tambah tiket
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',                  // Judul tiket wajib, max 255 karakter
            'description' => 'required|string',                    // Deskripsi wajib, tanpa batas karakter
            'priority' => 'nullable|in:low,medium,high,urgent',    // Prioritas optional, hanya 4 nilai ini
            'category' => 'nullable|string|max:255',               // Kategori optional, max 255 karakter
            'user_id' => 'nullable|exists:users,id'                // User ID optional, harus ada di tabel users
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            // Return response error dengan detail validasi
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors() // Berisi detail error per field
            ], 422); // HTTP 422: Unprocessable Entity
        }

        try {
            // Ambil semua data dari request
            $data = $request->all();
            // Set status default ke 'open' untuk tiket baru
            // Setiap tiket baru otomatis berstatus 'open'
            $data['status'] = 'open';

            // SIMPAN DATA KE DATABASE
            // Create record baru di tabel support_tickets
            $ticket = SupportTicket::create($data);

            // Return response sukses dengan data tiket yang baru dibuat
            // load(['user', 'assignedTo']) akan reload relasi setelah create
            // untuk menampilkan data user terkait di response
            return response()->json([
                'success' => true,
                'message' => 'Tiket support berhasil dibuat',
                'data' => $ticket->load(['user', 'assignedTo'])
            ], 201); // HTTP 201: Created
            
        } catch (\Exception $e) {
            // Jika terjadi error saat menyimpan
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tiket support',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: show()
     * FUNGSI: Menampilkan detail satu tiket support berdasarkan ID
     * ENDPOINT: GET /api/support-tickets/{id}
     */
    public function show($id)
    {
        try {
            // Cari tiket berdasarkan ID dengan eager loading relasi
            // with(['user', 'assignedTo']) akan load data user pembuat dan user yang di-assign
            // findOrFail() akan throw exception jika tidak ditemukan
            $ticket = SupportTicket::with(['user', 'assignedTo'])->findOrFail($id);

            // Return response sukses dengan data tiket lengkap
            return response()->json([
                'success' => true,
                'data' => $ticket
            ]);
            
        } catch (\Exception $e) {
            // Jika tiket tidak ditemukan atau error lainnya
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan',
                'error' => $e->getMessage()
            ], 404); // HTTP 404: Not Found
        }
    }

    /**
     * METHOD: update()
     * FUNGSI: Mengupdate data tiket support yang sudah ada
     * ENDPOINT: PUT/PATCH /api/support-tickets/{id}
     */
    public function update(Request $request, $id)
    {
        // VALIDASI DATA
        // 'sometimes' berarti field hanya divalidasi jika ada di request
        // Cocok untuk update karena tidak semua field harus dikirim
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'category' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id'  // User yang di-assign harus ada di tabel users
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari tiket berdasarkan ID
            $ticket = SupportTicket::findOrFail($id);
            
            // UPDATE DATA TIKET
            // update() akan mengupdate semua field yang ada di request
            $ticket->update($request->all());

            // Return response sukses dengan data tiket yang sudah diupdate
            // load(['user', 'assignedTo']) akan reload relasi untuk menampilkan data terbaru
            return response()->json([
                'success' => true,
                'message' => 'Tiket support berhasil diperbarui',
                'data' => $ticket->load(['user', 'assignedTo'])
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat update
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tiket support',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: destroy()
     * FUNGSI: Menghapus tiket support dari database
     * ENDPOINT: DELETE /api/support-tickets/{id}
     */
    public function destroy($id)
    {
        try {
            // Cari tiket berdasarkan ID
            $ticket = SupportTicket::findOrFail($id);
            
            // HAPUS DATA TIKET
            // delete() akan menghapus record dari database
            $ticket->delete();

            // Return response sukses tanpa data
            return response()->json([
                'success' => true,
                'message' => 'Tiket support berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat hapus
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tiket support',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: assign()
     * FUNGSI: Meng-assign tiket ke user tertentu dan otomatis mengubah status ke 'in_progress'
     * ENDPOINT: PATCH /api/support-tickets/{id}/assign
     */
    public function assign(Request $request, $id)
    {
        // VALIDASI DATA
        $validator = Validator::make($request->all(), [
            // assigned_to wajib diisi dan harus berupa ID user yang ada di tabel users
            'assigned_to' => 'required|exists:users,id'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari tiket berdasarkan ID
            $ticket = SupportTicket::findOrFail($id);
            
            // UPDATE ASSIGNED_TO DAN STATUS
            // Ketika tiket di-assign ke user, statusnya otomatis berubah ke 'in_progress'
            // Ini menandakan tiket sudah mulai ditangani
            $ticket->update([
                'assigned_to' => $request->assigned_to,  // Set user yang akan menangani
                'status' => 'in_progress'                 // Ubah status ke in_progress
            ]);

            // Return response sukses dengan data tiket yang sudah diupdate
            // load(['user', 'assignedTo']) untuk menampilkan data user terkait
            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil di-assign',
                'data' => $ticket->load(['user', 'assignedTo'])
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat assign
            return response()->json([
                'success' => false,
                'message' => 'Gagal assign tiket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: updateStatus()
     * FUNGSI: Mengupdate status tiket saja (endpoint khusus untuk update status)
     * ENDPOINT: PATCH /api/support-tickets/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        // VALIDASI STATUS
        $validator = Validator::make($request->all(), [
            // Status wajib diisi dan hanya boleh 4 nilai ini
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari tiket berdasarkan ID
            $ticket = SupportTicket::findOrFail($id);
            
            // UPDATE HANYA KOLOM STATUS
            // Hanya mengubah status tiket tanpa mengubah field lainnya
            $ticket->update(['status' => $request->status]);

            // Return response sukses dengan data tiket yang sudah diupdate
            return response()->json([
                'success' => true,
                'message' => 'Status tiket berhasil diperbarui',
                'data' => $ticket
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat update status
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status tiket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: statistics()
     * FUNGSI: Mengambil statistik dan data analytics dari tiket support
     * ENDPOINT: GET /api/support-tickets/statistics
     */
    public function statistics()
    {
        try {
            // HITUNG STATISTIK TIKET
            $stats = [
                // Total semua tiket
                'total_tickets' => SupportTicket::count(),
                
                // Jumlah tiket dengan status 'open' (baru dibuat, belum ditangani)
                'open_tickets' => SupportTicket::where('status', 'open')->count(),
                
                // Jumlah tiket dengan status 'in_progress' (sedang ditangani)
                'in_progress_tickets' => SupportTicket::where('status', 'in_progress')->count(),
                
                // Jumlah tiket dengan status 'resolved' (sudah diselesaikan)
                'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),
                
                // Jumlah tiket dengan status 'closed' (sudah ditutup)
                'closed_tickets' => SupportTicket::where('status', 'closed')->count(),
                
                // Jumlah tiket urgent yang belum selesai (butuh perhatian segera)
                // whereIn() untuk filter status yang masih aktif (open atau in_progress)
                'urgent_tickets' => SupportTicket::where('priority', 'urgent')
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count(),
                
                // Distribusi tiket berdasarkan prioritas
                'by_priority' => [
                    'low' => SupportTicket::where('priority', 'low')->count(),       // Prioritas rendah
                    'medium' => SupportTicket::where('priority', 'medium')->count(), // Prioritas menengah
                    'high' => SupportTicket::where('priority', 'high')->count(),     // Prioritas tinggi
                    'urgent' => SupportTicket::where('priority', 'urgent')->count()  // Prioritas urgent
                ]
            ];

            // Return response sukses dengan data statistik
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat mengambil statistik
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik tiket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: categories()
     * FUNGSI: Mengambil daftar kategori tiket yang unik (tidak duplikat)
     * ENDPOINT: GET /api/support-tickets/categories
     */
    public function categories()
    {
        try {
            // Query untuk mengambil kategori unik
            $categories = SupportTicket::select('category')  // Pilih hanya kolom category
                ->distinct()                                  // Ambil nilai yang unik (tidak duplikat)
                ->whereNotNull('category')                    // Hanya yang category-nya tidak null
                ->pluck('category');                          // Return sebagai array sederhana
                                                              // Contoh: ["Technical", "Billing", "General"]

            // Return response sukses dengan daftar kategori
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat ambil kategori
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}