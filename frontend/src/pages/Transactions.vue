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
            Transactions Dashboard
          </h1>
          <p class="text-slate-500 text-sm mt-1">Monitor and manage all your business transactions</p>
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
        <p class="text-slate-600">Loading transactions...</p>
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
              <p class="text-slate-500 text-sm font-medium">Total Revenue</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">
                Rp {{ formatPrice(stats.grandTotal) }}
              </p>
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
              <p class="text-slate-500 text-sm font-medium">Total Transactions</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">{{ stats.totalTransactions }}</p>
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
              <p class="text-slate-500 text-sm font-medium">Items Sold</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">{{ stats.totalItems }}</p>
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
              <p class="text-slate-500 text-sm font-medium">Avg Transaction</p>
              <p class="text-2xl font-bold text-slate-800 mt-1">
                Rp {{ formatPrice(Math.round(stats.avgTransaction)) }}
              </p>
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
              <h2 class="text-xl font-bold text-slate-800">New Transaction</h2>
            </div>

            <form @submit.prevent="addTransaction" class="space-y-5">
              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Select Product
                </label>
                <select
                  v-model="newTransaction.product_id"
                  required
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                >
                  <option value="">-- Choose Product --</option>
                  <option
                    v-for="product in availableProducts"
                    :key="product.id"
                    :value="product.id"
                  >
                    {{ product.name }} - Rp {{ formatPrice(product.price) }} (Stock: {{ product.stock }})
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Customer Name
                </label>
                <input
                  type="text"
                  v-model="newTransaction.customer_name"
                  required
                  placeholder="Enter customer name"
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                />
              </div>

              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Quantity
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
                  Available stock: {{ selectedProduct.stock }} units
                </p>
              </div>

              <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2">
                  Payment Method
                </label>
                <select
                  v-model="newTransaction.payment_method"
                  required
                  class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-700"
                >
                  <option value="">-- Choose Payment --</option>
                  <option value="cash">Cash</option>
                  <option value="transfer">Bank Transfer</option>
                  <option value="card">Credit Card</option>
                  <option value="e-wallet">E-Wallet</option>
                </select>
              </div>

              <div v-if="selectedProduct" class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-200">
                <p class="text-sm text-slate-600 mb-1">Transaction Total</p>
                <p class="text-2xl font-bold text-indigo-600">
                  Rp {{ formatPrice(getTransactionTotal()) }}
                </p>
              </div>

              <button
                type="submit"
                :disabled="submitting"
                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="!submitting" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span v-if="submitting">Processing...</span>
                <span v-else>Add Transaction</span>
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
                <h2 class="text-xl font-bold text-slate-800">Transaction History</h2>
              </div>
              <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ transactions.length }} records
              </span>
            </div>

            <div v-if="transactions.length === 0" class="text-center py-16">
              <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="9" cy="21" r="1"></circle>
                  <circle cx="20" cy="21" r="1"></circle>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
              </div>
              <p class="text-slate-400 font-medium">No transactions yet</p>
              <p class="text-slate-400 text-sm mt-1">Start by adding your first transaction</p>
            </div>

            <div v-else class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
              <div
                v-for="txn in transactions"
                :key="txn.id"
                class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl p-5 border border-slate-200 hover:shadow-md transition-all duration-300 group"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <h3 class="text-lg font-bold text-slate-800">{{ txn.product_name }}</h3>
                      <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        {{ txn.payment_method }}
                      </span>
                    </div>
                    
                    <div class="mb-2">
                      <p class="text-sm text-slate-600">
                        <span class="font-medium">Customer:</span> {{ txn.customer_name }}
                      </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                      <div>
                        <p class="text-slate-500">Quantity</p>
                        <p class="font-semibold text-slate-700">{{ txn.quantity }} pcs</p>
                      </div>
                      <div>
                        <p class="text-slate-500">Unit Price</p>
                        <p class="font-semibold text-slate-700">Rp {{ formatPrice(txn.unit_price) }}</p>
                      </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between">
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
                    <p class="text-indigo-100 text-sm font-medium mb-1">Grand Total Revenue</p>
                    <p class="text-3xl font-bold">Rp {{ formatPrice(stats.grandTotal) }}</p>
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
          <h3 class="text-xl font-bold text-slate-800 mb-2">Delete Transaction</h3>
          <p class="text-slate-600 mb-6">
            Are you sure you want to delete this transaction? This action cannot be undone.
          </p>
          <div class="flex gap-3">
            <button
              @click="deleteTransaction"
              class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-colors"
            >
              Delete
            </button>
            <button
              @click="showDeleteModal = false"
              class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold py-2 rounded-lg transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Transactions",
  data() {
    return {
      products: [],
      transactions: [],
      newTransaction: {
        product_id: "",
        customer_name: "",
        quantity: 1,
        payment_method: ""
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
    availableProducts() {
      return this.products.filter(p => p.status === 'active' && p.stock > 0);
    },
    selectedProduct() {
      return this.products.find(p => p.id === this.newTransaction.product_id);
    },
    stats() {
      const total = this.transactions.reduce((sum, t) => sum + parseFloat(t.total_amount), 0);
      const totalItems = this.transactions.reduce((sum, t) => sum + parseInt(t.quantity), 0);
      const avgTransaction = this.transactions.length > 0 ? total / this.transactions.length : 0;
      
      return {
        grandTotal: total,
        totalTransactions: this.transactions.length,
        totalItems,
        avgTransaction
      };
    }
  },
  methods: {
    async fetchProducts() {
      try {
        const localData = localStorage.getItem('productsData');
        if (localData) {
          this.products = JSON.parse(localData);
          return;
        }

        const response = await fetch('/api/products');
        if (!response.ok) throw new Error('Failed to fetch products');
        
        const data = await response.json();
        this.products = Array.isArray(data) ? data : (data.data || []);
        localStorage.setItem('productsData', JSON.stringify(this.products));
      } catch (error) {
        console.error('Error fetching products:', error);
        this.loadSampleProducts();
      }
    },
    
    async fetchTransactions() {
      this.loading = true;
      try {
        const localData = localStorage.getItem('transactionsData');
        if (localData) {
          this.transactions = JSON.parse(localData);
          this.loading = false;
          return;
        }

        const response = await fetch('/api/transactions');
        if (!response.ok) throw new Error('Failed to fetch transactions');
        
        const data = await response.json();
        this.transactions = Array.isArray(data) ? data : (data.data || []);
        localStorage.setItem('transactionsData', JSON.stringify(this.transactions));
      } catch (error) {
        console.error('Error fetching transactions:', error);
        this.transactions = [];
      } finally {
        this.loading = false;
      }
    },
    
    loadSampleProducts() {
      this.products = [
        { id: 1, name: "Laptop Gaming ROG", price: 15000000, stock: 5, category: "Elektronik", status: "active" },
        { id: 2, name: "Kemeja Batik Premium", price: 350000, stock: 25, category: "Fashion", status: "active" },
        { id: 3, name: "Kopi Arabica Premium", price: 85000, stock: 8, category: "Makanan", status: "active" },
        { id: 4, name: "Meja Kerja Minimalis", price: 1250000, stock: 15, category: "Furniture", status: "active" },
        { id: 5, name: "Set Alat Tulis Premium", price: 250000, stock: 30, category: "Alat Tulis", status: "active" }
      ];
      localStorage.setItem('productsData', JSON.stringify(this.products));
      console.log('Using sample products data');
    },
    
    async addTransaction() {
      if (!this.selectedProduct) return;
      
      if (this.newTransaction.quantity > this.selectedProduct.stock) {
        this.showErrorMessage('Insufficient stock available');
        return;
      }
      
      this.submitting = true;
      
      try {
        const transactionData = {
          product_id: this.newTransaction.product_id,
          customer_name: this.newTransaction.customer_name,
          quantity: this.newTransaction.quantity,
          unit_price: this.selectedProduct.price,
          total_amount: this.getTransactionTotal(),
          payment_method: this.newTransaction.payment_method,
          transaction_date: new Date().toISOString().split('T')[0]
        };
        
        try {
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
          await this.updateProductStock(this.newTransaction.product_id, -this.newTransaction.quantity);
          
        } catch (apiError) {
          console.log('API not available, using local storage');
          const newTransaction = {
            id: Date.now(),
            product_id: transactionData.product_id,
            product_name: this.selectedProduct.name,
            customer_name: transactionData.customer_name,
            quantity: transactionData.quantity,
            unit_price: transactionData.unit_price,
            total_amount: transactionData.total_amount,
            payment_method: transactionData.payment_method,
            transaction_date: transactionData.transaction_date,
            created_at: new Date().toISOString()
          };
          
          this.transactions.unshift(newTransaction);
          localStorage.setItem('transactionsData', JSON.stringify(this.transactions));
          
          const productIndex = this.products.findIndex(p => p.id === this.newTransaction.product_id);
          if (productIndex !== -1) {
            this.products[productIndex].stock -= this.newTransaction.quantity;
            localStorage.setItem('productsData', JSON.stringify(this.products));
          }
        }
        
        this.showSuccessMessage('Transaction added successfully!');
        this.resetForm();
        
      } catch (error) {
        console.error('Error adding transaction:', error);
        this.showErrorMessage('Failed to add transaction. Please try again.');
      } finally {
        this.submitting = false;
      }
    },
    
    async updateProductStock(productId, change) {
      try {
        await fetch(`/api/products/${productId}/stock`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ change })
        });
        
        const product = this.products.find(p => p.id === productId);
        if (product) {
          product.stock += change;
          localStorage.setItem('productsData', JSON.stringify(this.products));
        }
      } catch (error) {
        console.error('Error updating stock:', error);
      }
    },
    
    confirmDelete(transaction) {
      this.transactionToDelete = transaction;
      this.showDeleteModal = true;
    },
    
    async deleteTransaction() {
      if (!this.transactionToDelete) return;
      
      try {
        try {
          await fetch(`/api/transactions/${this.transactionToDelete.id}`, {
            method: 'DELETE'
          });

          await this.updateProductStock(
            this.transactionToDelete.product_id, 
            this.transactionToDelete.quantity
          );
        } catch (apiError) {
          console.log('Using local delete');
          
          const product = this.products.find(p => p.id === this.transactionToDelete.product_id);
          if (product) {
            product.stock += this.transactionToDelete.quantity;
          }
        }
        
        this.transactions = this.transactions.filter(t => t.id !== this.transactionToDelete.id);
        
        localStorage.setItem('transactionsData', JSON.stringify(this.transactions));
        localStorage.setItem('productsData', JSON.stringify(this.products));
        
        this.showSuccessMessage('Transaction deleted successfully!');
        this.showDeleteModal = false;
        this.transactionToDelete = null;
        
      } catch (error) {
        console.error('Error deleting transaction:', error);
        this.showErrorMessage('Failed to delete transaction');
      }
    },
    
    getTransactionTotal() {
      if (!this.selectedProduct) return 0;
      return this.selectedProduct.price * this.newTransaction.quantity;
    },
    
    formatPrice(value) {
      return parseFloat(value).toLocaleString("id-ID");
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString("id-ID", { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
      });
    },
    
    resetForm() {
      this.newTransaction = {
        product_id: "",
        customer_name: "",
        quantity: 1,
        payment_method: ""
      };
    },
    
    showSuccessMessage(message) {
      this.successMessage = message;
      this.showSuccess = true;
      setTimeout(() => {
        this.showSuccess = false;
      }, 3000);
    },
    
    showErrorMessage(message) {
      this.errorMessage = message;
      this.showError = true;
      setTimeout(() => {
        this.showError = false;
      }, 3000);
    }
  },
  
  created() {
    this.fetchProducts();
    this.fetchTransactions();
  },
  
  mounted() {
    console.log('Transactions component mounted');
  }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

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
</style>