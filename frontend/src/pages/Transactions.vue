<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 md:p-8">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
        </div>
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-slate-800">
            Transaksi Kostum
          </h1>
          <p class="text-slate-500 text-sm mt-1">Kelola penjualan dan penyewaan kostum</p>
        </div>
      </div>
    </div>

    <!-- Success/Error Notifications -->
    <transition name="fade">
      <div v-if="showSuccess" class="max-w-7xl mx-auto mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
        <p class="text-green-800 font-medium">✓ {{ successMessage }}</p>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="showError" class="max-w-7xl mx-auto mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md">
        <p class="text-red-800 font-medium">✗ {{ errorMessage }}</p>
      </div>
    </transition>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto">
      <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
        <p class="text-slate-600">Memuat transaksi...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Stats Cards -->
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 text-sm font-medium">Total Pendapatan</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">
                Rp {{ formatPrice(stats.grandTotal) }}
              </p>
              <p class="text-xs text-green-600 mt-1">+12% dari bulan lalu</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 text-sm font-medium">Total Transaksi</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">{{ stats.totalTransactions }}</p>
              <p class="text-xs text-slate-500 mt-1">{{ stats.salesCount }} penjualan, {{ stats.rentalCount }} sewa</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
              </svg>
            </div>
          </div>
        </div>

        <!-- Items Sold -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 text-sm font-medium">Kostum Terjual</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">{{ stats.totalItems }} pcs</p>
              <p class="text-xs text-slate-500 mt-1">Berbagai kategori</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Avg Transaction -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 text-sm font-medium">Rata-rata Transaksi</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">
                Rp {{ formatPrice(Math.round(stats.avgTransaction)) }}
              </p>
              <p class="text-xs text-slate-500 mt-1">Per transaksi</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Transaction Form -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200 sticky top-4">
            <div class="flex items-center gap-2 mb-6">
              <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </div>
              <h2 class="text-xl font-bold text-slate-800">Transaksi Baru</h2>
            </div>

            <form @submit.prevent="addTransaction" class="space-y-5">
              <!-- Transaction Type -->
              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Jenis Transaksi
                </label>
                <select
                  v-model="newTransaction.transaction_type"
                  required
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                >
                  <option value="sale">Penjualan (Beli)</option>
                  <option value="rental">Penyewaan</option>
                </select>
              </div>

              <!-- Product Selection -->
              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Pilih Kostum
                </label>
                <select
                  v-model="newTransaction.product_id"
                  required
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                >
                  <option value="">-- Pilih Kostum --</option>
                  <option
                    v-for="product in availableProducts"
                    :key="product.id"
                    :value="product.id"
                  >
                    {{ product.name }} - {{ product.category }} (Stok: {{ product.stock }})
                  </option>
                </select>
              </div>

              <!-- Product Info Display -->
              <div v-if="selectedProduct" class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl p-4 border border-slate-200">
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-slate-600">Material:</span>
                    <span class="font-semibold text-slate-800">{{ selectedProduct.material }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-slate-600">Size:</span>
                    <span class="font-semibold text-slate-800">{{ selectedProduct.size }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-slate-600">Warna:</span>
                    <span class="font-semibold text-slate-800">{{ selectedProduct.color }}</span>
                  </div>
                  <div class="flex justify-between border-t border-slate-200 pt-2">
                    <span class="text-slate-600">Harga {{ newTransaction.transaction_type === 'rental' ? 'Sewa/Hari' : 'Jual' }}:</span>
                    <span class="font-bold text-indigo-600">Rp {{ formatPrice(getCurrentPrice()) }}</span>
                  </div>
                </div>
              </div>

              <!-- Customer Name -->
              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Nama Pelanggan
                </label>
                <input
                  type="text"
                  v-model="newTransaction.customer_name"
                  required
                  placeholder="Masukkan nama pelanggan"
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                />
              </div>

              <!-- Quantity -->
              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Jumlah
                </label>
                <input
                  type="number"
                  v-model.number="newTransaction.quantity"
                  min="1"
                  :max="selectedProduct ? selectedProduct.stock : 9999"
                  required
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                />
                <p v-if="selectedProduct" class="text-xs text-slate-500 mt-1">
                  Stok tersedia: {{ selectedProduct.stock }} pcs
                </p>
              </div>

              <!-- Rental Days (only for rental) -->
              <div v-if="newTransaction.transaction_type === 'rental'">
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Durasi Sewa (Hari)
                </label>
                <input
                  type="number"
                  v-model.number="newTransaction.rental_days"
                  min="1"
                  required
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                />
              </div>

              <!-- Payment Method -->
              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Metode Pembayaran
                </label>
                <select
                  v-model="newTransaction.payment_method"
                  required
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                >
                  <option value="">-- Pilih Pembayaran --</option>
                  <option value="cash">Tunai</option>
                  <option value="transfer">Transfer Bank</option>
                  <option value="card">Kartu Kredit</option>
                  <option value="e-wallet">E-Wallet (GoPay, OVO, Dana)</option>
                </select>
              </div>

              <!-- Transaction Total -->
              <div v-if="selectedProduct" class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-200">
                <p class="text-sm text-slate-600 mb-1">Total Transaksi</p>
                <p class="text-2xl font-bold text-indigo-600">
                  Rp {{ formatPrice(getTransactionTotal()) }}
                </p>
                <p v-if="newTransaction.transaction_type === 'rental'" class="text-xs text-slate-500 mt-1">
                  {{ newTransaction.rental_days }} hari × Rp {{ formatPrice(getCurrentPrice()) }}
                </p>
              </div>

              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="submitting"
                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="!submitting" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span v-if="submitting">Memproses...</span>
                <span v-else>Tambah Transaksi</span>
              </button>
            </form>
          </div>
        </div>

        <!-- Transaction Records -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                  </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">Riwayat Transaksi</h2>
              </div>
              <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ transactions.length }} transaksi
              </span>
            </div>

            <!-- Empty State -->
            <div v-if="transactions.length === 0" class="text-center py-16">
              <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="9" cy="21" r="1"></circle>
                  <circle cx="20" cy="21" r="1"></circle>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
              </div>
              <p class="text-slate-400 font-medium">Belum ada transaksi</p>
              <p class="text-slate-400 text-sm mt-1">Mulai dengan menambahkan transaksi pertama</p>
            </div>

            <!-- Transaction List -->
            <div v-else class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
              <div
                v-for="txn in transactions"
                :key="txn.id"
                class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl p-5 border border-slate-200 hover:shadow-md transition-all duration-300 group"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <!-- Header -->
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                      <h3 class="text-lg font-bold text-slate-800">{{ txn.product_name }}</h3>
                      <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', 
                        txn.transaction_type === 'rental' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700']">
                        {{ txn.transaction_type === 'rental' ? 'Sewa' : 'Jual' }}
                      </span>
                      <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ txn.payment_method }}
                      </span>
                    </div>
                    
                    <!-- Customer Info -->
                    <div class="mb-3">
                      <p class="text-sm text-slate-600">
                        <span class="font-medium">Pelanggan:</span> {{ txn.customer_name }}
                      </p>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm mb-3">
                      <div>
                        <p class="text-slate-500">Jumlah</p>
                        <p class="font-semibold text-slate-700">{{ txn.quantity }} pcs</p>
                      </div>
                      <div v-if="txn.transaction_type === 'rental'">
                        <p class="text-slate-500">Durasi</p>
                        <p class="font-semibold text-slate-700">{{ txn.rental_days }} hari</p>
                      </div>
                      <div>
                        <p class="text-slate-500">Harga Satuan</p>
                        <p class="font-semibold text-slate-700">Rp {{ formatPrice(txn.unit_price) }}</p>
                      </div>
                    </div>

                    <!-- Footer -->
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                      <div class="flex items-center gap-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                          </svg>
                          {{ formatDate(txn.transaction_date) }}
                        </span>
                      </div>
                      <div class="text-right">
                        <p class="text-xs text-slate-500">Total</p>
                        <p class="text-xl font-bold text-indigo-600">
                          Rp {{ formatPrice(txn.total_amount) }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Delete Button -->
                  <button
                    @click="confirmDelete(txn)"
                    class="ml-3 w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="18" y1="6" x2="6" y2="18"></line>
                      <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Grand Total -->
            <div v-if="transactions.length > 0" class="mt-6 pt-6 border-t-2 border-slate-200">
              <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-indigo-100 text-sm font-medium mb-1">Total Pendapatan</p>
                    <p class="text-3xl font-bold">Rp {{ formatPrice(stats.grandTotal) }}</p>
                    <p class="text-indigo-100 text-xs mt-1">Dari {{ transactions.length }} transaksi</p>
                  </div>
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">Hapus Transaksi</h3>
          <p class="text-slate-600 mb-6">
            Apakah Anda yakin ingin menghapus transaksi ini? Stok produk akan dikembalikan. Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex gap-3">
            <button
              @click="deleteTransaction"
              class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-colors"
            >
              Hapus
            </button>
            <button
              @click="showDeleteModal = false"
              class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold py-2 rounded-lg transition-colors"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TransactionsCostume",
  
  data() {
    return {
      products: [],
      transactions: [],
      newTransaction: {
        product_id: "",
        customer_name: "",
        quantity: 1,
        payment_method: "",
        transaction_type: "sale",
        rental_days: 1
      },
      loading: false,
      submitting: false,
      showSuccess: false,
      showError: false,
      successMessage: "",
      errorMessage: "",
      showDeleteModal: false,
      transactionToDelete: null
    };
  },
  
  computed: {
    /**
     * Filter products that are active and have stock
     */
    availableProducts() {
      return this.products.filter(p => p.status === 'active' && p.stock > 0);
    },
    
    /**
     * Get currently selected product
     */
    selectedProduct() {
      return this.products.find(p => p.id === this.newTransaction.product_id);
    },
    
    /**
     * Calculate transaction statistics
     */
    stats() {
      const total = this.transactions.reduce((sum, t) => sum + parseFloat(t.total_amount), 0);
      const totalItems = this.transactions.reduce((sum, t) => sum + parseInt(t.quantity), 0);
      const avgTransaction = this.transactions.length > 0 ? total / this.transactions.length : 0;
      
      const salesCount = this.transactions.filter(t => t.transaction_type === 'sale').length;
      const rentalCount = this.transactions.filter(t => t.transaction_type === 'rental').length;
      
      return {
        grandTotal: total,
        totalTransactions: this.transactions.length,
        totalItems,
        avgTransaction,
        salesCount,
        rentalCount
      };
    }
  },
  
  methods: {
    /**
     * Fetch products from API or localStorage
     */
    async fetchProducts() {
      try {
        const localData = localStorage.getItem('productsData');
        if (localData) {
          this.products = JSON.parse(localData);
          console.log('✅ Loaded', this.products.length, 'costume products');
          return;
        }

        // If no localStorage, try API
        const response = await fetch('/api/products');
        if (!response.ok) throw new Error('API not available');
        
        const data = await response.json();
        this.products = Array.isArray(data) ? data : (data.data || []);
        localStorage.setItem('productsData', JSON.stringify(this.products));
      } catch (error) {
        console.log('⚠️ Products not found, loading sample data');
        this.loadSampleProducts();
      }
    },
    
    /**
     * Fetch transactions from API or localStorage
     */
    async fetchTransactions() {
      this.loading = true;
      try {
        const localData = localStorage.getItem('transactionsData');
        if (localData) {
          this.transactions = JSON.parse(localData);
          console.log('✅ Loaded', this.transactions.length, 'transactions');
          this.loading = false;
          return;
        }

        // If no localStorage, try API
        const response = await fetch('/api/transactions');
        if (!response.ok) throw new Error('API not available');
        
        const data = await response.json();
        this.transactions = Array.isArray(data) ? data : (data.data || []);
        localStorage.setItem('transactionsData', JSON.stringify(this.transactions));
      } catch (error) {
        console.log('ℹ️ No transactions yet');
        this.transactions = [];
      } finally {
        this.loading = false;
      }
    },
    
    /**
     * Load sample costume products if not available
     */
    loadSampleProducts() {
      this.products = [
        {
          id: 1,
          name: "Kebaya Modern Premium",
          price: 2500000,
          rental_price: 350000,
          stock: 8,
          category: "Traditional Costume",
          material: "Sutra",
          size: "M",
          color: "Merah Maroon",
          status: "active"
        },
        {
          id: 2,
          name: "Kostum Superhero Spider-Kid",
          price: 450000,
          rental_price: 75000,
          stock: 15,
          category: "Kids Costume",
          material: "Spandex",
          size: "Kids L",
          color: "Merah Biru",
          status: "active"
        },
        {
          id: 3,
          name: "Baju Adat Bali Complete Set",
          price: 3500000,
          rental_price: 500000,
          stock: 5,
          category: "Performance Costume",
          material: "Katun",
          size: "M",
          color: "Gold & White",
          status: "active"
        }
      ];
      localStorage.setItem('productsData', JSON.stringify(this.products));
      console.log('✅ Loaded sample costume products');
    },
    
    /**
     * Get current price based on transaction type
     */
    getCurrentPrice() {
      if (!this.selectedProduct) return 0;
      
      if (this.newTransaction.transaction_type === 'rental') {
        return this.selectedProduct.rental_price || 0;
      }
      return this.selectedProduct.price || 0;
    },
    
    /**
     * Calculate transaction total
     */
    getTransactionTotal() {
      if (!this.selectedProduct) return 0;
      
      const price = this.getCurrentPrice();
      const quantity = this.newTransaction.quantity;
      
      if (this.newTransaction.transaction_type === 'rental') {
        const days = this.newTransaction.rental_days || 1;
        return price * quantity * days;
      }
      
      return price * quantity;
    },
    
    /**
     * Add new transaction
     * In production: await axios.post('/api/transactions', data)
     */
    async addTransaction() {
      if (!this.selectedProduct) {
        this.showErrorMessage('Silakan pilih kostum');
        return;
      }
      
      if (this.newTransaction.quantity > this.selectedProduct.stock) {
        this.showErrorMessage('Stok tidak mencukupi');
        return;
      }
      
      if (this.newTransaction.transaction_type === 'rental' && !this.newTransaction.rental_days) {
        this.showErrorMessage('Silakan masukkan durasi sewa');
        return;
      }
      
      this.submitting = true;
      
      try {
        const transactionData = {
          product_id: this.newTransaction.product_id,
          product_name: this.selectedProduct.name,
          customer_name: this.newTransaction.customer_name,
          quantity: this.newTransaction.quantity,
          unit_price: this.getCurrentPrice(),
          total_amount: this.getTransactionTotal(),
          payment_method: this.newTransaction.payment_method,
          transaction_type: this.newTransaction.transaction_type,
          rental_days: this.newTransaction.transaction_type === 'rental' ? this.newTransaction.rental_days : null,
          transaction_date: new Date().toISOString().split('T')[0],
          created_at: new Date().toISOString()
        };
        
        try {
          // Try API call
          const response = await fetch('/api/transactions', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify(transactionData)
          });
          
          if (!response.ok) throw new Error('API not available');
          
          const data = await response.json();
          this.transactions.unshift(data.data || data);
          
        } catch (apiError) {
          // Fallback to localStorage
          console.log('📦 Using localStorage for transaction');
          const newTransaction = {
            id: Date.now(),
            ...transactionData
          };
          
          this.transactions.unshift(newTransaction);
          localStorage.setItem('transactionsData', JSON.stringify(this.transactions));
        }
        
        // Update product stock
        await this.updateProductStock(this.newTransaction.product_id, -this.newTransaction.quantity);
        
        this.showSuccessMessage(`✅ Transaksi ${this.newTransaction.transaction_type === 'rental' ? 'sewa' : 'penjualan'} berhasil ditambahkan!`);
        this.resetForm();
        
      } catch (error) {
        console.error('❌ Error adding transaction:', error);
        this.showErrorMessage('Gagal menambahkan transaksi. Silakan coba lagi.');
      } finally {
        this.submitting = false;
      }
    },
    
    /**
     * Update product stock after transaction
     * In production: await axios.patch(`/api/products/${id}/stock`, { change })
     */
    async updateProductStock(productId, change) {
      try {
        // Try API
        await fetch(`/api/products/${productId}/stock`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ change })
        });
      } catch (apiError) {
        console.log('📦 Using localStorage for stock update');
      }
      
      // Update local products
      const product = this.products.find(p => p.id === productId);
      if (product) {
        product.stock += change;
        localStorage.setItem('productsData', JSON.stringify(this.products));
        
        // Trigger event for other components
        window.dispatchEvent(new Event('productsUpdated'));
        console.log('✅ Stock updated:', product.name, 'new stock:', product.stock);
      }
    },
    
    /**
     * Show delete confirmation modal
     */
    confirmDelete(transaction) {
      this.transactionToDelete = transaction;
      this.showDeleteModal = true;
    },
    
    /**
     * Delete transaction and restore stock
     * In production: await axios.delete(`/api/transactions/${id}`)
     */
    async deleteTransaction() {
      if (!this.transactionToDelete) return;
      
      try {
        try {
          // Try API
          await fetch(`/api/transactions/${this.transactionToDelete.id}`, {
            method: 'DELETE'
          });
        } catch (apiError) {
          console.log('📦 Using localStorage for delete');
        }
        
        // Restore stock
        await this.updateProductStock(
          this.transactionToDelete.product_id, 
          this.transactionToDelete.quantity
        );
        
        // Remove from list
        this.transactions = this.transactions.filter(t => t.id !== this.transactionToDelete.id);
        
        // Save to localStorage
        localStorage.setItem('transactionsData', JSON.stringify(this.transactions));
        
        this.showSuccessMessage('✅ Transaksi berhasil dihapus dan stok dikembalikan');
        this.showDeleteModal = false;
        this.transactionToDelete = null;
        
      } catch (error) {
        console.error('❌ Error deleting transaction:', error);
        this.showErrorMessage('Gagal menghapus transaksi');
      }
    },
    
    /**
     * Format price to Indonesian Rupiah
     */
    formatPrice(value) {
      return parseFloat(value).toLocaleString("id-ID");
    },
    
    /**
     * Format date to Indonesian format
     */
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString("id-ID", { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
      });
    },
    
    /**
     * Reset form to initial state
     */
    resetForm() {
      this.newTransaction = {
        product_id: "",
        customer_name: "",
        quantity: 1,
        payment_method: "",
        transaction_type: "sale",
        rental_days: 1
      };
    },
    
    /**
     * Show success notification
     */
    showSuccessMessage(message) {
      this.successMessage = message;
      this.showSuccess = true;
      setTimeout(() => {
        this.showSuccess = false;
      }, 3000);
    },
    
    /**
     * Show error notification
     */
    showErrorMessage(message) {
      this.errorMessage = message;
      this.showError = true;
      setTimeout(() => {
        this.showError = false;
      }, 3000);
    }
  },
  
  /**
   * Component lifecycle hooks
   */
  created() {
    this.fetchProducts();
    this.fetchTransactions();
  },
  
  mounted() {
    console.log('✅ Transactions component mounted');
    console.log('📊 Products available:', this.products.length);
    console.log('📊 Transactions loaded:', this.transactions.length);
  }
};
</script>

<style scoped>
/* Animations */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Custom Scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Hover Animation */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group:hover {
  animation: slideIn 0.3s ease-out;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .text-3xl {
    font-size: 1.5rem;
  }
  
  .text-2xl {
    font-size: 1.25rem;
  }
}
</style>
