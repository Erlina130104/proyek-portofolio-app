<template>
  <div id="app">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="logo">SmartERP</div>
      <ul class="nav-links">
        <li><router-link to="/">Dashboard</router-link></li>
        <li><router-link to="/products">Products</router-link></li>
        <li><router-link to="/transactions">Transactions</router-link></li>
        <li><router-link to="/employees">Employees</router-link></li>
        <li><router-link to="/attendance">Attendance</router-link></li>
        <li><router-link to="/feedback">Feedback</router-link></li>
        <li><router-link to="/tickets">Tickets</router-link></li>
      </ul>
    </nav>

    <!-- Konten -->
    <main class="content">
      <router-view />
    </main>

    <!-- AI Chat Assistant (Tambahan) -->
    <AIChat />
  </div>
</template>

<script setup>
import AIChat from '@/components/AIChat.vue'
</script>

<style scoped>
/* Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

#app {
  background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
  min-height: 100vh;
  color: #333;
}

/* Navbar */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #1e3c72;
  background: linear-gradient(to right, #2a5298, #1e3c72);
  padding: 15px 40px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.logo {
  font-size: 20px;
  font-weight: bold;
  color: #fff;
  letter-spacing: 1px;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 20px;
}

.nav-links a {
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  padding: 6px 12px;
  border-radius: 6px;
}

.nav-links a:hover,
.router-link-active {
  background: rgba(255, 255, 255, 0.2);
}

/* Konten */
.content {
  padding: 40px;
  animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}
</style>