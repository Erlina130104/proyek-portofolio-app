<template>
  <div class="products-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-left">
          <div class="icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <div class="header-text">
            <h1>Produk</h1>
            <p>Koleksi produk berkualitas tinggi</p>
          </div>
        </div>
        <button @click="openAddModal" class="btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Tambah Produk</span>
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="container">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-content">
            <div>
              <p class="stat-label">Total Products</p>
              <p class="stat-value">{{ stats.total }}</p>
            </div>
            <div class="stat-icon blue">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-content">
            <div>
              <p class="stat-label">Categories</p>
              <p class="stat-value">{{ stats.categories }}</p>
            </div>
            <div class="stat-icon purple">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-content">
            <div>
              <p class="stat-label">Low Stock</p>
              <p class="stat-value">{{ stats.lowStock }}</p>
            </div>
            <div class="stat-icon yellow">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-content">
            <div>
              <p class="stat-label">Filtered</p>
              <p class="stat-value">{{ filteredProducts.length }}</p>
            </div>
            <div class="stat-icon indigo">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="container">
      <div class="search-card">
        <div class="search-grid">
          <div class="search-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input v-model="searchQuery" type="text" placeholder="Cari produk...">
          </div>

          <div class="filter-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <select v-model="selectedCategory">
              <option value="">Semua Kategori</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="container">
      <div class="loading-state">
        <div class="spinner"></div>
        <p>Memuat produk...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="container">
      <div class="error-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3>Gagal memuat produk</h3>
        <p>{{ error }}</p>
        <button @click="fetchProducts" class="btn-error">Coba Lagi</button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredProducts.length === 0" class="container">
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <h3>Belum ada produk</h3>
        <p>Mulai dengan menambahkan produk pertama Anda</p>
        <button @click="openAddModal" class="btn-primary">Tambah Produk</button>
      </div>
    </div>

    <!-- Products Grid -->
    <div v-else class="container">
      <div class="products-grid">
        <div v-for="product in filteredProducts" :key="product.id" class="product-card">
          <div class="product-image">
            <img :src="product.image || 'https://via.placeholder.com/400x300?text=No+Image'" :alt="product.name">
            <span :class="['badge-status', product.status === 'active' ? 'active' : 'inactive']">
              {{ product.status === 'active' ? 'Aktif' : 'Nonaktif' }}
            </span>
            <span v-if="product.stock <= 10" class="badge-lowstock">Stok Rendah</span>
          </div>

          <div class="product-body">
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-description">{{ product.description }}</p>
            
            <div class="product-meta">
              <span class="badge-category">{{ product.category }}</span>
              <span class="product-sku">SKU: {{ product.sku }}</span>
            </div>

            <div class="product-pricing">
              <div>
                <p class="product-price">Rp {{ formatPrice(product.price) }}</p>
              </div>
              <div class="product-stock">
                <p class="stock-label">Stok</p>
                <p :class="['stock-value', product.stock <= 10 ? 'low' : '']">{{ product.stock }}</p>
              </div>
            </div>

            <div class="product-actions">
              <button @click="editProduct(product)" class="btn-edit">Edit</button>
              <button @click="confirmDelete(product)" class="btn-delete">Hapus</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ showAddModal ? 'Tambah Produk Baru' : 'Edit Produk' }}</h2>
          <button @click="closeModal" class="btn-close">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="modal-form">
          <div class="form-group">
            <label>Nama Produk</label>
            <input v-model="form.name" type="text" required placeholder="Masukkan nama produk">
          </div>

          <div class="form-group">
            <label>Deskripsi</label>
            <textarea v-model="form.description" rows="3" placeholder="Masukkan deskripsi produk"></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>SKU</label>
              <input v-model="form.sku" type="text" required placeholder="SKU-001">
            </div>

            <div class="form-group">
              <label>Kategori</label>
              <select v-model="form.category" required>
                <option value="">Pilih Kategori</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Harga (Rp)</label>
              <input v-model.number="form.price" type="number" step="0.01" required placeholder="100000">
            </div>

            <div class="form-group">
              <label>Stok</label>
              <input v-model.number="form.stock" type="number" required placeholder="50">
            </div>
          </div>

          <div class="form-group">
            <label>URL Gambar</label>
            <input v-model="form.image" type="text" placeholder="https://example.com/image.jpg">
          </div>

          <div class="form-group">
            <label>Status</label>
            <select v-model="form.status">
              <option value="active">Aktif</option>
              <option value="inactive">Nonaktif</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-submit">
              {{ showAddModal ? 'Tambah Produk' : 'Simpan Perubahan' }}
            </button>
            <button type="button" @click="closeModal" class="btn-cancel">Batal</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="showDeleteModal = false">
      <div class="modal-content modal-small" @click.stop>
        <div class="delete-modal">
          <div class="delete-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <h3>Hapus Produk</h3>
          <p>Apakah Anda yakin ingin menghapus produk "{{ productToDelete?.name }}"? Tindakan ini tidak dapat dibatalkan.</p>
          <div class="delete-actions">
            <button @click="deleteProduct" class="btn-delete-confirm">Hapus</button>
            <button @click="showDeleteModal = false" class="btn-cancel">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductsPage',
  
  data() {
    return {
      products: [],
      categories: ['Elektronik', 'Fashion', 'Makanan', 'Furniture', 'Alat Tulis'],
      searchQuery: '',
      selectedCategory: '',
      loading: false,
      error: null,
      showAddModal: false,
      showEditModal: false,
      showDeleteModal: false,
      productToDelete: null,
      form: {
        name: '',
        description: '',
        sku: '',
        price: 0,
        stock: 0,
        category: '',
        image: '',
        status: 'active'
      }
    };
  },
  
  computed: {
    filteredProducts() {
      let filtered = this.products;

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(p => 
          p.name.toLowerCase().includes(query) ||
          p.description.toLowerCase().includes(query) ||
          p.sku.toLowerCase().includes(query)
        );
      }

      if (this.selectedCategory && this.selectedCategory !== '') {
        filtered = filtered.filter(p => p.category === this.selectedCategory);
      }

      return filtered;
    },
    
    stats() {
      return {
        total: this.products.length,
        categories: this.categories.length,
        lowStock: this.products.filter(p => p.stock <= 10).length
      };
    }
  },
  
  methods: {
    loadSampleData() {
      this.products = [
        {
          id: 1,
          name: 'Laptop Gaming ROG',
          description: 'Laptop gaming dengan performa tinggi untuk gaming dan editing',
          sku: 'ELK-001',
          price: 15000000,
          stock: 5,
          category: 'Elektronik',
          image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400',
          status: 'active'
        }
      ];
      // PENTING: Simpan ke localStorage agar Dashboard bisa baca
      this.saveToLocalStorage();
      console.log('Sample data loaded and saved:', this.products.length, 'products');
    },
    
    saveToLocalStorage() {
      try {
        localStorage.setItem('productsData', JSON.stringify(this.products));
        console.log('✅ Products saved to localStorage');
        
        // Trigger event untuk notify komponen lain
        window.dispatchEvent(new Event('productsUpdated'));
      } catch (error) {
        console.error('Error saving to localStorage:', error);
      }
    },
    
    loadFromLocalStorage() {
      try {
        const savedData = localStorage.getItem('productsData');
        if (savedData) {
          this.products = JSON.parse(savedData);
          console.log('✅ Products loaded from localStorage:', this.products.length);
          return true;
        }
        return false;
      } catch (error) {
        console.error('Error loading from localStorage:', error);
        return false;
      }
    },
    
    formatPrice(price) {
      return new Intl.NumberFormat('id-ID').format(price);
    },
    
    openAddModal() {
      this.showAddModal = true;
    },
    
    editProduct(product) {
      this.form = { ...product };
      this.showEditModal = true;
    },
    
    confirmDelete(product) {
      this.productToDelete = product;
      this.showDeleteModal = true;
    },
    
    async deleteProduct() {
      this.products = this.products.filter(p => p.id !== this.productToDelete.id);
      this.saveToLocalStorage(); // Simpan perubahan
      this.showDeleteModal = false;
      this.productToDelete = null;
      alert('Produk berhasil dihapus');
    },
    
    async submitForm() {
      if (this.showAddModal) {
        const newId = this.products.length > 0 
          ? Math.max(...this.products.map(p => p.id)) + 1 
          : 1;
        this.products.push({ ...this.form, id: newId });
        alert('Produk berhasil ditambahkan');
      } else {
        const index = this.products.findIndex(p => p.id === this.form.id);
        if (index !== -1) {
          this.products[index] = { ...this.form };
        }
        alert('Produk berhasil diupdate');
      }
      this.saveToLocalStorage(); // Simpan perubahan
      this.closeModal();
    },
    
    closeModal() {
      this.showAddModal = false;
      this.showEditModal = false;
      this.form = {
        name: '',
        description: '',
        sku: '',
        price: 0,
        stock: 0,
        category: '',
        image: '',
        status: 'active'
      };
    }
  },
  
  created() {
    // Coba load dari localStorage dulu, kalau tidak ada baru load sample data
    if (!this.loadFromLocalStorage()) {
      console.log('No data in localStorage, loading sample data...');
      this.loadSampleData();
    }
  },
  
  mounted() {
    console.log('✅ Products component mounted successfully');
    console.log('📦 Total products:', this.products.length);
  }
};
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.products-page { min-height: 100vh; background: #f9fafb; }
.container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }

/* Header */
.page-header { background: white; border-bottom: 1px solid #e5e7eb; margin-bottom: 1.5rem; }
.header-content { max-width: 1280px; margin: 0 auto; padding: 1.5rem 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
.header-left { display: flex; align-items: center; gap: 1rem; }
.icon-box { width: 48px; height: 48px; background: #3b82f6; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; }
.icon-box svg { width: 24px; height: 24px; color: white; }
.header-text h1 { font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem; }
.header-text p { font-size: 0.875rem; color: #6b7280; }

/* Buttons */
.btn-primary { display: flex; align-items: center; gap: 0.5rem; background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem; transition: background 0.2s; }
.btn-primary:hover { background: #2563eb; }
.btn-primary svg { width: 20px; height: 20px; }

/* Stats Grid */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card { background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.stat-content { display: flex; align-items: center; justify-content: space-between; }
.stat-label { font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; }
.stat-value { font-size: 1.875rem; font-weight: 700; color: #111827; }
.stat-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.stat-icon svg { width: 24px; height: 24px; }
.stat-icon.blue { background: #dbeafe; color: #3b82f6; }
.stat-icon.purple { background: #ede9fe; color: #8b5cf6; }
.stat-icon.yellow { background: #fef3c7; color: #f59e0b; }
.stat-icon.indigo { background: #e0e7ff; color: #6366f1; }

/* Search */
.search-card { background: white; border-radius: 0.5rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }
.search-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
.search-box, .filter-box { position: relative; }
.search-box svg, .filter-box svg { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #9ca3af; }
.search-box input, .filter-box select { width: 100%; padding: 0.5rem 0.5rem 0.5rem 2.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; }
.search-box input:focus, .filter-box select:focus { outline: none; border-color: #3b82f6; ring: 2px; ring-color: #3b82f6; }

/* Loading/Error States */
.loading-state, .error-state, .empty-state { background: white; border-radius: 0.5rem; padding: 3rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.spinner { display: inline-block; width: 48px; height: 48px; border: 2px solid #e5e7eb; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem; }
@keyframes spin { to { transform: rotate(360deg); } }
.error-state svg, .empty-state svg { width: 64px; height: 64px; color: #d1d5db; margin: 0 auto 1rem; }
.error-state h3, .empty-state h3 { font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem; }
.error-state p, .empty-state p { color: #6b7280; margin-bottom: 1.5rem; }
.btn-error { background: #ef4444; color: white; padding: 0.5rem 1.5rem; border-radius: 0.5rem; border: none; cursor: pointer; }
.btn-error:hover { background: #dc2626; }

/* Products Grid */
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.product-card { background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: box-shadow 0.2s; overflow: hidden; }
.product-card:hover { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.product-image { position: relative; width: 100%; height: 192px; overflow: hidden; }
.product-image img { width: 100%; height: 100%; object-fit: cover; }
.badge-status, .badge-lowstock { position: absolute; top: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; }
.badge-status { right: 0.5rem; }
.badge-status.active { background: #d1fae5; color: #065f46; }
.badge-status.inactive { background: #f3f4f6; color: #374151; }
.badge-lowstock { left: 0.5rem; background: #fee2e2; color: #991b1b; }
.product-body { padding: 1rem; }
.product-name { font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.product-description { font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-meta { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; }
.badge-category { padding: 0.25rem 0.5rem; background: #dbeafe; color: #1e40af; font-size: 0.75rem; border-radius: 9999px; }
.product-sku { font-size: 0.75rem; color: #6b7280; }
.product-pricing { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
.product-price { font-size: 1.5rem; font-weight: 700; color: #111827; }
.product-stock { text-align: right; }
.stock-label { font-size: 0.875rem; color: #6b7280; }
.stock-value { font-size: 1.125rem; font-weight: 600; color: #111827; }
.stock-value.low { color: #dc2626; }
.product-actions { display: flex; gap: 0.5rem; }
.btn-edit, .btn-delete { flex: 1; padding: 0.5rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem; transition: background 0.2s; }
.btn-edit { background: #3b82f6; color: white; }
.btn-edit:hover { background: #2563eb; }
.btn-delete { background: #ef4444; color: white; }
.btn-delete:hover { background: #dc2626; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 50; padding: 1rem; }
.modal-content { background: white; border-radius: 0.5rem; max-width: 42rem; width: 100%; max-height: 90vh; overflow-y: auto; }
.modal-small { max-width: 28rem; }
.modal-header { position: sticky; top: 0; background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
.modal-header h2 { font-size: 1.25rem; font-weight: 700; color: #111827; }
.btn-close { background: none; border: none; color: #9ca3af; cursor: pointer; padding: 0.25rem; }
.btn-close:hover { color: #6b7280; }
.btn-close svg { width: 24px; height: 24px; }
.modal-form { padding: 1.5rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-actions { display: flex; gap: 0.75rem; padding-top: 1rem; }
.btn-submit, .btn-cancel { flex: 1; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem; transition: background 0.2s; }
.btn-submit { background: #3b82f6; color: white; }
.btn-submit:hover { background: #2563eb; }
.btn-cancel { background: #e5e7eb; color: #374151; }
.btn-cancel:hover { background: #d1d5db; }

/* Delete Modal */
.delete-modal { padding: 1.5rem; text-align: center; }
.delete-icon { width: 48px; height: 48px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
.delete-icon svg { width: 24px; height: 24px; color: #dc2626; }
.delete-modal h3 { font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem; }
.delete-modal p { font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; }
.delete-actions { display: flex; gap: 0.75rem; }
.btn-delete-confirm { flex: 1; background: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; }
.btn-delete-confirm:hover { background: #dc2626; }

/* Responsive */
@media (max-width: 768px) {
  .header-content { flex-direction: column; align-items: stretch; }
  .header-left { flex-direction: column; align-items: flex-start; }
  .products-grid { grid-template-columns: 1fr; }
  .form-row { grid-template-columns: 1fr; }
}
</style>'
        
        {
          id: 2,
          name: 'Kemeja Batik Premium',
          description: 'Kemeja batik berkualitas tinggi dengan motif modern',
          sku: 'FSH-001',
          price: 350000,
          stock: 25,
          category: 'Fashion',
          image: 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=400',
          status: 'active'
        },
        {
          id: 3,
          name: 'Kopi Arabica Premium',
          description: 'Kopi arabica pilihan dari petani lokal',
          sku: 'MKN-001',
          price: 85000,
          stock: 8,
          category: 'Makanan',
          image: 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400',
          status: 'active'
        },
        {
          id: 4,
          name: 'Meja Kerja Minimalis',
          description: 'Meja kerja dengan desain minimalis modern',
          sku: 'FRN-001',
          price: 1250000,
          stock: 15,
          category: 'Furniture',
          image: 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=400',
          status: 'active'
        },
        {
          id: 5,
          name: 'Set Alat Tulis Premium',
          description: 'Set lengkap alat tulis untuk profesional',
          sku: 'ATK-001',
          price: 250000,
          stock: 30,
          category: 'Alat Tulis',
          image: 'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=400',
          status: 'active
        }