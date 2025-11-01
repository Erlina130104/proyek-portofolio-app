<template>
  <!-- 
    WADAH UTAMA DASHBOARD
    - min-h-screen: Tinggi minimum selebar layar penuh
    - bg-gradient: Warna latar gradasi dari abu ke ungu
  -->
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50 to-purple-100 p-4 md:p-8">
    
    <!-- ==================== LOADING (Spinner putar) ==================== -->
    <!-- Hanya muncul kalau sedang loading -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-slate-600">Loading dashboard...</p>
      </div>
    </div>

    <!-- ==================== ERROR (Kalau ada error) ==================== -->
    <div v-else-if="error" class="max-w-7xl mx-auto">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-red-800">{{ error }}</p>
        <!-- Tombol untuk coba lagi -->
        <button 
          @click="fetchDashboard" 
          class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Try Again
        </button>
      </div>
    </div>

    <!-- ==================== ISI DASHBOARD ==================== -->
    <!-- Muncul kalau loading = false dan error = null -->
    <div v-else>
      
      <!-- ===== HEADER: Judul, Tanggal, User Menu ===== -->
      <div class="max-w-7xl mx-auto mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
          
          <!-- KIRI: Icon & Judul -->
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="9"></rect>
                <rect x="14" y="3" width="7" height="5"></rect>
                <rect x="14" y="12" width="7" height="9"></rect>
                <rect x="3" y="16" width="7" height="5"></rect>
              </svg>
            </div>
            <div>
              <h1 class="text-3xl md:text-4xl font-bold text-slate-800">Dashboard Overview</h1>
              <p class="text-slate-500 text-sm mt-1">Welcome back! Here's what's happening today</p>
            </div>
          </div>
          
          <!-- KANAN: Tanggal & User Menu -->
          <div class="flex items-center gap-3">
            
            <!-- KOTAK TANGGAL -->
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-md">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <!-- Panggil function untuk dapat tanggal hari ini -->
              <span class="text-sm font-semibold text-slate-700">{{ getCurrentDate() }}</span>
            </div>

            <!-- DROPDOWN USER MENU -->
            <div class="relative">
              <!-- 
                TOMBOL: Klik untuk buka/tutup dropdown
                @click toggle (balik) nilai isUserMenuOpen
                true jadi false, false jadi true
              -->
              <button 
                @click="isUserMenuOpen = !isUserMenuOpen"
                class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all"
              >
                <!-- Avatar bulat dengan initial user -->
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                  <!-- userInitial adalah computed property (otomatis update) -->
                  {{ userInitial }}
                </div>
                
                <!-- Nama user (hidden di mobile dengan hidden md:block) -->
                <span class="text-sm font-semibold text-slate-700 hidden md:block">
                  {{ userName }}
                </span>
                
                <!-- Arrow icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <!-- MENU DROPDOWN (muncul kalau isUserMenuOpen = true) -->
              <div 
                v-if="isUserMenuOpen"
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-slate-200 py-2 z-50"
              >
                <!-- Header: Info User -->
                <div class="px-4 py-3 border-b border-slate-200">
                  <p class="font-medium text-slate-900">{{ userName }}</p>
                  <p class="text-sm text-slate-500">{{ userEmail }}</p>
                </div>
                
                <!-- TOMBOL LOGOUT -->
                <button 
                  @click="handleLogout"
                  class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 transition-colors"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                  </svg>
                  <span class="font-medium">Logout</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== 6 KARTU STATISTIK UTAMA ==================== -->
      <!--
        Grid responsive:
        - Mobile (default): 1 kolom
        - Tablet (md): 2 kolom
        - Desktop (lg): 3 kolom
      -->
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        
        <!-- KARTU 1: PRODUCTS -->
        <!-- 
          @click="navigateTo('/products')" artinya:
          Kalau diklik, pindah ke halaman products
          cursor-pointer = mouse jadi pointer (tanda klik)
          hover:scale-105 = membesar sedikit saat di-hover
        -->
        <div 
          @click="navigateTo('/products')" 
          class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
        >
          <div class="flex items-center justify-between mb-3">
            <!-- Icon Products -->
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
              </svg>
            </div>
            <!-- Badge pertumbuhan -->
            <span class="text-green-600 text-xs font-semibold">
              {{ getProductsGrowth() }}
            </span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Products</p>
          <!-- Angka besar dari stats (reactive - otomatis update) -->
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.products }}</p>
        </div>

        <!-- KARTU 2: TRANSACTIONS -->
        <div 
          @click="navigateTo('/transactions')" 
          class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
            </div>
            <span class="text-green-600 text-xs font-semibold">+8%</span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Transactions</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.transactions }}</p>
        </div>

        <!-- KARTU 3: EMPLOYEES -->
        <div 
          @click="navigateTo('/employees')" 
          class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <span class="text-orange-600 text-xs font-semibold">
              {{ getEmployeeGrowth() }}
            </span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Employees</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.employees }}</p>
        </div>

        <!-- KARTU 4: ATTENDANCE -->
        <div 
          @click="navigateTo('/attendance')" 
          class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <!-- Persentase kehadiran -->
            <span class="text-yellow-600 text-xs font-semibold">
              {{ stats.attendanceRate }}%
            </span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Attendance</p>
          <!-- Format: hadir/total -->
          <p class="text-3xl font-bold text-slate-800 mt-1">
            {{ stats.attendancePresent }}/{{ stats.attendanceTotal }}
          </p>
        </div>

        <!-- KARTU 5: FEEDBACK -->
        <div 
          @click="navigateTo('/feedback')" 
          class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-pink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
              </svg>
            </div>
            <!-- Jumlah feedback baru hari ini -->
            <span class="text-green-600 text-xs font-semibold">
              +{{ stats.feedbackNew }}
            </span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Feedback</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.feedback }}</p>
        </div>

        <!-- KARTU 6: PENDING TICKETS -->
        <div 
          @click="navigateTo('/tickets')" 
          class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
            </div>
            <!-- 
              Ternary operator (if-else singkat):
              kondisi ? nilaiJikaTrue : nilaiJikaFalse
            -->
            <span class="text-red-600 text-xs font-semibold">
              {{ stats.ticketsPending > 0 ? 'Urgent' : 'Normal' }}
            </span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Pending Tickets</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.ticketsPending }}</p>
        </div>
      </div>

      <!-- ==================== 2 KARTU BESAR: BUSINESS & PEOPLE ==================== -->
      <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- KARTU: BUSINESS OVERVIEW -->
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300">
          <!-- Header Kartu -->
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Business Overview</h3>
          </div>

          <!-- Isi Kartu -->
          <div class="space-y-4">
            <!-- Baris 1: Products -->
            <div class="flex items-center justify-between">
              <span class="text-slate-600 font-medium">Products</span>
              <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ stats.products }}
              </span>
            </div>
            
            <!-- Baris 2: Transactions -->
            <div class="flex items-center justify-between">
              <span class="text-slate-600 font-medium">Transactions (This Month)</span>
              <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ stats.transactions }}
              </span>
            </div>

            <!-- PROGRESS BAR: Growth Rate -->
            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-600">Growth Rate</span>
                <span class="text-sm font-bold text-green-600">
                  {{ stats.growthRate }}%
                </span>
              </div>
              
              <!-- Container progress bar -->
              <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                <!-- 
                  Bar yang bergerak (fill)
                  :style untuk set width secara dynamic
                  Math.min() ambil nilai terkecil (max 100%)
                  Math.abs() ambil nilai absolut (positif)
                -->
                <div 
                  class="bg-gradient-to-r from-green-500 to-emerald-600 h-full rounded-full transition-all duration-500" 
                  :style="`width: ${Math.min(Math.abs(stats.growthRate), 100)}%`"
                ></div>
              </div>
            </div>

            <!-- Kotak pesan sukses -->
            <div class="mt-4 p-4 bg-green-50 rounded-xl border border-green-200">
              <p class="text-sm text-green-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Pertumbuhan bisnis sangat baik bulan ini!
              </p>
            </div>
          </div>
        </div>

        <!-- KARTU: PEOPLE OVERVIEW -->
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">People Overview</h3>
          </div>

          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-slate-600 font-medium">Employees</span>
              <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ stats.employees }}
              </span>
            </div>
            
            <div class="flex items-center justify-between">
              <span class="text-slate-600 font-medium">Attendance Rate</span>
              <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ stats.attendanceRate }}%
              </span>
            </div>

            <!-- Progress bar attendance -->
            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-600">Attendance Progress</span>
                <span class="text-sm font-bold text-yellow-600">
                  {{ stats.attendanceRate }}%
                </span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                <div 
                  class="bg-gradient-to-r from-yellow-400 to-amber-500 h-full rounded-full transition-all duration-500" 
                  :style="`width: ${Math.min(stats.attendanceRate, 100)}%`"
                ></div>
              </div>
            </div>

            <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
              <p class="text-sm text-blue-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Tingkat kehadiran karyawan sangat baik
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== KARTU: SUPPORT & FEEDBACK ==================== -->
      <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-orange-600 rounded-xl flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Support & Feedback</h3>
          </div>

          <!-- 3 KOLOM -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- KOLOM 1: Pending Tickets -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-slate-600 font-medium">Pending Tickets</span>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                  {{ stats.ticketsPending }}
                </span>
              </div>
              <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                <!-- Text conditional berdasarkan jumlah ticket -->
                <p class="text-sm text-red-700">
                  {{ stats.ticketsPending > 0 ? 'Requires immediate attention' : 'All tickets resolved' }}
                </p>
              </div>
            </div>

            <!-- KOLOM 2: Customer Feedback -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-slate-600 font-medium">Customer Feedback</span>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                  +{{ stats.feedbackNew }} New
                </span>
              </div>
              
              <!-- Conditional: tampil beda kalau ada/gak ada feedback -->
              <div 
                v-if="stats.feedback > 0" 
                class="p-3 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200"
              >
                <!-- Italic untuk quote -->
                <p class="text-sm text-slate-700 italic">
                  "{{ stats.lastFeedbackComment }}"
                </p>
              </div>
              <div 
                v-else 
                class="p-3 bg-gray-50 rounded-lg border border-gray-200"
              >
                <p class="text-sm text-gray-600">Belum ada feedback terbaru</p>
              </div>
            </div>

            <!-- KOLOM 3: AI Insights -->
            <div class="space-y-3">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <span class="text-slate-600 font-medium">AI Insights</span>
              </div>
              <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                <!-- 
                  Function call yang return insight random
                  Function dipanggil SETIAP kali component re-render
                -->
                <p class="text-sm text-indigo-800">{{ generateAIInsight() }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// ==================== IMPORT ====================
// Import fungsi-fungsi Vue yang dibutuhkan
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router'; // Untuk navigasi antar halaman
import { useAuthStore } from '@/stores/auth'; // Pinia store untuk data user

// ==================== INITIALIZE ====================
const router = useRouter(); // Instance router untuk navigation
const authStore = useAuthStore(); // Instance auth store untuk access data user

// ==================== VARIABEL REACTIVE ====================
// ref() = bikin variabel yang reactive (Vue bisa track perubahannya)

const loading = ref(false);           // Loading state (true = lagi loading)
const error = ref(null);              // Error message (null = tidak ada error)
const isUserMenuOpen = ref(false);    // Status dropdown user menu (true = terbuka)

// Object untuk simpan semua statistik dashboard
const stats = ref({
  products: 0,              // Jumlah total produk
  transactions: 0,          // Jumlah total transaksi
  employees: 0,             // Jumlah total karyawan
  feedback: 0,              // Jumlah total feedback
  feedbackNew: 0,           // Jumlah feedback baru hari ini
  ticketsPending: 0,        // Jumlah ticket yang masih pending
  attendancePresent: 0,     // Jumlah yang hadir
  attendanceTotal: 0,       // Total record attendance
  attendanceRate: 0,        // Persentase kehadiran
  growthRate: 15.5,         // Persentase pertumbuhan bisnis
  lastFeedbackComment: 'Pelayanan sangat memuaskan, produk berkualitas!' // Feedback terakhir
});

// ==================== COMPUTED PROPERTIES ====================
// computed() = variabel yang dihitung otomatis dari variabel lain
// Lebih efisien karena di-cache (tidak hitung ulang kalau dependencies sama)

/**
 * COMPUTED: Ambil nama user dari auth store
 * Kalau user gak ada, pakai default 'User'
 */
const userName = computed(() => {
  // authStore.user?.name artinya:
  // Ambil name dari user, tapi aman kalau user null (optional chaining)
  // || 'User' = fallback kalau name kosong
  return authStore.user?.name || 'User';
});

/**
 * COMPUTED: Ambil email user dari auth store
 */
const userEmail = computed(() => {
  return authStore.user?.email || 'user@example.com';
});

/**
 * COMPUTED: Ambil initial (huruf pertama) nama user
 * Untuk ditampilkan di avatar bulat
 */
const userInitial = computed(() => {
  // .charAt(0) ambil karakter pertama
  // .toUpperCase() ubah jadi huruf besar
  return authStore.user?.name?.charAt(0).toUpperCase() || 'U';
});

// ==================== FUNGSI LOGOUT ====================

/**
 * FUNGSI: Handle logout user
 * Dipanggil saat user klik tombol Logout
 */
const handleLogout = async () => {
  // Tutup dropdown dulu
  isUserMenuOpen.value = false;
  
  // Konfirmasi dengan popup browser
  if (confirm('Apakah Anda yakin ingin logout?')) {
    // Panggil fungsi logout dari auth store
    // Fungsi ini akan clear data user dan redirect ke login
    await authStore.logout();
  }
};

// ==================== FUNGSI FETCH DASHBOARD ====================

/**
 * FUNGSI UTAMA: Fetch semua data dashboard
 * Dipanggil saat component pertama kali load
 * Dan dipanggil lagi setiap 30 detik untuk auto-refresh
 */
const fetchDashboard = async () => {
  try {
    loading.value = true;   // Aktifkan loading spinner
    error.value = null;     // Reset error message
    
    // ===== LOAD DATA DARI LOCALSTORAGE =====
    // localStorage.getItem() ambil data (return string atau null)
    const productsData = localStorage.getItem('productsData');
    const ticketsData = localStorage.getItem('ticketsData');
    const employeesData = localStorage.getItem('employeesData');
    const feedbackData = localStorage.getItem('feedbackData');
    const attendanceData = localStorage.getItem('attendanceData');

    // ===== PARSE JSON STRING JADI ARRAY =====
    // JSON.parse() ubah string jadi object/array
    // Kalau null, pakai empty array []
    const products = productsData ? JSON.parse(productsData) : [];
    const tickets = ticketsData ? JSON.parse(ticketsData) : [];
    const employees = employeesData ? JSON.parse(employeesData) : [];
    const feedbacks = feedbackData ? JSON.parse(feedbackData) : [];
    const attendance = attendanceData ? JSON.parse(attendanceData) : [];

    // ===== HITUNG STATISTIK =====
    
    // Filter feedback baru (hari ini)
    const feedbackNewToday = feedbacks.filter(f => {
      const date = new Date(f.created_at);    // Ubah string jadi Date object
      const today = new Date();               // Tanggal hari ini
      // toDateString() ubah jadi format "Mon Jan 15 2025"
      // Compare: apakah tanggal feedback sama dengan hari ini?
      return date.toDateString() === today.toDateString();
    }).length;
    
    // Filter ticket yang masih pending
    // Status bisa 'Pending' atau 'open' (case-sensitive)
    const pendingTickets = tickets.filter(t => 
      t.status === 'Pending' || t.status === 'open'
    ).length;
    
    // Hitung attendance rate (persentase kehadiran)
    let attendanceRate = 0;
    if (attendance.length > 0) {
      // Filter yang hadir
      const presentCount = attendance.filter(a => a.status === 'present').length;
      // Hitung persentase dan bulatkan
      attendanceRate = Math.round((presentCount / attendance.length) * 100);
    }

    // ===== UPDATE STATS OBJECT =====
    stats.value = {
      products: products.length,                    // Total produk
      transactions: tickets.length,                 // Total transaksi (dari tickets)
      employees: employees.length,                  // Total karyawan
      feedback: feedbacks.length,                   // Total feedback
      feedbackNew: feedbackNewToday,                // Feedback baru
      ticketsPending: pendingTickets,               // Ticket pending
      attendancePresent: attendance.filter(a => a.status === 'present').length, // Yang hadir
      attendanceTotal: attendance.length || employees.length || 1, // Total (minimal 1 untuk avoid divide by 0)
      attendanceRate: attendanceRate,               // Persentase kehadiran
      growthRate: 15.5,                             // Hardcoded (bisa dihitung dari data historis)
      lastFeedbackComment: feedbacks.length > 0 
        ? feedbacks[0].message || 'Pelayanan sangat memuaskan' 
        : 'Data tidak tersedia'
    };

    // ===== GENERATE RANDOM ATTENDANCE RATE (kalau gak ada data) =====
    if (!attendance.length && employees.length > 0) {
      // Random antara 70-100%
      stats.value.attendanceRate = Math.round(Math.random() * 30 + 70);
    }
    
  } catch (err) {
    // ===== ERROR HANDLING =====
    console.error('Error fetching dashboard:', err);
    error.value = 'Failed to load dashboard data'; // Set error message
  } finally {
    // ===== ALWAYS MATIKAN LOADING =====
    // Finally block selalu dijalankan (error atau success)
    loading.value = false;
  }
};

// ==================== FUNGSI HELPER ====================

/**
 * FUNGSI: Format tanggal hari ini
 * Return: String format "Senin, 15 Januari 2025"
 */
const getCurrentDate = () => {
  return new Date().toLocaleDateString('id-ID', {
    day: '2-digit',        // Hari (01, 02, dst)
    month: 'long',         // Bulan lengkap (Januari, Februari, dst)
    year: 'numeric',       // Tahun (2025)
    weekday: 'long'        // Nama hari (Senin, Selasa, dst)
  });
};

/**
 * FUNGSI: Hitung growth rate products
 * Return: String persentase (contoh: "+12%")
 */
const getProductsGrowth = () => {
  const products = localStorage.getItem('productsData');
  const count = products ? JSON.parse(products).length : 0;
  // Kalau ada produk, return +12%, kalau tidak 0%
  return count > 0 ? '+12%' : '0%';
};

/**
 * FUNGSI: Hitung growth rate employees
 * Return: String persentase
 */
const getEmployeeGrowth = () => {
  const employees = localStorage.getItem('employeesData');
  const count = employees ? JSON.parse(employees).length : 0;
  return count > 0 ? '+2%' : '0%';
};

/**
 * FUNGSI: Generate AI insight random
 * Return: String insight yang beda-beda setiap kali dipanggil
 */
const generateAIInsight = () => {
  // Array of possible insights
  const insights = [
    `Performa penjualan meningkat dengan ${stats.value.transactions} transaksi bulan ini`,
    `Tim Anda memiliki tingkat kehadiran yang baik di ${stats.value.attendanceRate}%`,
    `Ada ${stats.value.ticketsPending} ticket yang perlu perhatian segera`,
    `Feedback positif dari ${stats.value.feedback} pelanggan telah diterima`,
    `Total ${stats.value.employees} karyawan aktif dalam sistem`,
    `Rekomendasi: Fokus pada ${stats.value.ticketsPending > 0 ? 'resolusi ticket' : 'peningkatan layanan'}`
  ];
  
  // Pilih random insight dari array
  // Math.random() = angka random 0-1
  // Math.floor() = bulatkan ke bawah
  // Hasilnya: index random dari 0 sampai insights.length-1
  return insights[Math.floor(Math.random() * insights.length)];
};

/**
 * FUNGSI: Navigate ke halaman lain
 * @param {string} path - Path tujuan (contoh: '/products')
 */
const navigateTo = (path) => {
  // router.push() untuk pindah halaman (SPA - tanpa reload)
  router.push(path);
};

// ==================== LIFECYCLE HOOKS ====================

/**
 * onMounted: Dipanggil SETELAH component selesai di-render ke DOM
 * Ini tempat ideal untuk:
 * - Fetch data awal
 * - Setup event listeners
 * - Start timers/intervals
 */
onMounted(() => {
  // 1. Fetch data dashboard pertama kali
  fetchDashboard();
  
  // 2. Setup auto-refresh setiap 30 detik
  // setInterval() jalankan function secara berulang
  // 30000 milliseconds = 30 detik
  const intervalId = setInterval(fetchDashboard, 30000);
  
  // PENTING: Simpan intervalId untuk di-cleanup nanti
  // Attach ke window supaya bisa diakses di onUnmounted
  window.dashboardInterval = intervalId;
});

/**
 * onUnmounted: Dipanggil SEBELUM component dihapus dari DOM
 * Ini tempat untuk cleanup:
 * - Clear timers/intervals
 * - Remove event listeners
 * - Cancel pending requests
 * 
 * SANGAT PENTING untuk avoid memory leaks!
 */
onUnmounted(() => {
  // Clear interval untuk stop auto-refresh
  if (window.dashboardInterval) {
    clearInterval(window.dashboardInterval);
    // Set null untuk membebaskan memory
    window.dashboardInterval = null;
  }
});

</script>

