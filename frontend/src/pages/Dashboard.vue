<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50 to-purple-100 p-4 md:p-8">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-slate-600">Loading dashboard...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-red-800">{{ error }}</p>
        <button @click="fetchDashboard" class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
          Try Again
        </button>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Header -->
      <div class="max-w-7xl mx-auto mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
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
          
          <div class="flex items-center gap-3">
            <!-- Date -->
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-md">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span class="text-sm font-semibold text-slate-700">{{ getCurrentDate() }}</span>
            </div>

            <!-- User Menu with Logout -->
            <div class="relative">
              <button 
                @click="isUserMenuOpen = !isUserMenuOpen"
                class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all"
              >
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                  {{ userInitial }}
                </div>
                <span class="text-sm font-semibold text-slate-700 hidden md:block">{{ userName }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div 
                v-if="isUserMenuOpen"
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-slate-200 py-2 z-50"
              >
                <div class="px-4 py-3 border-b border-slate-200">
                  <p class="font-medium text-slate-900">{{ userName }}</p>
                  <p class="text-sm text-slate-500">{{ userEmail }}</p>
                </div>
                
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

      <!-- Quick Stats -->
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <!-- Products Card -->
        <div 
          @click="navigateTo('/products')" 
          class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
              </svg>
            </div>
            <span class="text-green-600 text-xs font-semibold">{{ getProductsGrowth() }}</span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Products</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.products }}</p>
        </div>

        <!-- Transactions Card -->
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

        <!-- Employees Card -->
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
            <span class="text-orange-600 text-xs font-semibold">{{ getEmployeeGrowth() }}</span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Employees</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.employees }}</p>
        </div>

        <!-- Attendance Card -->
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
            <span class="text-yellow-600 text-xs font-semibold">{{ stats.attendanceRate }}%</span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Attendance</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.attendancePresent }}/{{ stats.attendanceTotal }}</p>
        </div>

        <!-- Feedback Card -->
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
            <span class="text-green-600 text-xs font-semibold">+{{ stats.feedbackNew }}</span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Feedback</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.feedback }}</p>
        </div>

        <!-- Pending Tickets Card -->
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
            <span class="text-red-600 text-xs font-semibold">{{ stats.ticketsPending > 0 ? 'Urgent' : 'Normal' }}</span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Pending Tickets</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.ticketsPending }}</p>
        </div>
      </div>

      <!-- Business & People Overview Cards -->
      <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Business Overview Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Business Overview</h3>
          </div>

          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-slate-600 font-medium">Products</span>
              <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ stats.products }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-600 font-medium">Transactions (This Month)</span>
              <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                {{ stats.transactions }}
              </span>
            </div>

            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-600">Growth Rate</span>
                <span class="text-sm font-bold text-green-600">{{ stats.growthRate }}%</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                <div 
                  class="bg-gradient-to-r from-green-500 to-emerald-600 h-full rounded-full transition-all duration-500" 
                  :style="`width: ${Math.min(Math.abs(stats.growthRate), 100)}%`"
                ></div>
              </div>
            </div>

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

        <!-- People Overview Card -->
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

            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-600">Attendance Progress</span>
                <span class="text-sm font-bold text-yellow-600">{{ stats.attendanceRate }}%</span>
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

      <!-- Support & Feedback Card -->
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

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Pending Tickets -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-slate-600 font-medium">Pending Tickets</span>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                  {{ stats.ticketsPending }}
                </span>
              </div>
              <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                <p class="text-sm text-red-700">{{ stats.ticketsPending > 0 ? 'Requires immediate attention' : 'All tickets resolved' }}</p>
              </div>
            </div>

            <!-- Customer Feedback -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-slate-600 font-medium">Customer Feedback</span>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                  +{{ stats.feedbackNew }} New
                </span>
              </div>
              <div v-if="stats.feedback > 0" class="p-3 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                <p class="text-sm text-slate-700 italic">"{{ stats.lastFeedbackComment }}"</p>
              </div>
              <div v-else class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-600">Belum ada feedback terbaru</p>
              </div>
            </div>

            <!-- AI Insights -->
            <div class="space-y-3">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <span class="text-slate-600 font-medium">AI Insights</span>
              </div>
              <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
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
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(false);
const error = ref(null);
const isUserMenuOpen = ref(false);

const stats = ref({
  products: 0,
  transactions: 0,
  employees: 0,
  feedback: 0,
  feedbackNew: 0,
  ticketsPending: 0,
  attendancePresent: 0,
  attendanceTotal: 0,
  attendanceRate: 0,
  growthRate: 15.5,
  lastFeedbackComment: 'Pelayanan sangat memuaskan, produk berkualitas!'
});

const userName = computed(() => {
  return authStore.user?.name || 'User';
});

const userEmail = computed(() => {
  return authStore.user?.email || 'user@example.com';
});

const userInitial = computed(() => {
  return authStore.user?.name?.charAt(0).toUpperCase() || 'U';
});

const handleLogout = async () => {
  isUserMenuOpen.value = false;
  if (confirm('Apakah Anda yakin ingin logout?')) {
    await authStore.logout();
  }
};

const fetchDashboard = async () => {
  try {
    loading.value = true;
    error.value = null;

    // Load data dari localStorage dari berbagai module
    const productsData = localStorage.getItem('productsData');
    const ticketsData = localStorage.getItem('ticketsData');
    const employeesData = localStorage.getItem('employeesData');
    const feedbackData = localStorage.getItem('feedbackData');
    const attendanceData = localStorage.getItem('attendanceData');

    const products = productsData ? JSON.parse(productsData) : [];
    const tickets = ticketsData ? JSON.parse(ticketsData) : [];
    const employees = employeesData ? JSON.parse(employeesData) : [];
    const feedbacks = feedbackData ? JSON.parse(feedbackData) : [];
    const attendance = attendanceData ? JSON.parse(attendanceData) : [];

    // Calculate stats
    stats.value = {
      products: products.length,
      transactions: tickets.length,
      employees: employees.length,
      feedback: feedbacks.length,
      feedbackNew: feedbacks.filter(f => {
        const date = new Date(f.created_at);
        const today = new Date();
        return date.toDateString() === today.toDateString();
      }).length,
      ticketsPending: tickets.filter(t => t.status === 'Pending' || t.status === 'open').length,
      attendancePresent: attendance.filter(a => a.status === 'present').length,
      attendanceTotal: attendance.length || employees.length || 1,
      attendanceRate: attendance.length > 0 ? Math.round((attendance.filter(a => a.status === 'present').length / attendance.length) * 100) : 0,
      growthRate: 15.5,
      lastFeedbackComment: feedbacks.length > 0 ? feedbacks[0].message || 'Pelayanan sangat memuaskan' : 'Data tidak tersedia'
    };

    // Calculate attendance rate if no attendance data
    if (!attendance.length && employees.length > 0) {
      stats.value.attendanceRate = Math.round(Math.random() * 30 + 70); // Random 70-100%
    }
  } catch (err) {
    console.error('Error fetching dashboard:', err);
    error.value = 'Failed to load dashboard data';
  } finally {
    loading.value = false;
  }
};

const getCurrentDate = () => {
  return new Date().toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    weekday: 'long'
  });
};

const getProductsGrowth = () => {
  const products = localStorage.getItem('productsData');
  const count = products ? JSON.parse(products).length : 0;
  return count > 0 ? '+12%' : '0%';
};

const getEmployeeGrowth = () => {
  const employees = localStorage.getItem('employeesData');
  const count = employees ? JSON.parse(employees).length : 0;
  return count > 0 ? '+2%' : '0%';
};

const generateAIInsight = () => {
  const insights = [
    `Performa penjualan meningkat dengan ${stats.value.transactions} transaksi bulan ini`,
    `Tim Anda memiliki tingkat kehadiran yang baik di ${stats.value.attendanceRate}%`,
    `Ada ${stats.value.ticketsPending} ticket yang perlu perhatian segera`,
    `Feedback positif dari ${stats.value.feedback} pelanggan telah diterima`,
    `Total ${stats.value.employees} karyawan aktif dalam sistem`,
    `Rekomendasi: Fokus pada ${stats.value.ticketsPending > 0 ? 'resolusi ticket' : 'peningkatan layanan'}`
  ];
  
  return insights[Math.floor(Math.random() * insights.length)];
};

const navigateTo = (path) => {
  router.push(path);
};

onMounted(() => {
  fetchDashboard();
  // Refresh data setiap 30 detik
  setInterval(fetchDashboard, 30000);
});
</script>