<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50 to-teal-50 p-4 md:p-8">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center justify-between gap-3 mb-2">
        <div class="flex items-center gap-3">
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

    <!-- Stats -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div
        v-for="stat in statCards"
        :key="stat.label"
        class="bg-white rounded-xl p-6 shadow-md border border-gray-200 hover:shadow-xl transition-all"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">{{ stat.label }}</p>
            <p :class="['text-3xl font-bold mt-1', stat.color]">{{ stat.value }}</p>
          </div>
          <div :class="['w-12 h-12 rounded-lg flex items-center justify-center', stat.bgColor]"></div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto mb-6">
      <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="relative flex-1">
            <input
              v-model="search"
              @input="debouncedSearch"
              placeholder="Search by employee name..."
              class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
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

          <input type="date" v-model="selectedDate" @change="handleFilterChange" class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500" />

          <select v-model="filterStatus" @change="handleFilterChange" class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500">
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

    <!-- Loading -->
    <div v-if="loading" class="max-w-7xl mx-auto flex justify-center items-center py-16">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-green-600 mx-auto"></div>
        <p class="text-gray-600 mt-4 font-medium">Loading attendance records...</p>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="max-w-7xl mx-auto">
      <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold text-gray-800">Attendance Records</h2>
        </div>

        <div v-if="attendances.length === 0" class="text-center py-16 text-gray-400 font-medium text-lg">
          No attendance records found
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th v-for="header in ['Employee', 'Date', 'Check In', 'Check Out', 'Status']" :key="header" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ header }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="attendance in attendances" :key="attendance.id">
                <td class="px-6 py-4 text-gray-800">
                  {{ attendance.employee?.name || ('Employee #' + attendance.employee_id) }}
                </td>
                <td class="px-6 py-4 text-gray-500">{{ formatDate(attendance.date) }}</td>
                <td class="px-6 py-4 text-gray-700">{{ formatTime(attendance.check_in) }}</td>
                <td class="px-6 py-4 text-gray-700">{{ formatTime(attendance.check_out) }}</td>
                <td class="px-6 py-4">
                  <span :class="getStatusClass(attendance.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                    {{ formatStatus(attendance.status) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Add -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 rounded-t-2xl sticky top-0 z-10">
          <h2 class="text-2xl font-bold text-white">Add Attendance</h2>
          <button @click="closeModal" class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors">✕</button>
        </div>

        <div class="p-6 space-y-5">
          <!-- Warning if no employees -->
          <div v-if="employees.length === 0" class="bg-orange-50 border-l-4 border-orange-500 text-orange-700 px-4 py-3 rounded">
            <div class="flex items-start gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
              <div>
                <p class="font-bold">No Employees Available</p>
                <p class="text-sm mt-1">Please add employees first before creating attendance records.</p>
                <button @click="goToEmployees" class="mt-2 text-sm underline hover:text-orange-900">Go to Employees Page →</button>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded text-sm">
            <p class="font-medium">{{ errorMessage }}</p>
          </div>

          <!-- Employee -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">
              Employee <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.employee_id"
              class="w-full border-2 rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
              :class="{ 'border-red-300 bg-red-50': formErrors.employee_id }"
            >
              <option disabled value="">Select Employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.name }} ({{ emp.employee_id || 'EMP-' + emp.id }})
              </option>
            </select>
            <p v-if="formErrors.employee_id" class="text-red-500 text-xs mt-1">{{ formErrors.employee_id }}</p>
            <p v-if="employees.length === 0" class="text-orange-500 text-xs mt-1">⚠️ No employees found. Please add employees first.</p>
            <p v-else class="text-gray-500 text-xs mt-1">{{ employees.length }} employee(s) available</p>
          </div>

          <!-- Date -->
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
            <p v-if="formErrors.date" class="text-red-500 text-xs mt-1">{{ formErrors.date }}</p>
          </div>

          <!-- Check In & Check Out -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Check In -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Check In</label>
              <div class="relative">
                <select 
                  v-model="form.check_in_hour"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Hour</option>
                  <option v-for="h in 24" :key="h-1" :value="String(h-1).padStart(2, '0')">
                    {{ String(h-1).padStart(2, '0') }}
                  </option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">▼</span>
              </div>
              <div class="relative mt-2">
                <select 
                  v-model="form.check_in_minute"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Minute</option>
                  <option v-for="m in 60" :key="m-1" :value="String(m-1).padStart(2, '0')">
                    {{ String(m-1).padStart(2, '0') }}
                  </option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">▼</span>
              </div>
              <p class="text-sm text-gray-500 mt-1">
                {{ getTimeDisplay(form.check_in_hour, form.check_in_minute) }}
              </p>
            </div>

            <!-- Check Out -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Check Out</label>
              <div class="relative">
                <select 
                  v-model="form.check_out_hour"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Hour</option>
                  <option v-for="h in 24" :key="h-1" :value="String(h-1).padStart(2, '0')">
                    {{ String(h-1).padStart(2, '0') }}
                  </option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">▼</span>
              </div>
              <div class="relative mt-2">
                <select 
                  v-model="form.check_out_minute"
                  class="w-full border-2 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-green-500 appearance-none"
                >
                  <option value="">Minute</option>
                  <option v-for="m in 60" :key="m-1" :value="String(m-1).padStart(2, '0')">
                    {{ String(m-1).padStart(2, '0') }}
                  </option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">▼</span>
              </div>
              <p class="text-sm text-gray-500 mt-1">
                {{ getTimeDisplay(form.check_out_hour, form.check_out_minute) }}
              </p>
            </div>
          </div>

          <!-- Quick Time Presets -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm font-semibold text-gray-700 mb-2">⚡ Quick Select:</p>
            <div class="grid grid-cols-4 gap-2">
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

          <!-- Night Shift Toggle -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
            <label class="flex items-start gap-3 cursor-pointer">
              <input 
                type="checkbox" 
                v-model="form.is_night_shift"
                class="w-5 h-5 text-green-600 focus:ring-green-500 rounded mt-0.5"
              />
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-gray-800">🌙 Night Shift</span>
                  <span class="text-xs bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full font-semibold">Optional</span>
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

          <!-- Status -->
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
            <p v-if="formErrors.status" class="text-red-500 text-xs mt-1">{{ formErrors.status }}</p>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 pt-4 border-t-2">
            <button 
              @click="closeModal" 
              class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors font-semibold"
              :disabled="submitting"
            >
              Cancel
            </button>
            <button 
              @click="submitAttendance" 
              class="px-8 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              :disabled="submitting"
            >
              <svg v-if="submitting" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ submitting ? 'Saving...' : 'Save Attendance' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

// STATE
const attendances = ref([]);
const allAttendances = ref([]);
const employees = ref([]);
const loading = ref(true);
const submitting = ref(false);
const search = ref('');
const selectedDate = ref('');
const filterStatus = ref(null);
const showModal = ref(false);
const errorMessage = ref('');
const formErrors = ref({});

const form = ref({
  employee_id: '',
  date: '',
  check_in_hour: '',
  check_in_minute: '',
  check_out_hour: '',
  check_out_minute: '',
  status: '',
  is_night_shift: false,
});

// Time Presets
const timePresets = [
  { label: '08:00', time: '08:00', type: 'check_in' },
  { label: '09:00', time: '09:00', type: 'check_in' },
  { label: '17:00', time: '17:00', type: 'check_out' },
  { label: '18:00', time: '18:00', type: 'check_out' },
  { label: '20:00', time: '20:00', type: 'check_in' },
  { label: '22:00', time: '22:00', type: 'check_in' },
  { label: '05:00', time: '05:00', type: 'check_out' },
  { label: '06:00', time: '06:00', type: 'check_out' },
];

// Helper to get time display
const getTimeDisplay = (hour, minute) => {
  if (!hour || !minute) return '--:--';
  return `${hour}:${minute}`;
};

// Set time preset
const setTimePreset = (preset) => {
  const [hour, minute] = preset.time.split(':');
  if (preset.type === 'check_in' || !form.value.check_in_hour) {
    form.value.check_in_hour = hour;
    form.value.check_in_minute = minute;
  } else {
    form.value.check_out_hour = hour;
    form.value.check_out_minute = minute;
  }
};

// FETCH
const fetchAttendances = async () => {
  try {
    loading.value = true;
    
    const stored = localStorage.getItem('attendanceData');
    let allData = stored ? JSON.parse(stored) : [];
    
    let filtered = allData;
    
    if (search.value) {
      const searchLower = search.value.toLowerCase();
      filtered = filtered.filter(a => {
        const empName = a.employee?.name || '';
        return empName.toLowerCase().includes(searchLower);
      });
    }
    
    if (selectedDate.value) {
      filtered = filtered.filter(a => a.date === selectedDate.value);
    }
    
    if (filterStatus.value) {
      filtered = filtered.filter(a => a.status === filterStatus.value);
    }
    
    filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
    
    attendances.value = filtered;
  } catch (e) {
    console.error('Fetch attendance error:', e);
    attendances.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchAllAttendances = async () => {
  try {
    const stored = localStorage.getItem('attendanceData');
    allAttendances.value = stored ? JSON.parse(stored) : [];
  } catch (e) {
    allAttendances.value = [];
  }
};

const fetchEmployees = async () => {
  try {
    console.log('🔍 Loading employees from localStorage...');
    
    const stored = localStorage.getItem('employeesData');
    if (stored) {
      const data = JSON.parse(stored);
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

// STATS
const stats = computed(() => {
  const total = allAttendances.value.length;
  const present = allAttendances.value.filter((a) => ['present', 'late'].includes(a.status)).length;
  const onLeave = allAttendances.value.filter((a) => ['sick', 'leave'].includes(a.status)).length;
  const rate = total ? Math.round((present / total) * 100) : 0;
  return { total, present, onLeave, rate };
});

const statCards = computed(() => [
  { label: 'Total Records', value: stats.value.total, color: 'text-gray-800', bgColor: 'bg-gray-100' },
  { label: 'Present', value: stats.value.present, color: 'text-green-600', bgColor: 'bg-green-100' },
  { label: 'On Leave', value: stats.value.onLeave, color: 'text-orange-600', bgColor: 'bg-orange-100' },
  { label: 'Attendance Rate', value: stats.value.rate + '%', color: 'text-blue-600', bgColor: 'bg-blue-100' },
]);

// HELPERS
const formatDate = (d) => {
  if (!d) return '-';
  try {
    return new Date(d).toLocaleDateString('id-ID', { 
      day: '2-digit', 
      month: '2-digit', 
      year: 'numeric' 
    });
  } catch (e) {
    return d;
  }
};

const formatTime = (t) => {
  if (!t) return '-';
  if (t.includes(':')) {
    const parts = t.split(':');
    return `${parts[0]}:${parts[1]}`;
  }
  return t;
};

const formatStatus = (s) =>
  ({ 
    present: 'Hadir', 
    late: 'Terlambat', 
    absent: 'Tidak Hadir', 
    sick: 'Sakit', 
    leave: 'Izin' 
  }[s] || s);

const getStatusClass = (s) =>
  ({
    present: 'bg-green-100 text-green-700',
    late: 'bg-yellow-100 text-yellow-700',
    absent: 'bg-red-100 text-red-700',
    sick: 'bg-blue-100 text-blue-700',
    leave: 'bg-purple-100 text-purple-700',
  }[s] || 'bg-gray-100 text-gray-700');

// SEARCH & FILTER
let searchDebounce;
const debouncedSearch = () => {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(fetchAttendances, 500);
};

const handleFilterChange = () => fetchAttendances();

// VALIDATION
const validateForm = () => {
  formErrors.value = {};
  errorMessage.value = '';
  
  if (!form.value.employee_id) {
    formErrors.value.employee_id = 'Employee is required';
  } else {
    const employeeExists = employees.value.some(emp => emp.id == form.value.employee_id);
    if (!employeeExists) {
      formErrors.value.employee_id = 'Selected employee is invalid';
      errorMessage.value = '⚠️ Please select a valid employee from the list';
    }
  }
  
  if (!form.value.date) {
    formErrors.value.date = 'Date is required';
  }
  
  if (!form.value.status) {
    formErrors.value.status = 'Status is required';
  }

  // ✅ PERBAIKAN: Pakai backticks untuk template literals
  const checkIn = (form.value.check_in_hour && form.value.check_in_minute) 
    ? `${form.value.check_in_hour}:${form.value.check_in_minute}` 
    : null;
  const checkOut = (form.value.check_out_hour && form.value.check_out_minute) 
    ? `${form.value.check_out_hour}:${form.value.check_out_minute}` 
    : null;

  if (checkIn && checkOut && !form.value.is_night_shift) {
    if (checkOut <= checkIn) {
      errorMessage.value = '⚠️ Check Out must be after Check In, or enable "Night Shift" if crossing midnight.';
      return false;
    }
  }

  if (checkIn && checkOut && form.value.is_night_shift) {
    const checkInHour = parseInt(form.value.check_in_hour);
    const checkOutHour = parseInt(form.value.check_out_hour);
    
    if (checkOutHour > checkInHour && checkInHour < 12) {
      errorMessage.value = '⚠️ Night shift typically starts evening/night and ends morning. Uncheck "Night Shift" for regular shifts.';
      return false;
    }
  }
  
  return Object.keys(formErrors.value).length === 0;
};

// MODAL
const openCreateModal = () => {
  form.value = {
    employee_id: '',
    date: new Date().toISOString().split('T')[0],
    check_in_hour: '08',
    check_in_minute: '00',
    check_out_hour: '17',
    check_out_minute: '00',
    status: '',
    is_night_shift: false,
  };
  errorMessage.value = '';
  formErrors.value = {};
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  errorMessage.value = '';
  formErrors.value = {};
};

const goToEmployees = () => {
  window.location.href = '/employees';
};

// SUBMIT
const submitAttendance = async () => {
  if (!validateForm()) {
    if (!errorMessage.value) {
      errorMessage.value = 'Please fill all required fields correctly';
    }
    return;
  }

  const employeeId = parseInt(form.value.employee_id);
  if (isNaN(employeeId)) {
    errorMessage.value = '⚠️ Invalid employee ID format';
    return;
  }

  const employee = employees.value.find(e => e.id === employeeId);
  if (!employee) {
    errorMessage.value = '⚠️ Employee not found';
    return;
  }

  const newAttendance = {
    id: Date.now(),
    employee_id: employeeId,
    employee: {
      name: employee.name,
      employee_code: employee.employee_id || `EMP-${employee.id}` // ✅ Backticks
    },
    date: form.value.date,
    status: form.value.status,
    created_at: new Date().toISOString()
  };

  if (form.value.check_in_hour && form.value.check_in_minute) {
    const hour = String(form.value.check_in_hour).padStart(2, '0');
    const minute = String(form.value.check_in_minute).padStart(2, '0');
    newAttendance.check_in = `${hour}:${minute}`; // ✅ Backticks
  }

  if (form.value.check_out_hour && form.value.check_out_minute) {
    const hour = String(form.value.check_out_hour).padStart(2, '0');
    const minute = String(form.value.check_out_minute).padStart(2, '0');
    newAttendance.check_out = `${hour}:${minute}`; // ✅ Backticks
    
    if (form.value.is_night_shift) {
      newAttendance.is_night_shift = true;
      const checkOutDate = new Date(form.value.date);
      checkOutDate.setDate(checkOutDate.getDate() + 1);
      newAttendance.check_out_date = checkOutDate.toISOString().split('T')[0];
    }
  }

  try {
    submitting.value = true;
    errorMessage.value = '';
    
    const stored = localStorage.getItem('attendanceData');
    let allAttendances = stored ? JSON.parse(stored) : [];
    
    const duplicate = allAttendances.find(a => 
      a.employee_id === employeeId && a.date === form.value.date
    );
    
    if (duplicate) {
      errorMessage.value = '⚠️ Attendance for this employee on this date already exists!';
      submitting.value = false;
      return;
    }
    
    allAttendances.unshift(newAttendance);
    localStorage.setItem('attendanceData', JSON.stringify(allAttendances));
    
    alert('✅ Attendance added successfully!');
    closeModal();
    await fetchAttendances();
    await fetchAllAttendances();
  } catch (err) {
    console.error('❌ Error:', err);
    errorMessage.value = '❌ Failed to save attendance. Please try again.';
  } finally {
    submitting.value = false;
  }
};

// LIFECYCLE
onMounted(async () => {
  console.log('🚀 Attendance page mounted');
  await fetchEmployees();
  await fetchAllAttendances();
  await fetchAttendances();
  console.log('✅ Initial data loaded');
});


</script>
<style scoped>
/* Custom scrollbar */
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
</style>