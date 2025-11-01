<?php

namespace App\Http\Controllers\Api;

// Import class-class yang diperlukan
use App\Http\Controllers\Controller; // Base controller dari Laravel
use App\Models\Product; // Model Product untuk interaksi dengan database
use Illuminate\Http\Request; // Class untuk handle HTTP request
use Illuminate\Support\Facades\Validator; // Facade untuk validasi data
use Illuminate\Support\Facades\Storage; // Facade untuk handle file storage

class ProductController extends Controller
{
    /**
     * METHOD: index()
     * FUNGSI: Mengambil daftar produk dengan fitur filtering, sorting, dan pagination
     * ENDPOINT: GET /api/products
     */
    public function index(Request $request)
    {
        try {
            // Membuat query builder dari model Product
            $query = Product::query();

            // FITUR PENCARIAN
            // Mengecek apakah parameter 'search' ada dalam request
            // has() akan return true jika parameter ada, meskipun nilainya kosong
            if ($request->has('search')) {
                $search = $request->search; // Ambil nilai search dari request
                
                // Tambahkan kondisi WHERE dengan LIKE untuk mencari di beberapa kolom
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")      // Cari di nama produk
                      ->orWhere('sku', 'like', "%{$search}%")     // ATAU cari di SKU
                      ->orWhere('category', 'like', "%{$search}%"); // ATAU cari di kategori
                });
            }

            // FILTER BERDASARKAN KATEGORI
            // Jika parameter 'category' ada dalam request
            if ($request->has('category')) {
                // Filter produk dengan kategori yang sesuai
                $query->where('category', $request->category);
            }

            // FILTER BERDASARKAN STATUS
            // Jika parameter 'status' ada (active/inactive)
            if ($request->has('status')) {
                // Filter produk dengan status yang sesuai
                $query->where('status', $request->status);
            }

            // FILTER STOK RENDAH
            // Mengecek apakah parameter 'low_stock' ada DAN bernilai true/1
            if ($request->has('low_stock') && $request->low_stock) {
                // Filter produk dengan stok kurang dari atau sama dengan 10
                $query->where('stock', '<=', 10);
            }

            // SORTING
            // Ambil parameter sort_by, default 'created_at' jika tidak ada
            $sortBy = $request->input('sort_by', 'created_at');
            // Ambil parameter sort_order, default 'desc' (descending) jika tidak ada
            $sortOrder = $request->input('sort_order', 'desc');
            // Terapkan sorting ke query
            $query->orderBy($sortBy, $sortOrder);

            // PAGINATION
            // Ambil jumlah data per halaman, default 15
            $perPage = $request->input('per_page', 15);
            // Lakukan pagination dengan jumlah per halaman sesuai parameter
            $products = $query->paginate($perPage);

            // Return response JSON dengan data pagination
            // Laravel paginate() sudah otomatis include: data, current_page, total, dll
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error, return response error
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500); // HTTP 500: Internal Server Error
        }
    }

    /**
     * METHOD: store()
     * FUNGSI: Menambahkan produk baru dengan support upload gambar
     * ENDPOINT: POST /api/products
     */
    public function store(Request $request)
    {
        // VALIDASI DATA
        // Membuat validator dengan aturan validasi untuk form tambah produk
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',           // Wajib, string, max 255 karakter
            'sku' => 'required|string|unique:products,sku', // Wajib, string, harus unik di tabel products kolom sku
            'price' => 'required|numeric|min:0',           // Wajib, angka, minimal 0 (tidak boleh negatif)
            'stock' => 'required|integer|min:0',           // Wajib, integer, minimal 0
            'category' => 'nullable|string|max:255',       // Optional, string, max 255 karakter
            'description' => 'nullable|string',            // Optional, string tanpa batas karakter
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Optional, harus gambar, tipe jpeg/png/jpg, max 2MB
            'status' => 'nullable|in:active,inactive'      // Optional, hanya boleh 'active' atau 'inactive'
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

            // PROSES UPLOAD GAMBAR
            // Mengecek apakah ada file yang diupload dengan nama 'image'
            if ($request->hasFile('image')) {
                // Simpan file gambar ke folder 'products' di disk 'public'
                // store() akan return path file yang tersimpan (contoh: products/abc123.jpg)
                $imagePath = $request->file('image')->store('products', 'public');
                // Tambahkan path gambar ke array data
                $data['image'] = $imagePath;
            }

            // SIMPAN DATA KE DATABASE
            // Create record baru di tabel products
            $product = Product::create($data);

            // Return response sukses dengan data produk yang baru dibuat
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201); // HTTP 201: Created
            
        } catch (\Exception $e) {
            // Jika terjadi error saat menyimpan
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: show()
     * FUNGSI: Menampilkan detail produk dengan data tambahan (total terjual & rating)
     * ENDPOINT: GET /api/products/{id}
     */
    public function show($id)
    {
        try {
            // Cari produk berdasarkan ID dengan eager loading relasi
            // with() akan load relasi untuk menghindari N+1 query problem
            // findOrFail() akan throw exception jika tidak ditemukan
            $product = Product::with(['transactionItems', 'feedbacks'])->findOrFail($id);

            // HITUNG TOTAL UNIT TERJUAL
            // transactionItems() adalah relasi ke tabel transaction_items
            // sum('quantity') akan menjumlahkan semua quantity di relasi tersebut
            $totalSold = $product->transactionItems()->sum('quantity');
            
            // HITUNG RATA-RATA RATING
            // feedbacks() adalah relasi ke tabel feedbacks
            // avg('rating') akan menghitung rata-rata dari kolom rating
            $avgRating = $product->feedbacks()->avg('rating');

            // Tambahkan data computed ke object product
            $product->total_sold = $totalSold;
            // round($avgRating, 1) membulatkan ke 1 angka di belakang koma
            $product->average_rating = round($avgRating, 1);

            // Return response sukses dengan data produk lengkap
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
            
        } catch (\Exception $e) {
            // Jika produk tidak ditemukan atau error lainnya
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
                'error' => $e->getMessage()
            ], 404); // HTTP 404: Not Found
        }
    }

    /**
     * METHOD: update()
     * FUNGSI: Mengupdate data produk yang sudah ada (termasuk update gambar)
     * ENDPOINT: PUT/PATCH /api/products/{id}
     */
    public function update(Request $request, $id)
    {
        // VALIDASI DATA
        // 'sometimes' berarti field hanya divalidasi jika ada di request
        // Cocok untuk update karena tidak semua field harus dikirim
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            // unique:products,sku,' . $id → SKU harus unik, tapi abaikan record dengan ID ini
            // Jadi produk ini boleh tetap pakai SKU yang sama
            'sku' => 'sometimes|required|string|unique:products,sku,' . $id,
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'nullable|in:active,inactive'
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
            // Cari produk berdasarkan ID
            $product = Product::findOrFail($id);
            // Ambil semua data dari request
            $data = $request->all();

            // PROSES UPDATE GAMBAR
            // Jika ada file gambar baru yang diupload
            if ($request->hasFile('image')) {
                // Jika produk sudah punya gambar lama
                if ($product->image) {
                    // Hapus gambar lama dari storage untuk menghemat space
                    // delete() akan menghapus file berdasarkan path
                    Storage::disk('public')->delete($product->image);
                }
                
                // Simpan gambar baru
                $imagePath = $request->file('image')->store('products', 'public');
                // Update path gambar di array data
                $data['image'] = $imagePath;
            }

            // UPDATE DATA PRODUK
            // update() akan mengupdate record di database dengan data dari $data
            $product->update($data);

            // Return response sukses dengan data produk yang sudah diupdate
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => $product
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat update
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: destroy()
     * FUNGSI: Menghapus produk dari database beserta file gambarnya
     * ENDPOINT: DELETE /api/products/{id}
     */
    public function destroy($id)
    {
        try {
            // Cari produk berdasarkan ID
            $product = Product::findOrFail($id);
            
            // HAPUS FILE GAMBAR
            // Jika produk memiliki gambar
            if ($product->image) {
                // Hapus file gambar dari storage
                Storage::disk('public')->delete($product->image);
            }

            // HAPUS DATA PRODUK
            // delete() akan menghapus record dari database
            $product->delete();

            // Return response sukses tanpa data
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat hapus
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: updateStock()
     * FUNGSI: Mengupdate stok produk dengan 3 mode: set (ganti), add (tambah), subtract (kurangi)
     * ENDPOINT: PATCH /api/products/{id}/stock
     */
    public function updateStock(Request $request, $id)
    {
        // VALIDASI DATA
        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0',          // Jumlah stok, wajib, integer, minimal 0
            'type' => 'required|in:set,add,subtract',     // Tipe update: set/add/subtract
            'notes' => 'nullable|string'                  // Catatan optional
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
            // Cari produk berdasarkan ID
            $product = Product::findOrFail($id);
            // Simpan stok lama untuk ditampilkan di response
            $oldStock = $product->stock;

            // UPDATE STOK SESUAI TIPE
            // Switch case untuk handle 3 jenis update stok
            switch ($request->type) {
                case 'set':
                    // SET: Ganti stok dengan nilai baru langsung
                    // Contoh: stok 100 → set 50 = jadi 50
                    $product->stock = $request->stock;
                    break;
                    
                case 'add':
                    // ADD: Tambah stok dengan nilai yang diberikan
                    // Contoh: stok 100 → add 20 = jadi 120
                    $product->stock += $request->stock;
                    break;
                    
                case 'subtract':
                    // SUBTRACT: Kurangi stok dengan nilai yang diberikan
                    // Tapi cek dulu apakah stok mencukupi
                    if ($product->stock < $request->stock) {
                        // Jika stok tidak cukup, return error
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok tidak mencukupi'
                        ], 400); // HTTP 400: Bad Request
                    }
                    // Contoh: stok 100 → subtract 20 = jadi 80
                    $product->stock -= $request->stock;
                    break;
            }

            // Simpan perubahan ke database
            $product->save();

            // Return response sukses dengan detail perubahan stok
            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil diperbarui',
                'data' => [
                    'product' => $product,                      // Data produk lengkap
                    'old_stock' => $oldStock,                   // Stok sebelum update
                    'new_stock' => $product->stock,             // Stok setelah update
                    'difference' => $product->stock - $oldStock // Selisih perubahan (+ atau -)
                ]
            ]);
            
        } catch (\Exception $e) {
            // Jika terjadi error saat update stok
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * METHOD: categories()
     * FUNGSI: Mengambil daftar kategori produk yang unik (tidak duplikat)
     * ENDPOINT: GET /api/products/categories
     */
    public function categories()
    {
        try {
            // Query untuk mengambil kategori unik
            $categories = Product::select('category')     // Pilih hanya kolom category
                ->distinct()                               // Ambil nilai yang unik (tidak duplikat)
                ->whereNotNull('category')                 // Hanya yang category-nya tidak null
                ->pluck('category');                       // Return sebagai array sederhana

            // Return response sukses dengan daftar kategori
            return response()->json([
                'success' => true,
                'data' => $categories // Array kategori: ["Elektronik", "Pakaian", "Makanan"]
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