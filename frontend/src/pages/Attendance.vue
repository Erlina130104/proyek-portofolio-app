<template>
  <!-- 
    WADAH UTAMA - Container paling luar
    - min-h-screen: Tinggi minimum selebar layar (supaya full page)
    - bg-gradient: Warna latar belakang gradasi dari abu-abu ke hijau
    - p-4 md:p-8: Padding (jarak dalam) 16px di HP, 32px di desktop
  -->
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50 to-teal-50 p-4 md:p-8">
    
    <!-- ==================== BAGIAN HEADER (JUDUL & TOMBOL) ==================== -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center justify-between gap-3 mb-2">
        
        <!-- BAGIAN KIRI: Icon & Judul -->
        <div class="flex items-center gap-3">
          <!-- Icon Kalender dengan background warna hijau -->
          <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
              Attendance Management
            </h1>
            <p class="text-gray-500 text-sm mt-1">
              Track and monitor employee attendance records
            </p>
          </div>
        </div>

        <!-- BAGIAN KANAN: Tombol Tambah Absensi -->
        <!-- 
          @click="openCreateModal" artinya:
          Kalau tombol ini diklik, jalankan function openCreateModal()
        -->
        <button
          @click="openCreateModal"
          class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all flex items-center gap-2 font-semibold"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Add Attendance
        </button>
      </div>
    </div>

    <!-- ==================== KARTU STATISTIK (4 kotak info) ==================== -->
    <!-- 
      v-for="stat in statCards" artinya:
      Loop/ulang untuk setiap item di array statCards
      Jadi kalau ada 4 item, maka bikin 4 kotak
      
      :key="stat.label" artinya:
      Kasih penanda unik untuk setiap kotak (biar Vue bisa track dengan baik)
    -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div
        v-for="stat in statCards"
        :key="stat.label"
        class="bg-white rounded-xl p-6 shadow-md border border-gray-200 hover:shadow-xl transition-all"
      >
        <div class="flex items-center justify-between">
          <div>
            <!-- {{ stat.label }} artinya: tampilkan isi dari stat.label -->
            <p class="text-gray-500 text-sm font-medium">{{ stat.label }}</p>
            
            <!-- 
              :class artinya: dynamic class (class yang bisa berubah-ubah)
              Array notation [...] untuk gabungkan beberapa class
            -->
            <p :class="['text-3xl font-bold mt-1', stat.color]">{{ stat.value }}</p>
          </div>
          <div :class="['w-12 h-12 rounded-lg flex items-center justify-center', stat.bgColor]"></div>
        </div>
      </div>
    </div>

    <!-- ==================== BAGIAN FILTER & PENCARIAN ==================== -->
    <div class="max-w-7xl mx-auto mb-6">
      <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
        <div class="flex flex-col md:flex-row gap-4">
          
          <!-- KOTAK PENCARIAN -->
          <div class="relative flex-1">
            <!-- 
              v-model="search" artinya:
              Two-way binding = sinkronisasi 2 arah
              - Kalau user ketik, variable 'search' otomatis update
              - Kalau 'search' diubah di kode, input juga otomatis update
              
              @input="debouncedSearch" artinya:
              Setiap kali user mengetik, jalankan function debouncedSearch()
            -->
            <input
              v-model="search"
              @input="debouncedSearch"
              placeholder="Search by employee name..."
              class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
            <!-- Icon search (posisi absolute = nempel di dalam input) -->
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </div>

          <!-- FILTER TANGGAL -->
          <input 
            type="date" 
            v-model="selectedDate" 
            @change="handleFilterChange" 
            class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500" 
          />

          <!-- FILTER STATUS (Dropdown) -->
          <select 
            v-model="filterStatus" 
            @change="handleFilterChange" 
            class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500"
          >
            <!-- :value="null" artinya: value-nya adalah null (kosong/semua) -->
            <option :value="null">All Status</option>
            <option value="present">Present</option>
            <option value="late">Late</option>
            <option value="absent">Absent</option>
            <option value="sick">Sick</option>
            <option value="leave">Leave</option>
          </select>
        </div>
      </div>
    </div>

    <!-- ==================== TAMPILAN LOADING (Spinner) ==================== -->
    <!-- 
      v-if="loading" artinya:
      Component ini HANYA muncul kalau variable 'loading' bernilai true
      Conditional rendering = render berdasarkan kondisi
    -->
    <div v-if="loading" class="max-w-7xl mx-auto flex justify-center items-center py-16">
      <div class="text-center">
        <!-- animate-spin = animasi putar terus menerus -->
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-green-600 mx-auto"></div>
        <p class="text-gray-600 mt-4 font-medium">Loading attendance records...</p>
      </div>
    </div>

    <!-- ==================== TABEL DATA ABSENSI ==================== -->
    <!-- 
      v-else artinya: 
      Kalau loading = false, maka tampilkan ini
      Jadi loading dan tabel gak muncul bersamaan
    -->
    <div v-else class="max-w-7xl mx-auto">
      <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold text-gray-800">Attendance Records</h2>
        </div>

        <!-- TAMPILAN KALAU DATA KOSONG -->
        <!-- 
          v-if="attendances.length === 0" artinya:
          Kalau panjang array attendances = 0 (tidak ada data)
        -->
        <div v-if="attendances.length === 0" class="text-center py-16 text-gray-400 font-medium text-lg">
          No attendance records found
        </div>

        <!-- TAMPILAN TABEL (kalau ada data) -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <!-- HEADER TABEL -->
            <thead class="bg-gray-50">
              <tr>
                <!-- Loop untuk bikin header kolom -->
                <th 
                  v-for="header in ['Employee', 'Date', 'Check In', 'Check Out', 'Status']" 
                  :key="header" 
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  {{ header }}
                </th>
              </tr>
            </thead>
            
            <!-- BODY TABEL (ISI DATA) -->
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- Loop untuk setiap data attendance -->
              <tr v-for="attendance in attendances" :key="attendance.id">
                
                <!-- KOLOM: Nama Karyawan -->
                <td class="px-6 py-4 text-gray-800">
                  <!-- 
                    attendance.employee?.name artinya:
                    Optional chaining (?) = aman kalau employee null
                    Kalau employee ada, ambil name-nya
                    Kalau employee null, gak error (return undefined)
                    
                    || `Employee #${attendance.employee_id}` artinya:
                    Kalau name-nya kosong/undefined, pakai fallback ini
                    Backticks (`) untuk template literals = bisa insert variable
                  -->
                  {{ attendance.employee?.name || `Employee #${attendance.employee_id}` }}
                </td>
                
                <!-- KOLOM: Tanggal -->
                <td class="px-6 py-4 text-gray-500">
                  <!-- Panggil function formatDate untuk format tanggal -->
                  {{ formatDate(attendance.date) }}
                </td>
                
                <!-- KOLOM: Jam Masuk -->
                <td class="px-6 py-4 text-gray-700">{{ formatTime(attendance.check_in) }}</td>
                
                <!-- KOLOM: Jam Keluar -->
                <td class="px-6 py-4 text-gray-700">{{ formatTime(attendance.check_out) }}</td>
                
                <!-- KOLOM: Status (dengan badge warna) -->
                <td class="px-6 py-4">
                  <span 
                    :class="getStatusClass(attendance.status)" 
                    class="px-3 py-1 rounded-full text-xs font-semibold"
                  >
                    {{ formatStatus(attendance.status) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== POPUP MODAL TAMBAH ABSENSI ==================== -->
    <!-- 
      Modal = popup yang muncul di tengah layar dengan background gelap
      @click.self="closeModal" artinya:
      Kalau user klik di background (bukan di modal), tutup modal
      .self = hanya trigger kalau klik di element itu sendiri, bukan child-nya
    -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
      @click.self="closeModal"
    >
      <!-- KOTAK MODAL (putih) -->
      <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
        
        <!-- HEADER MODAL (sticky = tetap di atas saat scroll) -->
        <div class="flex justify-between items-center bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 rounded-t-2xl sticky top-0 z-10">
          <h2 class="text-2xl font-bold text-white">Add Attendance</h2>
          <button 
            @click="closeModal" 
            class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors"
          >
            ✕
          </button>
        </div>

        <!-- BODY MODAL (Isi form) -->
        <div class="p-6 space-y-5">
          
          <!-- PERINGATAN: Belum Ada Karyawan -->
          <!-- Conditional: hanya muncul kalau employees.length = 0 -->
          <div 
            v-if="employees.length === 0" 
            class="bg-orange-50 border-l-4 border-orange-500 text-orange-700 px-4 py-3 rounded"
          >
            <div class="flex items-start gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
              <div>
                <p class="font-bold">No Employees Available</p>
                <p class="text-sm mt-1">Please add employees first before creating attendance records.</p>
                <button 
                  @click="goToEmployees" 
                  class="mt-2 text-sm underline hover:text-orange-900"
                >
                  Go to Employees Page →
                </button>
              </div>
            </div>
          </div>

          <!-- TAMPILAN ERROR MESSAGE -->
          <div 
            v-if="errorMessage" 
            class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded text-sm"
          >
            <p class="font-medium">{{ errorMessage }}</p>
          </div>

          <!-- FIELD: PILIH KARYAWAN (Dropdown) -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">
              Employee <span class="text-red-500">*</span>
            </label>
            
            <!-- 
              SELECT dengan v-model
              value akan otomatis sync dengan form.employee_id
              
              :class dengan conditional:
              Kalau ada error (formErrors.employee_id), tambah class merah
            -->
            <select
              v-model="form.employee_id"
              class="w-full border-2 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
              :class="{ 'border-red-300 bg-red-50': formErrors.employee_id }"
            >
              <option disabled value="">Select Employee</option>
              
              <!-- Loop untuk bikin option setiap employee -->
              <option 
                v-for="emp in employees" 
                :key="emp.id" 
                :value="emp.id"
              >
                <!-- 
                  Tampilan: "Nama Employee (Kode Employee)"
                  Kalau gak ada employee_id, pakai format EMP-123
                -->
                {{ emp.name }} ({{ emp.employee_id || `EMP-${emp.id}` }})
              </option>
            </select>
            
            <!-- Error message khusus field ini -->
            <p v-if="formErrors.employee_id" class="text-red-500 text-xs mt-1">
              {{ formErrors.employee_id }}
            </p>
            
            <!-- Helper text (info tambahan) -->
            <p v-if="employees.length === 0" class="text-orange-500 text-xs mt-1">
              ⚠️ No employees found. Please add employees first.
            </p>
            <p v-else class="text-gray-500 text-xs mt-1">
              <!-- Tampilkan jumlah employee yang tersedia -->
              {{ employees.length }} employee(s) available
            </p>
          </div>

          <!-- FIELD: PILIH TANGGAL -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">
              Date <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.date" 
              type="date" 
              class="w-full border-2 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
              :class="{ 'border-red-300 bg-red-50': formErrors.date }"
            />
            <p v-if="formErrors.date" class="text-red-500 text-xs mt-1">
              {{ formErrors.date }}
            </p>
          </div>

          <!-- FIELD: JAM MASUK & JAM KELUAR (2 kolom) -->
          <div class="grid grid-cols-2 gap-4">
            
            <!-- KOLOM 1: JAM MASUK -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Check In</label>
              
              <!-- PILIH JAM (00-23) -->
              <div class="relative">
                <select 
                  v-model="form.check_in_hour"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Hour</option>
                  
                  <!-- 
                    Loop 24 kali (jam 0-23)
                    v-for="h in 24" artinya h akan bernilai 1,2,3...24
                    h-1 untuk dapat 0,1,2...23
                    
                    String(h-1).padStart(2, '0') artinya:
                    Ubah ke string dan tambah 0 di depan kalau cuma 1 digit
                    Contoh: 1 jadi "01", 9 jadi "09"
                  -->
                  <option 
                    v-for="h in 24" 
                    :key="h-1" 
                    :value="String(h-1).padStart(2, '0')"
                  >
                    {{ String(h-1).padStart(2, '0') }}
                  </option>
                </select>
                <!-- Arrow dropdown custom -->
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  ▼
                </span>
              </div>
              
              <!-- PILIH MENIT (00-59) -->
              <div class="relative mt-2">
                <select 
                  v-model="form.check_in_minute"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Minute</option>
                  <!-- Loop 60 kali untuk menit 0-59 -->
                  <option 
                    v-for="m in 60" 
                    :key="m-1" 
                    :value="String(m-1).padStart(2, '0')"
                  >
                    {{ String(m-1).padStart(2, '0') }}
                  </option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  ▼
                </span>
              </div>
              
              <!-- Preview waktu yang dipilih -->
              <p class="text-sm text-gray-500 mt-1">
                {{ getTimeDisplay(form.check_in_hour, form.check_in_minute) }}
              </p>
            </div>

            <!-- KOLOM 2: JAM KELUAR (struktur sama persis dengan Jam Masuk) -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Check Out</label>
              <div class="relative">
                <select 
                  v-model="form.check_out_hour"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Hour</option>
                  <option 
                    v-for="h in 24" 
                    :key="h-1" 
                    :value="String(h-1).padStart(2, '0')"
                  >
                    {{ String(h-1).padStart(2, '0') }}
                  </option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  ▼
                </span>
              </div>
              <div class="relative mt-2">
                <select 
                  v-model="form.check_out_minute"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Minute</option>
                  <option 
                    v-for="m in 60" 
                    :key="m-1" 
                    :value="String(m-1).padStart(2, '0')"
                  >
                    {{ String(m-1).padStart(2, '0') }}
                  </option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  ▼
                </span>
              </div>
              <p class="text-sm text-gray-500 mt-1">
                {{ getTimeDisplay(form.check_out_hour, form.check_out_minute) }}
              </p>
            </div>
          </div>

          <!-- TOMBOL QUICK SELECT WAKTU (Preset) -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm font-semibold text-gray-700 mb-2">⚡ Quick Select:</p>
            <div class="grid grid-cols-4 gap-2">
              <!-- 
                Loop untuk setiap preset waktu
                @click akan set form dengan waktu preset
              -->
              <button 
                v-for="preset in timePresets" 
                :key="preset.time"
                @click="setTimePreset(preset)"
                class="px-3 py-2 bg-white border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 rounded-lg text-sm font-medium transition-all"
              >
                {{ preset.label }}
              </button>
            </div>
          </div>

          <!-- CHECKBOX: SHIFT MALAM -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
            <label class="flex items-start gap-3 cursor-pointer">
              <!-- 
                Checkbox dengan v-model
                form.is_night_shift akan otomatis true/false
              -->
              <input 
                type="checkbox" 
                v-model="form.is_night_shift"
                class="w-5 h-5 text-green-600 focus:ring-green-500 rounded mt-0.5"
              />
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-gray-800">🌙 Night Shift</span>
                  <span class="text-xs bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full font-semibold">
                    Optional
                  </span>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                  Check this if employee clocks out after midnight (next day)
                </p>
                <p class="text-xs text-gray-500 mt-1 font-mono">
                  Example: Check In 20:00, Check Out 05:00 (next day)
                </p>
              </div>
            </label>
          </div>

          <!-- FIELD: PILIH STATUS -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">
              Status <span class="text-red-500">*</span>
            </label>
            <select 
              v-model="form.status" 
              class="w-full border-2 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
              :class="{ 'border-red-300 bg-red-50': formErrors.status }"
            >
              <option disabled value="">Select status</option>
              <option value="present">✅ Hadir (Present)</option>
              <option value="late">⏰ Terlambat (Late)</option>
              <option value="absent">❌ Tidak Hadir (Absent)</option>
              <option value="sick">🤒 Sakit (Sick)</option>
              <option value="leave">📋 Izin (Leave)</option>
            </select>
            <p v-if="formErrors.status" class="text-red-500 text-xs mt-1">
              {{ formErrors.status }}
            </p>
          </div>

          <!-- TOMBOL AKSI: Cancel & Submit -->
          <div class="flex justify-end gap-3 pt-4 border-t-2">
            <!-- Tombol Cancel -->
            <button 
              @click="closeModal" 
              class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors font-semibold"
              :disabled="submitting"
            >
              Cancel
            </button>
            
            <!-- Tombol Submit dengan loading state -->
            <button 
              @click="submitAttendance" 
              class="px-8 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              :disabled="submitting"
            >
              <!-- 
                Loading spinner (conditional)
                v-if="submitting" = hanya muncul saat sedang submit
              -->
              <svg 
                v-if="submitting" 
                class="animate-spin h-5 w-5" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              
              <!-- 
                Text tombol yang berubah saat loading
                Ternary operator: kondisi ? valueJikaTrue : valueJikaFalse
              -->
              <span>{{ submitting ? 'Saving...' : 'Save Attendance' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// ==================== IMPORT LIBRARY ====================
// Import function-function Vue yang kita butuhkan
import { ref, computed, onMounted } from 'vue';

// ==================== VARIABEL REACTIVE (Data yang bisa berubah) ====================
// ref() = bikin variabel yang "reactive" (Vue bisa track perubahannya)
// Setiap kali nilainya berubah, tampilan otomatis update

const attendances = ref([]);        // Array: data absensi yang sudah difilter (untuk ditampilkan di tabel)
const allAttendances = ref([]);     // Array: SEMUA data absensi (untuk hitung statistik)
const employees = ref([]);          // Array: list karyawan (untuk dropdown pilih karyawan)
const loading = ref(true);          // Boolean: true = lagi loading, false = sudah selesai
const submitting = ref(false);      // Boolean: true = lagi submit form, false = idle
const search = ref('');             // String: text yang diketik user di kotak pencarian
const selectedDate = ref('');       // String: tanggal yang dipilih di filter
const filterStatus = ref(null);     // String atau null: status yang dipilih di filter
const showModal = ref(false);       // Boolean: true = modal muncul, false = modal hilang
const errorMessage = ref('');       // String: pesan error yang ditampilkan ke user
const formErrors = ref({});         // Object: error untuk setiap field form (contoh: {employee_id: 'Required'})

// Object untuk simpan data form
// Setiap field di form akan nyimpan nilainya di sini
const form = ref({
  employee_id: '',           // ID karyawan yang dipilih
  date: '',                  // Tanggal absensi
  check_in_hour: '',         // Jam masuk (00-23)
  check_in_minute: '',       // Menit masuk (00-59)
  check_out_hour: '',        // Jam keluar (00-23)
  check_out_minute: '',      // Menit keluar (00-59)
  status: '',                // Status absensi (present, late, etc)
  is_night_shift: false,     // Boolean: apakah shift malam?
});

// ==================== PRESET WAKTU (untuk tombol quick select) ====================
// Array of object: list waktu yang sering dipakai
const timePresets = [
  { label: '08:00', time: '08:00', type: 'check_in' },   // Jam masuk pagi
  { label: '09:00', time: '09:00', type: 'check_in' },   // Jam masuk agak siang
  { label: '17:00', time: '17:00', type: 'check_out' },  // Jam pulang sore
  { label: '18:00', time: '18:00', type: 'check_out' },  // Jam pulang malam
  { label: '20:00', time: '20:00', type: 'check_in' },   // Jam masuk shift malam
  { label: '22:00', time: '22:00', type: 'check_in' },   // Jam masuk shift malam
  { label: '05:00', time: '05:00', type: 'check_out' },  // Jam pulang pagi (shift malam)
  { label: '06:00', time: '06:00', type: 'check_out' },  // Jam pulang pagi
];

// ==================== FUNGSI HELPER (Fungsi pembantu kecil) ====================

/**
 * FUNGSI: Tampilkan preview waktu yang dipilih
 * Parameter: hour (jam) dan minute (menit)
 * Return: String format "08:30" atau "--:--" kalau belum dipilih
 */
const getTimeDisplay = (hour, minute) => {
  // Kalau hour atau minute kosong, tampilkan placeholder
  if (!hour || !minute) return '--:--';
  
  // Backticks (`) untuk template literals = bisa masukin variable ke dalam string
  // ${hour} akan diganti dengan nilai hour
  return `${hour}:${minute}`;
};

/**
 * FUNGSI: Set waktu form dari tombol preset yang diklik
 * Parameter: preset object (contoh: {label: '08:00', time: '08:00', type: 'check_in'})
 */
const setTimePreset = (preset) => {
  // Split string "08:00" jadi array ["08", "00"]
  const [hour, minute] = preset.time.split(':');
  
  // Kalau preset untuk check_in ATAU check_in belum diisi
  if (preset.type === 'check_in' || !form.value.check_in_hour) {
    // Set jam masuk
    form.value.check_in_hour = hour;
    form.value.check_in_minute = minute;
  } else {
    // Kalau tidak, set jam keluar
    form.value.check_out_hour = hour;
    form.value.check_out_minute = minute;
  }
};

// ==================== FUNGSI FETCH DATA (Ambil data) ====================

/**
 * FUNGSI: Ambil dan filter data absensi
 * Dipanggil setiap kali user ubah filter (search, date, status)
 */
const fetchAttendances = async () => {
  try {
    loading.value = true; // Set loading jadi true (tampilkan spinner)
    
    // Ambil data dari localStorage (penyimpanan browser)
    const stored = localStorage.getItem('attendanceData');
    
    // Parse JSON string jadi JavaScript object/array
    // Kalau gak ada data, pakai array kosong []
    let allData = stored ? JSON.parse(stored) : [];
    
    // Mulai dari semua data
    let filtered = allData;
    
    // ===== FILTER 1: PENCARIAN NAMA =====
    if (search.value) {
      // Ubah ke lowercase supaya pencarian gak case-sensitive
      const searchLower = search.value.toLowerCase();
      
      // Filter: hanya ambil data yang nama employee-nya cocok
      filtered = filtered.filter(a => {
        const empName = a.employee?.name || ''; // Ambil nama employee (atau string kosong)
        return empName.toLowerCase().includes(searchLower); // Cek apakah mengandung kata kunci
      });
    }
    
    // ===== FILTER 2: TANGGAL =====
    if (selectedDate.value) {
      // Filter: hanya ambil data dengan tanggal yang sama
      filtered = filtered.filter(a => a.date === selectedDate.value);
    }
    
    // ===== FILTER 3: STATUS =====
    if (filterStatus.value) {
      // Filter: hanya ambil data dengan status yang dipilih
      filtered = filtered.filter(a => a.status === filterStatus.value);
    }
    
    // ===== SORTING: Urutkan dari tanggal terbaru =====
    filtered.sort((a, b) => {
      // new Date() ubah string tanggal jadi Date object
      // Kurangi untuk dapat urutan descending (terbaru dulu)
      return new Date(b.date) - new Date(a.date);
    });
    
    // Update state dengan data yang sudah difilter
    attendances.value = filtered;
    
  } catch (e) {
    // Kalau ada error, log ke console dan set array kosong
    console.error('Fetch attendance error:', e);
    attendances.value = [];
  } finally {
    // Finally block SELALU dijalankan (error atau tidak)
    loading.value = false; // Matikan loading
  }
};

/**
 * FUNGSI: Ambil SEMUA data absensi (tanpa filter)
 * Dipakai untuk hitung statistik di kartu atas
 */
const fetchAllAttendances = async () => {
  try {
    const stored = localStorage.getItem('attendanceData');
    allAttendances.value = stored ? JSON.parse(stored) : [];
  } catch (e) {
    allAttendances.value = [];
  }
};

/**
 * FUNGSI: Ambil data karyawan
 * Data ini untuk populate dropdown pilih karyawan
 */
const fetchEmployees = async () => {
  try {
    console.log('🔍 Loading employees from localStorage...');
    
    const stored = localStorage.getItem('employeesData');
    
    if (stored) {
      const data = JSON.parse(stored);
      
      // Filter hanya karyawan yang statusnya 'active'
      // e => e.status === 'active' adalah arrow function
      // Artinya: untuk setiap employee (e), cek apakah status-nya 'active'
      employees.value = data.filter(e => e.status === 'active');
      
      console.log('✅ Employees loaded:', employees.value.length);
    } else {
      employees.value = [];
      console.warn('⚠️ No employees found in localStorage!');
    }
  } catch (err) {
    console.error('❌ Error loading employees:', err);
    employees.value = [];
  }
};

// ==================== COMPUTED PROPERTIES (Variabel yang dihitung otomatis) ====================

/**
 * COMPUTED: Statistik untuk kartu atas
 * Computed property = variabel yang nilainya dihitung dari variabel lain
 * Otomatis update kalau dependencies-nya berubah
 * Lebih efisien daripada method karena di-cache
 */
const stats = computed(() => {
  const total = allAttendances.value.length; // Total semua record
  
  // Hitung yang hadir (present atau late)
  // .filter() = ambil yang memenuhi kondisi
  // .includes() = cek apakah ada di dalam array
  const present = allAttendances.value.filter((a) => 
    ['present', 'late'].includes(a.status)
  ).length;
  
  // Hitung yang cuti/sakit
  const onLeave = allAttendances.value.filter((a) => 
    ['sick', 'leave'].includes(a.status)
  ).length;
  
  // Hitung persentase kehadiran
  // Ternary operator: total ? (kalau ada) : (kalau 0)
  // Math.round() untuk bulatkan angka
  const rate = total ? Math.round((present / total) * 100) : 0;
  
  // Return object dengan semua statistik
  return { total, present, onLeave, rate };
});

/**
 * COMPUTED: Data untuk kartu statistik
 * Return array of objects untuk di-loop di template
 */
const statCards = computed(() => [
  { 
    label: 'Total Records',        // Label kartu
    value: stats.value.total,      // Nilai yang ditampilkan
    color: 'text-gray-800',        // Warna text
    bgColor: 'bg-gray-100'         // Warna background icon
  },
  { 
    label: 'Present', 
    value: stats.value.present, 
    color: 'text-green-600', 
    bgColor: 'bg-green-100' 
  },
  { 
    label: 'On Leave', 
    value: stats.value.onLeave, 
    color: 'text-orange-600', 
    bgColor: 'bg-orange-100' 
  },
  { 
    label: 'Attendance Rate', 
    value: `${stats.value.rate}%`,  // Tambah simbol %
    color: 'text-blue-600', 
    bgColor: 'bg-blue-100' 
  },
]);

// ==================== FUNGSI FORMATTER (Format tampilan data) ====================

/**
 * FUNGSI: Format tanggal untuk ditampilkan
 * Input: "2025-01-15" (format database)
 * Output: "15/01/2025" (format Indonesia)
 */
const formatDate = (d) => {
  if (!d) return '-'; // Kalau kosong, return dash
  
  try {
    // new Date() ubah string jadi Date object
    // toLocaleDateString() format sesuai locale (id-ID = Indonesia)
    return new Date(d).toLocaleDateString('id-ID', { 
      day: '2-digit',      // Hari 2 digit (01, 02, dst)
      month: '2-digit',    // Bulan 2 digit
      year: 'numeric'      // Tahun lengkap (2025)
    });
  } catch (e) {
    // Kalau error, return original string
    return d;
  }
};

/**
 * FUNGSI: Format waktu untuk ditampilkan
 * Input: "08:30:00" atau "08:30"
 * Output: "08:30" (hanya HH:MM)
 */
const formatTime = (t) => {
  if (!t) return '-'; // Kalau kosong, return dash
  
  if (t.includes(':')) {
    // Split string jadi array: "08:30:00" → ["08", "30", "00"]
    const parts = t.split(':');
    // Ambil hanya jam dan menit (index 0 dan 1)
    return `${parts[0]}:${parts[1]}`;
  }
  return t;
};

/**
 * FUNGSI: Translate status ke Bahasa Indonesia
 * Input: 'present', 'late', dll
 * Output: 'Hadir', 'Terlambat', dll
 */
const formatStatus = (s) => {
  // Object sebagai mapping/dictionary
  const statusMap = { 
    present: 'Hadir', 
    late: 'Terlambat', 
    absent: 'Tidak Hadir', 
    sick: 'Sakit', 
    leave: 'Izin' 
  };
  
  // statusMap[s] ambil value berdasarkan key
  // || s = fallback kalau key tidak ketemu, pakai value original
  return statusMap[s] || s;
};

/**
 * FUNGSI: Ambil CSS class untuk badge status
 * Return: string class yang sesuai dengan status
 */
const getStatusClass = (s) => {
  const classMap = {
    present: 'bg-green-100 text-green-700',   // Hijau untuk hadir
    late: 'bg-yellow-100 text-yellow-700',    // Kuning untuk terlambat
    absent: 'bg-red-100 text-red-700',        // Merah untuk tidak hadir
    sick: 'bg-blue-100 text-blue-700',        // Biru untuk sakit
    leave: 'bg-purple-100 text-purple-700',   // Ungu untuk izin
  };
  return classMap[s] || 'bg-gray-100 text-gray-700'; // Default abu-abu
};

// ==================== HANDLER PENCARIAN & FILTER ====================

/**
 * FUNGSI: Debounced search
 * Debounce = tunda eksekusi sampai user berhenti mengetik
 * Tujuan: supaya tidak terlalu banyak proses filtering (performance optimization)
 */
let searchDebounce; // Variable untuk simpan timeout ID

const debouncedSearch = () => {
  clearTimeout(searchDebounce); // Clear timeout sebelumnya (kalau ada)
  
  // Set timeout baru: tunggu 500ms (0.5 detik)
  // Kalau user ketik lagi sebelum 500ms, timeout di-clear dan ulang lagi
  searchDebounce = setTimeout(fetchAttendances, 500);
};

/**
 * FUNGSI: Handle perubahan filter (date atau status)
 * Langsung fetch tanpa debounce karena bukan typing
 */
const handleFilterChange = () => fetchAttendances();

// ==================== VALIDASI FORM ====================

/**
 * FUNGSI: Validasi data form sebelum submit
 * Return: true kalau valid, false kalau ada error
 */
const validateForm = () => {
  // Reset errors setiap kali validasi
  formErrors.value = {};
  errorMessage.value = '';
  
  // ===== VALIDASI 1: EMPLOYEE WAJIB DIISI =====
  if (!form.value.employee_id) {
    formErrors.value.employee_id = 'Employee is required';
  } else {
    // Cek apakah employee_id valid (ada di list employees)
    // .some() = cek apakah ada minimal 1 yang memenuhi kondisi
    const employeeExists = employees.value.some(emp => emp.id == form.value.employee_id);
    
    if (!employeeExists) {
      formErrors.value.employee_id = 'Selected employee is invalid';
      errorMessage.value = '⚠️ Please select a valid employee from the list';
    }
  }
  
  // ===== VALIDASI 2: TANGGAL WAJIB DIISI =====
  if (!form.value.date) {
    formErrors.value.date = 'Date is required';
  }
  
  // ===== VALIDASI 3: STATUS WAJIB DIISI =====
  if (!form.value.status) {
    formErrors.value.status = 'Status is required';
  }

  // ===== VALIDASI 4: LOGIKA WAKTU CHECK IN/OUT =====
  
  // Build string waktu dari jam dan menit
  // Pakai backticks untuk template literals (PENTING!)
  const checkIn = (form.value.check_in_hour && form.value.check_in_minute) 
    ? `${form.value.check_in_hour}:${form.value.check_in_minute}` 
    : null;
    
  const checkOut = (form.value.check_out_hour && form.value.check_out_minute) 
    ? `${form.value.check_out_hour}:${form.value.check_out_minute}` 
    : null;

  // Kalau ada check in dan check out, DAN bukan shift malam
  if (checkIn && checkOut && !form.value.is_night_shift) {
    // Check out harus lebih besar dari check in (regular shift)
    if (checkOut <= checkIn) {
      errorMessage.value = '⚠️ Check Out must be after Check In, or enable "Night Shift" if crossing midnight.';
      return false; // Validasi gagal
    }
  }

  // Validasi untuk shift malam
  if (checkIn && checkOut && form.value.is_night_shift) {
    const checkInHour = parseInt(form.value.check_in_hour);
    const checkOutHour = parseInt(form.value.check_out_hour);
    
    // Shift malam biasanya masuk malam (>= 18:00) dan pulang pagi (< 12:00)
    // Kalau check out > check in dan check in < 12, kemungkinan bukan shift malam
    if (checkOutHour > checkInHour && checkInHour < 12) {
      errorMessage.value = '⚠️ Night shift typically starts evening/night and ends morning. Uncheck "Night Shift" for regular shifts.';
      return false;
    }
  }
  
  // Return true kalau tidak ada error
  // Object.keys() ambil semua key dari object
  // .length untuk hitung jumlah key (jumlah error)
  return Object.keys(formErrors.value).length === 0;
};

// ==================== FUNGSI MODAL ====================

/**
 * FUNGSI: Buka modal untuk tambah absensi
 * Reset form dengan nilai default
 */
const openCreateModal = () => {
  // Reset form dengan default values
  form.value = {
    employee_id: '',
    date: new Date().toISOString().split('T')[0], // Tanggal hari ini
    check_in_hour: '08',   // Default jam masuk 08:00
    check_in_minute: '00',
    check_out_hour: '17',  // Default jam keluar 17:00
    check_out_minute: '00',
    status: '',
    is_night_shift: false,
  };
  
  // Reset error messages
  errorMessage.value = '';
  formErrors.value = {};
  
  // Tampilkan modal
  showModal.value = true;
};

/**
 * FUNGSI: Tutup modal
 */
const closeModal = () => {
  showModal.value = false;
  errorMessage.value = '';
  formErrors.value = {};
};

/**
 * FUNGSI: Redirect ke halaman employees
 * Dipanggil kalau user klik "Go to Employees Page"
 */
const goToEmployees = () => {
  window.location.href = '/employees';
};

// ==================== FUNGSI SUBMIT (SIMPAN DATA) ====================

/**
 * FUNGSI UTAMA: Submit form attendance
 * Ini fungsi paling penting - proses simpan data absensi
 */
const submitAttendance = async () => {
  // ===== STEP 1: VALIDASI FORM =====
  if (!validateForm()) {
    // Kalau validasi gagal, tampilkan error
    if (!errorMessage.value) {
      errorMessage.value = 'Please fill all required fields correctly';
    }
    return; // Stop eksekusi
  }

  // ===== STEP 2: PARSE EMPLOYEE ID =====
  // parseInt() ubah string jadi number
  const employeeId = parseInt(form.value.employee_id);
  
  // isNaN() cek apakah Not a Number
  if (isNaN(employeeId)) {
    errorMessage.value = '⚠️ Invalid employee ID format';
    return;
  }

  // ===== STEP 3: AMBIL DATA EMPLOYEE =====
  // .find() cari employee dengan ID yang sesuai
  const employee = employees.value.find(e => e.id === employeeId);
  
  if (!employee) {
    errorMessage.value = '⚠️ Employee not found';
    return;
  }

  // ===== STEP 4: BUILD OBJECT DATA BARU =====
  const newAttendance = {
    id: Date.now(), // ID sederhana dari timestamp (milliseconds sejak 1970)
    employee_id: employeeId,
    employee: {
      name: employee.name,
      // employee_code pakai backticks untuk template literals
      employee_code: employee.employee_id || `EMP-${employee.id}`
    },
    date: form.value.date,
    status: form.value.status,
    created_at: new Date().toISOString() // Timestamp kapan dibuat
  };

  // ===== STEP 5: TAMBAH CHECK IN TIME (kalau ada) =====
  if (form.value.check_in_hour && form.value.check_in_minute) {
    // padStart(2, '0') tambah 0 di depan kalau cuma 1 digit
    const hour = String(form.value.check_in_hour).padStart(2, '0');
    const minute = String(form.value.check_in_minute).padStart(2, '0');
    
    // Pakai backticks untuk template literals!
    newAttendance.check_in = `${hour}:${minute}`;
  }

  // ===== STEP 6: TAMBAH CHECK OUT TIME (kalau ada) =====
  if (form.value.check_out_hour && form.value.check_out_minute) {
    const hour = String(form.value.check_out_hour).padStart(2, '0');
    const minute = String(form.value.check_out_minute).padStart(2, '0');
    
    newAttendance.check_out = `${hour}:${minute}`;
    
    // ===== STEP 7: HANDLE NIGHT SHIFT =====
    if (form.value.is_night_shift) {
      newAttendance.is_night_shift = true;
      
      // Hitung tanggal besok untuk check out
      const checkOutDate = new Date(form.value.date);
      checkOutDate.setDate(checkOutDate.getDate() + 1); // Tambah 1 hari
      newAttendance.check_out_date = checkOutDate.toISOString().split('T')[0];
    }
  }

  // ===== STEP 8-15: SIMPAN KE LOCALSTORAGE =====
  try {
    submitting.value = true; // Aktifkan loading state
    errorMessage.value = '';
    
    // Ambil data existing dari localStorage
    const stored = localStorage.getItem('attendanceData');
    let allAttendances = stored ? JSON.parse(stored) : [];
    
    // ===== CEK DUPLICATE =====
    // Cek apakah sudah ada absensi untuk employee ini di tanggal yang sama
    const duplicate = allAttendances.find(a => 
      a.employee_id === employeeId && a.date === form.value.date
    );
    
    if (duplicate) {
      errorMessage.value = '⚠️ Attendance for this employee on this date already exists!';
      submitting.value = false;
      return;
    }
    
    // ===== TAMBAH DATA BARU =====
    // .unshift() tambah ke awal array (supaya yang baru muncul duluan)
    allAttendances.unshift(newAttendance);
    
    // ===== SIMPAN KE LOCALSTORAGE =====
    // JSON.stringify() ubah object jadi string (karena localStorage cuma bisa simpan string)
    localStorage.setItem('attendanceData', JSON.stringify(allAttendances));
    
    // ===== NOTIFIKASI SUCCESS =====
    alert('✅ Attendance added successfully!');
    
    // ===== TUTUP MODAL & REFRESH DATA =====
    closeModal();
    await fetchAttendances();     // Refresh tabel
    await fetchAllAttendances();  // Refresh statistik
    
  } catch (err) {
    // ===== ERROR HANDLING =====
    console.error('❌ Error:', err);
    errorMessage.value = '❌ Failed to save attendance. Please try again.';
  } finally {
    // ===== ALWAYS RESET LOADING STATE =====
    // Finally block selalu dijalankan (error atau success)
    submitting.value = false;
  }
};

// ==================== LIFECYCLE HOOKS ====================

/**
 * onMounted = dipanggil SETELAH component selesai di-render ke DOM
 * Ini tempat ideal untuk fetch data awal
 */
onMounted(async () => {
  console.log('🚀 Attendance page mounted');
  
  // Fetch data secara berurutan (await = tunggu selesai dulu)
  await fetchEmployees();      // 1. Load employees dulu (penting untuk dropdown)
  await fetchAllAttendances(); // 2. Load semua attendance (untuk statistik)
  await fetchAttendances();    // 3. Load attendance yang difilter (untuk tabel)
  
  console.log('✅ Initial data loaded');
});

</script>

<style scoped>
/* ==================== CUSTOM SCROLLBAR ==================== */
/* scoped = CSS ini hanya berlaku untuk component ini */

/* Styling scrollbar untuk browser webkit (Chrome, Safari, Edge) */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px; /* Lebar scrollbar */
}

/* Track = background scrollbar */
.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9; /* Warna abu-abu terang */
  border-radius: 10px;
}

/* Thumb = bagian yang bisa di-drag */
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1; /* Warna abu-abu medium */
  border-radius: 10px;
}

/* Thumb saat di-hover mouse */
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; /* Warna lebih gelap */
}
</style>