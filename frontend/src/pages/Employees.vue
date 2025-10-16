<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-indigo-100 p-4 md:p-8">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center justify-between gap-3 mb-2">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-800">
              Employee Management
            </h1>
            <p class="text-slate-500 text-sm mt-1">Manage your team members and departments</p>
          </div>
        </div>
        <!-- Add Button -->
        <button @click="openCreateModal" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all flex items-center gap-2 font-semibold">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Add Employee
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-500 text-sm font-medium">Total Employees</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ employees.length }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-500 text-sm font-medium">Departments</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ uniqueDepartments.length }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-500 text-sm font-medium">Active Status</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ activePercentage }}%</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="22 4 12 14 9 11"></polyline>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="max-w-7xl mx-auto mb-6">
      <div class="bg-white rounded-xl shadow-md p-4 border border-slate-200">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" v-model="search" @input="debouncedSearch" placeholder="Search employees..." class="w-full pl-10 pr-4 py-2 border-2 border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
          </div>
          <select v-model="filterStatus" @change="handleFilterChange" class="px-4 py-2 border-2 border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto flex justify-center items-center py-16">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600 mx-auto"></div>
        <p class="text-slate-600 mt-4 font-medium">Loading employees...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto">
      <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 text-center">
        <p class="text-red-800 font-semibold">{{ error }}</p>
        <button @click="fetchEmployees" class="mt-3 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
          Try Again
        </button>
      </div>
    </div>

    <!-- Employee Table -->
    <div v-else class="max-w-7xl mx-auto">
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="p-6 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
          <h2 class="text-xl font-bold text-slate-800">Employee Directory</h2>
          <span class="text-sm bg-purple-100 text-purple-700 font-semibold px-3 py-1 rounded-full">{{ filteredEmployees.length }} members</span>
        </div>

        <!-- Empty State -->
        <div v-if="filteredEmployees.length === 0" class="p-16 text-center">
          <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            </svg>
          </div>
          <p class="text-slate-400 font-medium text-lg">No employees found</p>
          <p class="text-slate-400 text-sm mt-1">Try adjusting your search or filters</p>
        </div>

        <!-- Table -->
        <table v-else class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-600 uppercase text-xs border-b border-slate-200">
            <tr>
              <th class="px-6 py-4 text-left font-semibold">Employee ID</th>
              <th class="px-6 py-4 text-left font-semibold">Name</th>
              <th class="px-6 py-4 text-left font-semibold">Position</th>
              <th class="px-6 py-4 text-left font-semibold">Department</th>
              <th class="px-6 py-4 text-left font-semibold">Email</th>
              <th class="px-6 py-4 text-left font-semibold">Status</th>
              <th class="px-6 py-4 text-left font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <tr v-for="emp in filteredEmployees" :key="emp.id" class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-mono font-semibold text-slate-700 bg-slate-100 px-2 py-1 rounded">
                  {{ emp.employee_id || `EMP-${emp.id.toString().slice(-4)}` }}
                </span>
              </td>
              <td class="px-6 py-4 font-semibold text-slate-800">{{ emp.name }}</td>
              <td class="px-6 py-4 text-slate-600">{{ emp.position }}</td>
              <td class="px-6 py-4 text-slate-600">{{ emp.department }}</td>
              <td class="px-6 py-4 text-slate-600 text-sm">{{ emp.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(emp.status)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold">
                  <span :class="getStatusDotClass(emp.status)" class="w-2 h-2 rounded-full"></span>
                  {{ emp.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex gap-2">
                  <button @click="openEditModal(emp)" class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded transition-colors" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </button>
                  <button @click="confirmDelete(emp)" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition-colors" title="Delete">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-96 overflow-y-auto">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6 flex items-center justify-between sticky top-0">
          <h2 class="text-2xl font-bold text-white">{{ modalMode === 'create' ? 'Add Employee' : 'Edit Employee' }}</h2>
          <button @click="closeModal" class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <!-- Employee ID -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Employee ID</label>
            <input 
              v-model="formData.employee_id"
              type="text" 
              placeholder="EMP-001"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-purple-500"
              :readonly="modalMode === 'edit'"
            />
          </div>

          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Name <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.name"
              type="text" 
              placeholder="Enter employee name"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-purple-500"
              required
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.email"
              type="email" 
              placeholder="employee@company.com"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-purple-500"
              required
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Phone <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.phone"
              type="tel" 
              placeholder="08xx-xxxx-xxxx"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-purple-500"
              required
            />
          </div>

          <!-- Position -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Position <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.position"
              type="text" 
              placeholder="e.g., Senior Developer"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-purple-500"
              required
            />
          </div>

          <!-- Department -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Department <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.department"
              type="text" 
              placeholder="e.g., IT, HR, Finance"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-purple-500"
              required
            />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
            <select 
              v-model="formData.status"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-purple-500"
              required
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 pt-4 border-t border-slate-200">
            <button 
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 font-medium transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="submitting"
              class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium disabled:bg-purple-400 transition-colors"
            >
              {{ submitting ? 'Saving...' : (modalMode === 'create' ? 'Add Employee' : 'Save Changes') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="cancelDelete">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Delete Employee</h3>
        <p class="text-slate-600 mb-6">Are you sure you want to delete <strong>{{ employeeToDelete?.name }}</strong>? This action cannot be undone.</p>
        <div class="flex gap-3">
          <button 
            @click="cancelDelete"
            class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 font-medium transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="executeDelete"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

const employees = ref([]);
const loading = ref(false);
const error = ref(null);
const search = ref("");
const filterStatus = ref("");

const showModal = ref(false);
const modalMode = ref("create");
const selectedEmployee = ref(null);
const submitting = ref(false);

const formData = ref({
  employee_id: "",
  name: "",
  email: "",
  phone: "",
  position: "",
  department: "",
  status: "active",
});

const showDeleteModal = ref(false);
const employeeToDelete = ref(null);

const uniqueDepartments = computed(() => 
  [...new Set(employees.value.map(e => e.department))].filter(Boolean)
);

const activePercentage = computed(() => {
  if (!employees.value.length) return 0;
  const activeCount = employees.value.filter(e => e.status === "active").length;
  return Math.round((activeCount / employees.value.length) * 100);
});

const filteredEmployees = computed(() => {
  let filtered = employees.value;

  if (search.value.trim()) {
    const searchLower = search.value.toLowerCase();
    filtered = filtered.filter(e => 
      e.name.toLowerCase().includes(searchLower) ||
      e.email.toLowerCase().includes(searchLower) ||
      e.position.toLowerCase().includes(searchLower) ||
      e.department.toLowerCase().includes(searchLower) ||
      (e.employee_id || '').toLowerCase().includes(searchLower)
    );
  }

  if (filterStatus.value) {
    filtered = filtered.filter(e => e.status === filterStatus.value);
  }

  return filtered;
});

const fetchEmployees = async () => {
  try {
    loading.value = true;
    error.value = null;

    const stored = localStorage.getItem('employeesData');
    if (stored) {
      const data = JSON.parse(stored);
      employees.value = data.sort((a, b) => 
        new Date(b.created_at || 0) - new Date(a.created_at || 0)
      );
    } else {
      employees.value = [];
    }
  } catch (err) {
    console.error('Error loading employees:', err);
    error.value = 'Failed to load employees. Please try again.';
  } finally {
    loading.value = false;
  }
};

let searchTimeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    // Filter is already applied via computed property
  }, 500);
};

const handleFilterChange = () => {
  // Filter is already applied via computed property
};

const openCreateModal = () => {
  modalMode.value = "create";
  formData.value = {
    employee_id: "",
    name: "",
    email: "",
    phone: "",
    position: "",
    department: "",
    status: "active",
  };
  selectedEmployee.value = null;
  showModal.value = true;
};

const openEditModal = (emp) => {
  modalMode.value = "edit";
  selectedEmployee.value = emp;
  formData.value = { ...emp };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedEmployee.value = null;
};

const handleSubmit = async () => {
  if (!formData.value.name || !formData.value.email || !formData.value.phone || 
      !formData.value.position || !formData.value.department) {
    alert('Please fill in all required fields');
    return;
  }

  try {
    submitting.value = true;

    const stored = localStorage.getItem('employeesData');
    let allEmployees = stored ? JSON.parse(stored) : [];

    if (modalMode.value === "create") {
      const newEmployee = {
        id: Date.now(),
        ...formData.value,
        created_at: new Date().toISOString()
      };
      allEmployees.unshift(newEmployee);
    } else {
      const index = allEmployees.findIndex(e => e.id === selectedEmployee.value.id);
      if (index !== -1) {
        allEmployees[index] = {
          ...allEmployees[index],
          ...formData.value,
          updated_at: new Date().toISOString()
        };
      }
    }

    localStorage.setItem('employeesData', JSON.stringify(allEmployees));
    await fetchEmployees();
    closeModal();
    alert(modalMode.value === 'create' ? 'Employee added successfully!' : 'Employee updated successfully!');
  } catch (err) {
    console.error('Error saving employee:', err);
    alert('Failed to save employee. Please try again.');
  } finally {
    submitting.value = false;
  }
};

const confirmDelete = (emp) => {
  employeeToDelete.value = emp;
  showDeleteModal.value = true;
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  employeeToDelete.value = null;
};

const executeDelete = async () => {
  if (!employeeToDelete.value) return;

  try {
    const stored = localStorage.getItem('employeesData');
    let allEmployees = stored ? JSON.parse(stored) : [];

    allEmployees = allEmployees.filter(e => e.id !== employeeToDelete.value.id);
    localStorage.setItem('employeesData', JSON.stringify(allEmployees));

    await fetchEmployees();
    cancelDelete();
    alert('Employee deleted successfully!');
  } catch (err) {
    console.error('Error deleting employee:', err);
    alert('Failed to delete employee. Please try again.');
  }
};

const getStatusClass = (status) => {
  if (status === 'active') {
    return 'bg-green-100 text-green-700';
  } else {
    return 'bg-red-100 text-red-700';
  }
};

const getStatusDotClass = (status) => {
  if (status === 'active') {
    return 'bg-green-500 animate-pulse';
  } else {
    return 'bg-red-500';
  }
};

onMounted(() => {
  fetchEmployees();
});
</script>