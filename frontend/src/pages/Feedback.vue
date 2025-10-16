<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-pink-50 to-rose-100 p-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-4xl font-bold text-slate-800 mb-2">Customer Feedback</h1>
          <p class="text-slate-600">Listen to what customers are saying about your business</p>
        </div>
        <button @click="openModal" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
          + Add Feedback
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-md">
          <p class="text-slate-500 text-sm font-medium">Total Feedback</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">{{ stats.total }}</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-md">
          <p class="text-slate-500 text-sm font-medium">Positive</p>
          <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.positive }}</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-md">
          <p class="text-slate-500 text-sm font-medium">Neutral</p>
          <p class="text-3xl font-bold text-orange-600 mt-1">{{ stats.neutral }}</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-md">
          <p class="text-slate-500 text-sm font-medium">Negative</p>
          <p class="text-3xl font-bold text-red-600 mt-1">{{ stats.negative }}</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-pink-600 mx-auto"></div>
        <p class="text-slate-600 mt-4">Loading feedback...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border-2 border-red-200 rounded-xl p-6 text-center">
        <p class="text-red-800 font-semibold">{{ error }}</p>
        <button @click="fetchFeedback" class="mt-3 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
          Try Again
        </button>
      </div>

      <!-- Feedback List -->
      <div v-else class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Recent Feedback ({{ feedback.length }})</h2>

        <!-- Empty State -->
        <div v-if="feedback.length === 0" class="text-center py-16">
          <p class="text-slate-400 text-lg">No feedback found</p>
        </div>

        <!-- Feedback Cards -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="fb in feedback" :key="fb.id" 
               class="bg-gradient-to-br from-white to-slate-50 rounded-xl p-6 border-2 border-slate-200 hover:border-pink-300 hover:shadow-lg transition-all">
            
            <!-- Sentiment Badge -->
            <div class="flex justify-between items-start mb-3">
              <span :class="getSentimentClass(fb.sentiment)" 
                    class="px-3 py-1 rounded-full text-xs font-semibold capitalize">
                {{ fb.sentiment }}
              </span>
              <div class="flex gap-1 items-center">
                <span v-for="i in fb.rating" :key="i" class="text-yellow-400">⭐</span>
                <span class="text-slate-400 text-sm ml-1">{{ fb.rating }}/5</span>
              </div>
            </div>

            <!-- Customer Info -->
            <div class="mb-3">
              <h3 class="font-bold text-slate-800 text-lg">{{ fb.customer_name }}</h3>
              <p class="text-slate-500 text-sm">{{ fb.email || 'No email' }}</p>
            </div>

            <!-- Message -->
            <p class="text-slate-700 text-sm mb-4 line-clamp-3">{{ fb.message }}</p>

            <!-- Date -->
            <p class="text-slate-400 text-xs">{{ formatDate(fb.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Add Feedback -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-slate-800">Add New Feedback</h2>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 text-2xl">✕</button>
        </div>

        <form @submit.prevent="submitFeedback" class="space-y-4">
          <!-- Customer Name -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Customer Name <span class="text-red-500">*</span></label>
            <input 
              v-model="formData.customer_name"
              type="text" 
              placeholder="Enter customer name"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-pink-500"
              required
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
            <input 
              v-model="formData.email"
              type="email" 
              placeholder="Enter email"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-pink-500"
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
            <input 
              v-model="formData.phone"
              type="tel" 
              placeholder="Enter phone number"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-pink-500"
            />
          </div>

          <!-- Rating -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Rating <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-2">
              <select 
                v-model.number="formData.rating"
                class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-pink-500"
                required
              >
                <option value="">Select rating</option>
                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                <option value="4">⭐⭐⭐⭐ Very Good</option>
                <option value="3">⭐⭐⭐ Good</option>
                <option value="2">⭐⭐ Fair</option>
                <option value="1">⭐ Poor</option>
              </select>
            </div>
          </div>

          <!-- Sentiment -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Sentiment <span class="text-red-500">*</span></label>
            <select 
              v-model="formData.sentiment"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-pink-500"
              required
            >
              <option value="">Select sentiment</option>
              <option value="positive">Positive</option>
              <option value="neutral">Neutral</option>
              <option value="negative">Negative</option>
            </select>
          </div>

          <!-- Feedback Message -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Feedback Message <span class="text-red-500">*</span></label>
            <textarea 
              v-model="formData.message"
              placeholder="Enter feedback message"
              rows="3"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-pink-500 resize-none"
              required
            ></textarea>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 pt-4">
            <button 
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 font-medium"
            >
              Close
            </button>
            <button 
              type="submit"
              :disabled="submitting"
              class="flex-1 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-medium disabled:bg-pink-400"
            >
              {{ submitting ? 'Submitting...' : 'Submit Feedback' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const feedback = ref([]);
const loading = ref(true);
const error = ref(null);
const showModal = ref(false);
const submitting = ref(false);

const formData = ref({
  customer_name: '',
  email: '',
  phone: '',
  rating: '',
  sentiment: '',
  message: ''
});

const stats = computed(() => {
  const total = feedback.value.length;
  const positive = feedback.value.filter(f => f.sentiment === 'positive').length;
  const neutral = feedback.value.filter(f => f.sentiment === 'neutral').length;
  const negative = feedback.value.filter(f => f.sentiment === 'negative').length;
  return { total, positive, neutral, negative };
});

const fetchFeedback = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    // Fetch from localStorage atau API
    const stored = localStorage.getItem('feedbackData');
    if (stored) {
      feedback.value = JSON.parse(stored);
    } else {
      // Data default jika belum ada
      feedback.value = [];
    }
  } catch (err) {
    console.error('Error loading feedback:', err);
    error.value = 'Failed to load feedback. Please try again.';
  } finally {
    loading.value = false;
  }
};

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  formData.value = {
    customer_name: '',
    email: '',
    phone: '',
    rating: '',
    sentiment: '',
    message: ''
  };
};

const submitFeedback = async () => {
  if (!formData.value.customer_name || !formData.value.rating || !formData.value.sentiment || !formData.value.message) {
    alert('Please fill in all required fields');
    return;
  }

  try {
    submitting.value = true;

    const newFeedback = {
      id: Date.now(),
      customer_name: formData.value.customer_name,
      email: formData.value.email,
      phone: formData.value.phone,
      rating: parseInt(formData.value.rating),
      sentiment: formData.value.sentiment,
      message: formData.value.message,
      created_at: new Date().toISOString()
    };

    // Simpan ke localStorage
    feedback.value.unshift(newFeedback);
    localStorage.setItem('feedbackData', JSON.stringify(feedback.value));

    // Reset form dan tutup modal
    resetForm();
    showModal.value = false;
    
    // Tampilkan notifikasi sukses
    alert('Feedback submitted successfully!');
  } catch (err) {
    console.error('Error submitting feedback:', err);
    alert('Failed to submit feedback. Please try again.');
  } finally {
    submitting.value = false;
  }
};

const getSentimentClass = (sentiment) => {
  const classes = {
    'positive': 'bg-green-100 text-green-700',
    'neutral': 'bg-orange-100 text-orange-700',
    'negative': 'bg-red-100 text-red-700'
  };
  return classes[sentiment] || 'bg-gray-100 text-gray-700';
};

const formatDate = (dateString) => {
  if (!dateString) return 'Today';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', { 
    day: '2-digit', 
    month: 'short', 
    year: 'numeric' 
  });
};

onMounted(() => {
  fetchFeedback();
});
</script>