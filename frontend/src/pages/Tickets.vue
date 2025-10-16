<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-amber-50 to-yellow-100 p-4 md:p-8">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="12" y1="18" x2="12" y2="12"></line>
            <line x1="9" y1="15" x2="15" y2="15"></line>
          </svg>
        </div>
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-slate-800">
            Support Tickets
          </h1>
          <p class="text-slate-500 text-sm mt-1">Track and manage customer support tickets</p>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-500 text-sm font-medium">Total Tickets</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.total }}</p>
          </div>
          <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-500 text-sm font-medium">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ stats.pending }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-500 text-sm font-medium">Resolved</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.resolved }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-md border border-slate-200 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-500 text-sm font-medium">High Priority</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ stats.highPriority }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="max-w-7xl mx-auto mb-6">
      <div class="bg-white rounded-xl shadow-md p-4 border border-slate-200">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input
              type="text"
              v-model="search"
              @input="debouncedSearch"
              placeholder="Search by ticket title..."
              class="w-full pl-10 pr-4 py-2 border-2 border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
            />
          </div>

          <select 
            v-model="filterStatus" 
            @change="handleFilterChange"
            class="px-4 py-2 border-2 border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500"
          >
            <option :value="null">All Status</option>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
            <option value="Closed">Closed</option>
          </select>

          <select 
            v-model="filterPriority" 
            @change="handleFilterChange"
            class="px-4 py-2 border-2 border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500"
          >
            <option :value="null">All Priorities</option>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto flex justify-center items-center py-16">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-amber-600 mx-auto"></div>
        <p class="text-slate-600 mt-4 font-medium">Loading tickets...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto">
      <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-red-500 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <p class="text-red-800 font-semibold mb-2">{{ error }}</p>
        <button @click="fetchTickets" class="mt-3 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
          Try Again
        </button>
      </div>
    </div>

    <!-- Tickets Table -->
    <div v-else class="max-w-7xl mx-auto">
      <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <!-- Table Header -->
        <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-amber-50">
          <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
              <h2 class="text-xl font-bold text-slate-800">Active Tickets</h2>
              <p class="text-sm text-slate-500 mt-1">Monitor and respond to support requests</p>
            </div>
            <button 
              @click="openCreateModal"
              class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-4 py-2 rounded-lg font-semibold hover:from-amber-700 hover:to-orange-700 transition-all shadow-md flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              New Ticket
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="tickets.length === 0" class="text-center py-16">
          <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
          </div>
          <p class="text-slate-400 font-medium text-lg">No tickets found</p>
          <p class="text-slate-400 text-sm mt-1">Try adjusting your search or filters</p>
        </div>

        <!-- Table Content -->
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                  Ticket ID
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                  Issue Title
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                  Priority
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                  Created
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr 
                v-for="ticket in tickets" 
                :key="ticket.id" 
                class="hover:bg-slate-50 transition-colors duration-150"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-mono font-semibold text-slate-700 bg-slate-100 px-2 py-1 rounded">
                    {{ ticket.ticket_code }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-800">{{ ticket.title }}</p>
                      <p class="text-xs text-slate-500">{{ truncateText(ticket.description, 50) }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(ticket.status)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold">
                    <span :class="getStatusDotClass(ticket.status)" class="w-2 h-2 rounded-full"></span>
                    {{ ticket.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getPriorityClass(ticket.priority)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="12" y1="19" x2="12" y2="5"></line>
                      <polyline points="5 12 12 5 19 12"></polyline>
                    </svg>
                    {{ ticket.priority }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {{ formatDate(ticket.created_at) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex gap-2">
                    <button 
                      @click="viewDetails(ticket)"
                      class="text-amber-600 hover:text-amber-700 font-medium text-sm flex items-center gap-1 transition-colors"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                      View
                    </button>
                    <button 
                      @click="deleteTicket(ticket.id)"
                      class="text-red-600 hover:text-red-700 font-medium text-sm flex items-center gap-1 transition-colors"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      </svg>
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Table Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
          <p class="text-sm text-slate-600">
            Showing <span class="font-semibold text-slate-800">{{ tickets.length }}</span> tickets
          </p>
          <div class="flex items-center gap-2">
            <span class="text-sm text-slate-600">Response Rate:</span>
            <span class="text-sm font-bold text-amber-600">{{ responseRate }}%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Create/Edit Ticket -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-96 overflow-y-auto">
        <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-6 flex items-center justify-between rounded-t-2xl">
          <h2 class="text-2xl font-bold text-white">New Ticket</h2>
          <button @click="closeModal" class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitTicket" class="p-6 space-y-4">
          <!-- Ticket Code -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Ticket Code <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.ticket_code"
              type="text" 
              placeholder="TKT-001"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-amber-500"
              required
            />
          </div>

          <!-- Title -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Issue Title <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.title"
              type="text" 
              placeholder="Enter issue title"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-amber-500"
              required
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Description <span class="text-red-500">*</span></label>
            <textarea 
              v-model="formData.description"
              placeholder="Enter ticket description"
              rows="3"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-amber-500 resize-none"
              required
            ></textarea>
          </div>

          <!-- Status -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
              <select 
                v-model="formData.status"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-amber-500"
                required
              >
                <option value="">Select status</option>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Resolved">Resolved</option>
                <option value="Closed">Closed</option>
              </select>
            </div>

            <!-- Priority -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Priority <span class="text-red-500">*</span></label>
              <select 
                v-model="formData.priority"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-amber-500"
                required
              >
                <option value="">Select priority</option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
              </select>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 pt-4">
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
              class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium disabled:bg-amber-400 transition-colors"
            >
              {{ submitting ? 'Creating...' : 'Create Ticket' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const tickets = ref([]);
const loading = ref(false);
const error = ref(null);
const search = ref('');
const filterStatus = ref(null);
const filterPriority = ref(null);
const showModal = ref(false);
const submitting = ref(false);

const formData = ref({
  ticket_code: '',
  title: '',
  description: '',
  status: '',
  priority: ''
});

const stats = computed(() => {
  const total = tickets.value.length;
  const pending = tickets.value.filter(t => t.status === 'Pending').length;
  const resolved = tickets.value.filter(t => t.status === 'Resolved').length;
  const highPriority = tickets.value.filter(t => t.priority === 'High').length;

  return { total, pending, resolved, highPriority };
});

const responseRate = computed(() => {
  const total = tickets.value.length;
  if (total === 0) return 0;
  
  const responded = tickets.value.filter(t => 
    t.status === 'Resolved' || t.status === 'In Progress'
  ).length;
  
  return Math.round((responded / total) * 100);
});

const fetchTickets = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    // Load dari localStorage
    const stored = localStorage.getItem('ticketsData');
    if (stored) {
      let allTickets = JSON.parse(stored);
      
      // Apply filters
      if (search.value.trim()) {
        allTickets = allTickets.filter(t => 
          t.title.toLowerCase().includes(search.value.toLowerCase()) ||
          t.ticket_code.toLowerCase().includes(search.value.toLowerCase())
        );
      }
      
      if (filterStatus.value) {
        allTickets = allTickets.filter(t => t.status === filterStatus.value);
      }
      
      if (filterPriority.value) {
        allTickets = allTickets.filter(t => t.priority === filterPriority.value);
      }
      
      tickets.value = allTickets;
    } else {
      tickets.value = [];
    }
  } catch (err) {
    console.error('Error loading tickets:', err);
    error.value = 'Failed to load tickets. Please try again.';
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  resetForm();
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  formData.value = {
    ticket_code: '',
    title: '',
    description: '',
    status: '',
    priority: ''
  };
};

const submitTicket = async () => {
  if (!formData.value.ticket_code || !formData.value.title || !formData.value.description || !formData.value.status || !formData.value.priority) {
    alert('Please fill in all required fields');
    return;
  }

  try {
    submitting.value = true;

    const newTicket = {
      id: Date.now(),
      ticket_code: formData.value.ticket_code,
      title: formData.value.title,
      description: formData.value.description,
      status: formData.value.status,
      priority: formData.value.priority,
      created_at: new Date().toISOString()
    };

    // Load existing tickets
    const stored = localStorage.getItem('ticketsData');
    let allTickets = stored ? JSON.parse(stored) : [];
    
    // Add new ticket
    allTickets.unshift(newTicket);
    
    // Save to localStorage
    localStorage.setItem('ticketsData', JSON.stringify(allTickets));

    // Update state
    tickets.value = allTickets;
    
    // Close modal and show success
    closeModal();
    alert('Ticket created successfully!');
    
    // Refresh data
    await fetchTickets();
  } catch (err) {
    console.error('Error creating ticket:', err);
    alert('Failed to create ticket. Please try again.');
  } finally {
    submitting.value = false;
  }
};

const deleteTicket = async (ticketId) => {
  if (!confirm('Are you sure you want to delete this ticket?')) {
    return;
  }

  try {
    const stored = localStorage.getItem('ticketsData');
    let allTickets = stored ? JSON.parse(stored) : [];
    
    // Remove ticket
    allTickets = allTickets.filter(t => t.id !== ticketId);
    
    // Save to localStorage
    localStorage.setItem('ticketsData', JSON.stringify(allTickets));

    // Update state
    tickets.value = allTickets;
    
    alert('Ticket deleted successfully!');
  } catch (err) {
    console.error('Error deleting ticket:', err);
    alert('Failed to delete ticket. Please try again.');
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now - date;
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);
  
  if (diffMins < 60) return `${diffMins} minutes ago`;
  if (diffHours < 24) return `${diffHours} hours ago`;
  if (diffDays === 1) return '1 day ago';
  if (diffDays < 7) return `${diffDays} days ago`;
  
  return date.toLocaleDateString('id-ID', { 
    day: '2-digit', 
    month: 'short', 
    year: 'numeric' 
  });
};

const truncateText = (text, length) => {
  if (!text) return 'No description';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const getStatusClass = (status) => {
  const classes = {
    'Pending': 'bg-yellow-100 text-yellow-700',
    'In Progress': 'bg-blue-100 text-blue-700',
    'Resolved': 'bg-green-100 text-green-700',
    'Closed': 'bg-slate-100 text-slate-700'
  };
  return classes[status] || 'bg-gray-100 text-gray-700';
};

const getStatusDotClass = (status) => {
  const classes = {
    'Pending': 'bg-yellow-500 animate-pulse',
    'In Progress': 'bg-blue-500 animate-pulse',
    'Resolved': 'bg-green-500',
    'Closed': 'bg-slate-500'
  };
  return classes[status] || 'bg-gray-500';
};

const getPriorityClass = (priority) => {
  const classes = {
    'High': 'bg-red-100 text-red-700',
    'Medium': 'bg-orange-100 text-orange-700',
    'Low': 'bg-blue-100 text-blue-700'
  };
  return classes[priority] || 'bg-gray-100 text-gray-700';
};

let searchTimeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchTickets();
  }, 500);
};

const handleFilterChange = () => {
  fetchTickets();
};

const viewDetails = (ticket) => {
  console.log('View details for ticket:', ticket);
  alert(`Ticket: ${ticket.ticket_code}\nTitle: ${ticket.title}\nStatus: ${ticket.status}\nPriority: ${ticket.priority}`);
};

onMounted(() => {
  fetchTickets();
});
</script>