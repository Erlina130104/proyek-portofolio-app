<template>
  <div class="fixed bottom-6 right-6 z-40">
    <!-- Chat Button (Minimized) -->
    <button 
      v-if="!chatOpen"
      @click="openChat"
      class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
      title="Open AI Chat"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
    </button>

    <!-- Chat Window (Expanded) -->
    <div v-else class="bg-white rounded-2xl shadow-2xl w-96 max-h-[600px] flex flex-col border border-slate-200">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 rounded-t-2xl flex items-center justify-between">
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
          </svg>
          <div>
            <h3 class="font-bold">AI Assistant</h3>
            <p class="text-xs opacity-90">SmartERP Helper</p>
          </div>
        </div>
        <button @click="closeChat" class="hover:bg-white/20 p-1 rounded transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Messages Container -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50">
        <div v-if="messages.length === 0" class="text-center py-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-300 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          <p class="text-slate-500 text-sm">Halo! Ada yang bisa saya bantu?</p>
        </div>

        <div v-for="(msg, idx) in messages" :key="idx" :class="msg.role === 'user' ? 'text-right' : 'text-left'">
          <div 
            :class="msg.role === 'user' 
              ? 'bg-indigo-600 text-white' 
              : 'bg-white text-slate-800 border border-slate-200'"
            class="inline-block max-w-xs px-4 py-2 rounded-lg"
          >
            <p class="text-sm">{{ msg.content }}</p>
          </div>
          <p class="text-xs text-slate-400 mt-1">{{ formatTime(msg.timestamp) }}</p>
        </div>

        <div v-if="isLoading" class="text-left">
          <div class="bg-white text-slate-800 border border-slate-200 inline-block px-4 py-2 rounded-lg">
            <div class="flex gap-1">
              <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></div>
              <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
              <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="border-t border-slate-200 p-4 bg-white rounded-b-2xl">
        <form @submit.prevent="sendMessage" class="flex gap-2">
          <input 
            v-model="userInput"
            type="text"
            placeholder="Tanyakan sesuatu..."
            class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-indigo-500 text-sm"
            :disabled="isLoading"
          />
          <button 
            type="submit"
            :disabled="isLoading || !userInput.trim()"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors disabled:bg-slate-300 disabled:cursor-not-allowed"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const chatOpen = ref(false);
const messages = ref([]);
const userInput = ref('');
const isLoading = ref(false);

const openChat = () => {
  chatOpen.value = true;
};

const closeChat = () => {
  chatOpen.value = false;
};

const formatTime = (timestamp) => {
  if (!timestamp) return '';
  const date = new Date(timestamp);
  return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

const sendMessage = async () => {
  if (!userInput.value.trim()) return;

  // Add user message
  const userMsg = userInput.value.trim();
  messages.value.push({
    role: 'user',
    content: userMsg,
    timestamp: new Date()
  });

  userInput.value = '';
  isLoading.value = true;

  // Simulate API delay
  await new Promise(resolve => setTimeout(resolve, 800));

  // Generate AI response
  const aiResponse = generateAIResponse(userMsg);
  messages.value.push({
    role: 'assistant',
    content: aiResponse,
    timestamp: new Date()
  });

  isLoading.value = false;
};

const generateAIResponse = (question) => {
  const lowerQuestion = question.toLowerCase();

  // Knowledge base untuk AI responses
  const responses = {
    // Dashboard questions
    'dashboard': 'Dashboard menampilkan overview semua data bisnis Anda termasuk produk, transaksi, karyawan, kehadiran, feedback pelanggan, dan tickets.',
    'berapa': handleDataQuestions(lowerQuestion),
    
    // Products questions
    'produk': 'Produk adalah item yang dijual dalam sistem. Anda bisa menambah, mengedit, atau menghapus produk di halaman Products.',
    'tambah produk': 'Klik tombol "Add Product" di halaman Products, isi informasi produk, kemudian klik Save.',
    
    // Tickets questions
    'ticket': 'Tickets adalah laporan masalah dari pelanggan. Anda dapat melacak, mengelola, dan merespon tickets di halaman Tickets.',
    'buat ticket': 'Klik "New Ticket" di halaman Tickets, isi detail ticket termasuk judul, deskripsi, status, dan prioritas.',
    'pending': 'Pending tickets adalah tickets yang belum diselesaikan. Lihat halaman Tickets untuk melihat semua pending tickets.',
    
    // Employees questions
    'karyawan': 'Karyawan adalah anggota tim Anda. Kelola data karyawan termasuk nama, posisi, departemen, dan status di halaman Employees.',
    'tambah karyawan': 'Klik "Add Employee" di halaman Employees, isi informasi lengkap, kemudian klik Add Employee.',
    'departemen': 'Departemen adalah pengelompokan karyawan. Anda bisa membuat departemen baru saat menambah atau mengedit karyawan.',
    
    // Attendance questions
    'kehadiran': 'Kehadiran melacak absensi karyawan. Lihat halaman Attendance untuk melihat data kehadiran harian.',
    'absen': 'Untuk mencatat absensi, buka halaman Attendance dan klik tombol untuk menandai kehadiran atau ketidakhadiran.',
    
    // Feedback questions
    'feedback': 'Feedback adalah masukan dari pelanggan. Lihat halaman Feedback untuk melihat semua feedback yang diterima.',
    'tambah feedback': 'Klik "Add Feedback" di halaman Feedback, isi data pelanggan, rating, sentiment, dan pesan.',
    
    // General questions
    'help': 'Saya adalah AI Assistant untuk SmartERP. Saya bisa membantu Anda dengan pertanyaan tentang:\n- Dashboard\n- Produk\n- Transactions\n- Karyawan\n- Kehadiran\n- Feedback\n- Tickets',
    'halo': 'Halo! Saya adalah AI Assistant SmartERP. Ada yang bisa saya bantu?',
    'apa itu': 'Saya adalah AI Assistant yang membantu menjawab pertanyaan tentang SmartERP. Tanyakan sesuatu tentang sistem ini!',
    'berapa jumlah': handleDataQuestions(lowerQuestion),
  };

  // Find matching response
  for (const [key, response] of Object.entries(responses)) {
    if (lowerQuestion.includes(key)) {
      return typeof response === 'function' ? response(lowerQuestion) : response;
    }
  }

  // Default response
  return 'Maaf, saya belum memahami pertanyaan Anda. Coba tanyakan tentang: Dashboard, Produk, Transactions, Karyawan, Kehadiran, Feedback, atau Tickets. Ketik "help" untuk bantuan.';
};

const handleDataQuestions = (question) => {
  try {
    const productsData = localStorage.getItem('productsData');
    const ticketsData = localStorage.getItem('ticketsData');
    const employeesData = localStorage.getItem('employeesData');
    const feedbackData = localStorage.getItem('feedbackData');

    const products = productsData ? JSON.parse(productsData) : [];
    const tickets = ticketsData ? JSON.parse(ticketsData) : [];
    const employees = employeesData ? JSON.parse(employeesData) : [];
    const feedbacks = feedbackData ? JSON.parse(feedbackData) : [];

    if (question.includes('produk') || question.includes('product')) {
      return `Saat ini ada ${products.length} produk di sistem.`;
    }
    if (question.includes('ticket')) {
      const pending = tickets.filter(t => t.status === 'Pending' || t.status === 'open').length;
      return `Total ada ${tickets.length} tickets, dengan ${pending} tickets yang pending.`;
    }
    if (question.includes('karyawan') || question.includes('employee')) {
      const active = employees.filter(e => e.status === 'active').length;
      return `Total ada ${employees.length} karyawan, ${active} di antaranya aktif.`;
    }
    if (question.includes('feedback')) {
      const positive = feedbacks.filter(f => f.sentiment === 'positive').length;
      return `Total ada ${feedbacks.length} feedback, ${positive} di antaranya positif.`;
    }

    return 'Data tidak tersedia';
  } catch (error) {
    return 'Terjadi kesalahan saat mengambil data.';
  }
};
</script>