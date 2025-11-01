# SmartERP - Sistem Manajemen Bisnis Kostum

> Sistem ERP yang saya bangun untuk mengelola bisnis rental dan penjualan kostum secara terintegrasi

![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js) ![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel) ![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)

![Dashboard Overview](dashboard-main.png)
*Dashboard utama dengan metrik bisnis real-time*

---

## Tentang Proyek

SmartERP ini saya bangun dari nol untuk mengatasi tantangan spesifik dalam bisnis kostum. Sistemnya dirancang untuk handle dua model bisnis sekaligus: penjualan dan penyewaan kostum, dengan manajemen inventory otomatis.

**Kenapa Saya Bikin Ini:**
- Pengen praktek langsung bikin sistem yang kompleks, bukan cuma CRUD sederhana
- Belajar gimana caranya design database yang proper untuk bisnis real
- Ngasah skill backend development dengan Laravel dan best practices-nya
- Ngerti cara build REST API yang scalable dan maintainable

---

## Arsitektur Sistem

Proyek ini pakai **arsitektur 3-tier** standar dengan pemisahan yang jelas:

```
┌─────────────────────────────────────────┐
│         Frontend (Vue.js 3)             │
│   - User Interface                      │
│   - State Management (Pinia)            │
│   - API Integration                     │
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│         Backend (Laravel 10)            │
│   - REST API                            │
│   - Business Logic                      │
│   - Authentication (JWT)                │
│   - Data Validation                     │
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│         Database (MySQL 8.0)            │
│   - Normalized Schema                   │
│   - 15+ Tables                          │
│   - Indexed Queries                     │
└─────────────────────────────────────────┘
```

**Kenapa Pakai Arsitektur Ini:**
1. **Separation of Concerns** - Frontend dan backend terpisah, gampang di-maintain
2. **Scalability** - Bisa di-deploy terpisah kalau butuh scale
3. **Team-Friendly** - Frontend dev dan backend dev bisa kerja parallel
4. **Industry Standard** - Arsitektur yang umum dipakai di perusahaan

---

## Tech Stack

### Frontend
- **Vue.js 3** - Framework JavaScript dengan Composition API
- **Tailwind CSS** - Utility-first CSS framework
- **Pinia** - State management yang lightweight
- **Vue Router** - Routing di sisi client
- **Axios** - HTTP client untuk konsumsi API

### Backend
- **Laravel 10** - PHP framework dengan Eloquent ORM
- **MySQL 8.0** - Relational database
- **JWT (tymon/jwt-auth)** - Token-based authentication
- **Laravel Sanctum** - API token management

### Development Tools
- **Git & GitHub** - Version control
- **Postman** - API testing dan dokumentasi
- **Vite** - Build tool untuk frontend
- **Composer** - Dependency management PHP
- **npm** - Package manager JavaScript

---

## Fitur Utama

### 1. Dashboard Analytics Real-time

Dashboard yang nampilang metrik bisnis penting secara real-time. Datanya di-aggregate dari berbagai modul dan ditampilin dalam bentuk yang mudah dipahami.

**Metrik yang Ditampilkan:**
- Total revenue hari ini/bulan ini dengan persentase pertumbuhan
- Jumlah transaksi (breakdown penjualan vs rental)
- Status inventory (produk low stock, best seller)
- Tingkat kehadiran karyawan
- Recent activities dan notifications

**Implementasi Teknis:**
```php
// Example: Query aggregation untuk dashboard metrics
$metrics = [
    'total_revenue' => Transaction::whereDate('created_at', today())->sum('total'),
    'transaction_count' => Transaction::whereDate('created_at', today())->count(),
    'low_stock_products' => Product::where('stock', '<=', 10)->count(),
    'attendance_rate' => Attendance::calculateTodayRate()
];
```

### 2. Manajemen Produk

![Product Management](products.png)

Sistem katalog lengkap untuk manage produk kostum dengan detail material, ukuran, warna, dan supplier. Support dual pricing (harga jual dan sewa per hari) serta tracking stok otomatis.

**Fitur:**
- CRUD operations dengan validasi yang ketat
- Filter dan search real-time
- Notifikasi otomatis kalau stok ≤ 10 items
- Upload gambar produk dengan compression
- Kategori: Tradisional, Modern, Anak-anak, Pertunjukan
- Tracking supplier dan purchase price untuk analisa margin

**Database Design:**
```sql
products
├── id
├── name
├── sku (unique)
├── category
├── material
├── size
├── color
├── stock
├── purchase_price
├── sale_price
├── rental_price_per_day
├── supplier_id
└── timestamps
```

### 3. Sistem Transaksi

![Transaction System](transactions.png)

Handle penjualan dan rental dalam satu sistem. Stok inventory otomatis berkurang pas transaksi dibuat, dan bisa dirollback kalau transaksi dibatalkan.

**Flow Transaksi:**
1. User pilih produk dan tentukan tipe (sale/rental)
2. System validasi stok availability
3. Kalkulasi total (untuk rental: price × quantity × days)
4. Kurangi stok secara atomic
5. Generate invoice dengan unique transaction code

**Contoh Logic:**
```php
// Validasi dan kurangi stok (with transaction)
DB::transaction(function () use ($data) {
    $product = Product::lockForUpdate()->find($data['product_id']);
    
    if ($product->stock < $data['quantity']) {
        throw new InsufficientStockException();
    }
    
    $product->decrement('stock', $data['quantity']);
    
    Transaction::create([
        'product_id' => $product->id,
        'type' => $data['type'], // 'sale' or 'rental'
        'quantity' => $data['quantity'],
        'total' => $this->calculateTotal($data),
        // ... fields lainnya
    ]);
});
```

### 4. Manajemen Karyawan & Absensi

![Employee Management](employees.png)

Database karyawan lengkap dengan sistem absensi harian. Bisa tracking kehadiran, menghitung tingkat kehadiran, dan generate laporan bulanan.

**Fitur Absensi:**
- Check-in/check-out dengan timestamp
- Status: Hadir, Sakit, Izin, Alpha
- Perhitungan tingkat kehadiran per bulan
- Generate laporan kehadiran per periode
- Filter dan export data kehadiran

### 5. Customer Feedback & Support Tickets

Sistem untuk handle feedback pelanggan dan support tickets. Customer bisa kasih rating dan komentar, sementara tim bisa track dan resolve issues.

**Status Ticket:**
- Open (baru masuk)
- In Progress (sedang ditangani)
- Resolved (sudah selesai)
- Closed (ditutup setelah konfirmasi customer)

---

## REST API Documentation

API dibangun mengikuti RESTful principles dengan response format yang konsisten.

### Authentication

Semua protected endpoints butuh JWT token di header:
```
Authorization: Bearer {token}
```

### Endpoint Structure

**Products:**
```
GET    /api/products           - List semua produk (support pagination, filter, search)
GET    /api/products/{id}      - Detail produk by ID
POST   /api/products           - Create produk baru
PUT    /api/products/{id}      - Update produk
DELETE /api/products/{id}      - Soft delete produk
```

**Transactions:**
```
GET    /api/transactions       - List transaksi (filter by type, date range)
GET    /api/transactions/{id}  - Detail transaksi
POST   /api/transactions       - Create transaksi baru
PUT    /api/transactions/{id}  - Update transaksi
POST   /api/transactions/{id}/cancel - Cancel transaksi (rollback stock)
```

**Employees:**
```
GET    /api/employees          - List karyawan
POST   /api/employees          - Add karyawan baru
GET    /api/employees/{id}/attendance - History absensi
POST   /api/attendance         - Record kehadiran
```

### Response Format

Semua API response pakai format standar:

**Success Response:**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": {
    // response data
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // validation errors (jika ada)
  }
}
```

### Pagination

List endpoint support pagination dengan format:
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

---

## Database Schema

Database di-design dengan prinsip normalization untuk menghindari redundancy dan maintain data integrity.

### Key Tables

**products** - Katalog produk kostum
- Primary key: id
- Unique: sku
- Indexed: category, stock
- Relations: hasMany transactions, belongsTo supplier

**transactions** - Record semua transaksi
- Primary key: id
- Indexed: transaction_date, type
- Relations: belongsTo product, belongsTo customer

**employees** - Data karyawan
- Primary key: id
- Unique: email, employee_number
- Relations: hasMany attendances

**attendances** - Absensi harian
- Primary key: id
- Indexed: date, employee_id
- Relations: belongsTo employee

### Relationships

```
products ─┬─ hasMany → transactions
          └─ belongsTo → suppliers

transactions ─┬─ belongsTo → products
              └─ belongsTo → customers

employees ─── hasMany → attendances
```

---

## Setup & Instalasi

### Requirements
- PHP 8.1 atau lebih baru
- Composer
- Node.js 18+
- MySQL 8.0
- Git

### Setup Backend (Laravel)

```bash
# Clone repository
git clone https://github.com/Erlina130104/proyek-portofolio-app.git
cd proyek-portofolio-app/backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Setup database di .env
# DB_DATABASE=smarterp
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed

# Start development server
php artisan serve
```

Backend akan jalan di `http://localhost:8000`

### Setup Frontend (Vue.js)

```bash
cd frontend

# Install dependencies
npm install

# Setup API base URL di .env
# VITE_API_URL=http://localhost:8000/api

# Start development server
npm run dev
```

Frontend akan jalan di `http://localhost:5173`

---

## Logika Bisnis

### Perhitungan Transaksi

**Untuk Penjualan:**
```php
$total = $product->sale_price * $quantity;
$profit = ($product->sale_price - $product->purchase_price) * $quantity;
```

**Untuk Rental:**
```php
$total = $product->rental_price_per_day * $quantity * $rental_days;
```

### Stock Management

```php
// Saat create transaksi
$product->decrement('stock', $quantity);

// Saat cancel transaksi
$product->increment('stock', $quantity);

// Low stock alert
if ($product->stock <= 10) {
    // Trigger notification
    Notification::lowStock($product);
}
```

### Kalkulasi Margin

```php
$margin_amount = $sale_price - $purchase_price;
$margin_percentage = ($margin_amount / $sale_price) * 100;
```

---

## Tantangan & Solusi

### Challenge #1: Dual Business Model
**Problem:** Gimana caranya handle penjualan dan rental dalam satu sistem tanpa bikin kode jadi berantakan?

**Solution:** Pakai polymorphic approach dengan field `type` di transactions table. Logic untuk pricing dan stock movement di-handle di Service Layer dengan strategy pattern sederhana.

```php
class TransactionService {
    public function calculateTotal($type, $product, $quantity, $days = null) {
        return match($type) {
            'sale' => $product->sale_price * $quantity,
            'rental' => $product->rental_price_per_day * $quantity * $days,
        };
    }
}
```

### Challenge #2: Stock Consistency
**Problem:** Gimana ensure stok tetap konsisten pas ada concurrent transactions?

**Solution:** Pakai database transactions dengan pessimistic locking (`lockForUpdate()`). Jadi pas ada yang lagi process transaksi, row product-nya di-lock sampe transaksi selesai.

### Challenge #3: Performance untuk Dashboard
**Problem:** Dashboard harus load cepat meskipun harus aggregate data dari banyak tabel.

**Solution:** 
- Pakai eager loading untuk minimize N+1 queries
- Index di kolom yang sering di-query (date, status, etc)
- Cache metrics yang jarang berubah pakai Laravel Cache

```php
// Example: Eager loading dengan cache
$metrics = Cache::remember('dashboard_metrics', 300, function() {
    return [
        'revenue' => Transaction::sum('total'),
        'products' => Product::with('category')->get(),
        // ... metrics lainnya
    ];
});
```

---

## Best Practices yang Diterapkan

### 1. Repository Pattern
Pisahin logic database dari business logic:

```php
// Repository
class ProductRepository {
    public function findWithLowStock($threshold = 10) {
        return Product::where('stock', '<=', $threshold)->get();
    }
}

// Controller
class ProductController {
    public function lowStock(ProductRepository $repo) {
        return response()->json($repo->findWithLowStock());
    }
}
```

### 2. Service Layer
Business logic yang kompleks dipisah ke Service classes:

```php
class TransactionService {
    public function createTransaction($data) {
        DB::transaction(function() use ($data) {
            // Complex business logic here
        });
    }
}
```

### 3. Request Validation
Semua input di-validate pakai Form Request:

```php
class StoreProductRequest extends FormRequest {
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'stock' => 'required|integer|min:0',
            // ... rules lainnya
        ];
    }
}
```

### 4. Error Handling
Custom exception untuk handle business errors:

```php
class InsufficientStockException extends Exception {
    public function render() {
        return response()->json([
            'success' => false,
            'message' => 'Stok tidak mencukupi'
        ], 400);
    }
}
```

---

## Testing (Manual)

Saya udah test secara manual berbagai skenario:

**Functional Testing:**
- ✅ CRUD operations untuk semua modul
- ✅ Transaction flow (create, view, cancel)
- ✅ Stock update consistency
- ✅ Authentication & authorization
- ✅ Form validation (valid & invalid inputs)

**Edge Cases:**
- ✅ Transaksi pas stok = 0
- ✅ Concurrent transactions ke produk yang sama
- ✅ Cancel transaksi dan rollback stok
- ✅ Invalid JWT token handling
- ✅ Large dataset pagination

**Performance:**
- ✅ Dashboard load time: < 2 detik
- ✅ API response: rata-rata < 300ms
- ✅ Database queries optimized (no N+1)

---

## Deployment Ready

Sistem ini udah siap untuk di-deploy dengan beberapa adjustment:

### Environment Configuration
- Production `.env` dengan config yang proper
- Database credentials yang secure
- JWT secret yang strong
- Debug mode off

### Optimization
- Route caching: `php artisan route:cache`
- Config caching: `php artisan config:cache`
- View caching: `php artisan view:cache`
- Frontend build: `npm run build`

### Security Checklist
- ✅ Input sanitization
- ✅ SQL injection prevention (via Eloquent)
- ✅ XSS protection
- ✅ CSRF token
- ✅ Password hashing (bcrypt)
- ✅ JWT token expiration
- ✅ Rate limiting

---

## Roadmap (Rencana Pengembangan)

### Short Term
- [ ] Export laporan ke Excel/PDF
- [ ] Email notification untuk low stock
- [ ] Advanced filtering di transaction history
- [ ] Customer loyalty points system

### Long Term
- [ ] Mobile app (React Native)
- [ ] Barcode scanning untuk inventory
- [ ] Integration payment gateway
- [ ] Multi-warehouse support
- [ ] WhatsApp notification integration

---

## Pembelajaran dari Proyek Ini

**Technical Skills:**
- Cara design database yang scalable
- Implementasi REST API yang proper dengan Laravel
- Handle complex business logic dengan Service Layer
- Performance optimization untuk query database
- Frontend-backend integration dengan JWT

**Soft Skills:**
- Problem solving untuk business requirements yang kompleks
- Time management dalam development solo project
- Documentation yang jelas dan lengkap

**Yang Bisa Diperbaiki:**
- Automated testing (unit test & integration test)
- CI/CD pipeline untuk deployment otomatis
- Monitoring dan logging yang lebih baik
- Caching strategy yang lebih advanced

---

## Kontak

Kalau ada pertanyaan teknis atau mau diskusi tentang proyek ini, feel free to reach out:

**Email:** erlinanainggolan130104@gmail.com  
**GitHub:** [github.com/Erlina130104](https://github.com/Erlina130104/proyek-portofolio-app)  
**Phone:** +62 897-1798-041

---

## Catatan

Proyek ini saya develop sebagai learning project dan portfolio. Kalau ada feedback atau saran improvement, saya sangat terbuka untuk diskusi!

**Last Updated:** Oktober 2024

---

**Built with ❤️ using Laravel & Vue.js**