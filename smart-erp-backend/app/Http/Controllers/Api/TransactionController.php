<template>
  <!-- Container utama, background gradient biar cantik -->
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-cyan-50 to-blue-100 p-4 md:p-8">
    
    <!-- Wrapper konten, max-w-7xl biar ga terlalu lebar di layar gede -->
    <div class="max-w-7xl mx-auto space-y-8">
      
      <!-- Header bagian atas -->
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Transactions</h1>
        
        <!-- Tombol buat munculin/sembunyiin form, pake toggle -->
        <button
          @click="showAddForm = !showAddForm"
          class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-2xl shadow-md transition-all"
        >
          <i class="fas fa-plus"></i> Add Transaction
        </button>
      </div>

      <!-- Form tambah transaksi, cuma muncul kalo showAddForm = true -->
      <div v-if="showAddForm" class="bg-white rounded-2xl p-6 shadow-md space-y-4 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-700">Add New Transaction</h2>

        <!-- Grid 2 kolom di desktop, 1 kolom di mobile -->
        <div class="grid md:grid-cols-2 gap-4">
          
          <!-- Dropdown pilih produk -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Product</label>
            
            <!-- v-model buat binding data 2 arah -->
            <select
              v-model="form.product_id"
              class="w-full border border-gray-300 rounded-xl p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            >
              <option value="">-- Select Product --</option>
              
              <!-- Loop products buat isi dropdown -->
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }}
              </option>
            </select>
          </div>

          <!-- Input jumlah quantity -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Quantity</label>
            <input
              type="number"
              v-model="form.quantity"
              min="1"
              class="w-full border border-gray-300 rounded-xl p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            />
          </div>
        </div>

        <!-- Tombol cancel & save -->
        <div class="flex justify-end gap-3 mt-4">
          <button
            @click="resetForm"
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-xl hover:bg-gray-400 transition-all"
          >
            Cancel
          </button>
          <button
            @click="createTransaction"
            class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all"
          >
            Save
          </button>
        </div>
      </div>

      <!-- Tabel list transaksi -->
      <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Transaction List</h2>

        <!-- Loading state pas lagi fetch data -->
        <div v-if="loading" class="text-center text-gray-500 py-4">Loading...</div>

        <!-- Tabel muncul pas loading udah selesai -->
        <table v-else class="w-full border-collapse text-sm text-left">
          <thead>
            <tr class="bg-blue-100 text-gray-700">
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Product</th>
              <th class="px-4 py-2">Quantity</th>
              <th class="px-4 py-2">Date</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loop tiap transaksi, index buat nomor urut -->
            <tr v-for="(trx, index) in transactions" :key="trx.id" class="hover:bg-blue-50">
              <td class="px-4 py-2">{{ index + 1 }}</td>
              
              <!-- Pake optional chaining (?.) biar ga error kalo product null -->
              <td class="px-4 py-2">{{ trx.product?.name || '-' }}</td>
              
              <td class="px-4 py-2">{{ trx.quantity }}</td>
              
              <!-- Format tanggal biar lebih enak dibaca -->
              <td class="px-4 py-2">{{ formatDate(trx.created_at) }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Kalo data kosong, kasih tau user -->
        <div v-if="transactions.length === 0 && !loading" class="text-center text-gray-500 py-4">
          No transactions found.
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "TransactionPage",
  
  data() {
    return {
      products: [],         // List produk buat dropdown
      transactions: [],     // List transaksi buat tabel
      loading: false,       // Status loading
      showAddForm: false,   // Toggle buat munculin form
      form: {               // Data form
        product_id: "",
        quantity: 1,
      },
    };
  },
  
  methods: {
    // Ambil list produk dari API
    async fetchProducts() {
      try {
        const res = await axios.get("http://localhost:8000/api/v1/products");
        this.products = res.data.data || res.data;
      } catch (err) {
        console.error("Failed to fetch products:", err);
      }
    },

    // Ambil list transaksi dari API
    async fetchTransactions() {
      try {
        this.loading = true;
        const res = await axios.get("http://localhost:8000/api/v1/transactions");
        this.transactions = res.data.data || res.data;
      } catch (err) {
        console.error("Failed to fetch transactions:", err);
      } finally {
        this.loading = false;
      }
    },

    // Kirim data transaksi baru ke API
    async createTransaction() {
      // Validasi dulu sebelum kirim
      if (!this.form.product_id || !this.form.quantity) {
        alert("Please select a product and enter quantity.");
        return;
      }
      
      try {
        await axios.post("http://localhost:8000/api/v1/transactions", this.form);
        alert("Transaction added successfully!");
        this.resetForm();
        this.fetchTransactions(); // Refresh data
      } catch (err) {
        console.error("Failed to create transaction:", err);
        alert("Failed to add transaction.");
      }
    },

    // Reset form ke kondisi awal
    resetForm() {
      this.form = { product_id: "", quantity: 1 };
      this.showAddForm = false;
    },

    // Format tanggal jadi lebih readable
    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleString("id-ID");
    },
  },
  
  // Pas komponen pertama kali dimuat, langsung fetch data
  mounted() {
    this.fetchProducts();
    this.fetchTransactions();
  },
};
</script>

<style scoped>
table th,
table td {
  border-bottom: 1px solid #e2e8f0;
}
</style>