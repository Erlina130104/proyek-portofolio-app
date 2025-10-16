// services/dashboardService.js

const dashboardService = {
  /**
   * Get dashboard overview
   */
  getOverview: async () => {
    try {
      // Simulate API call delay
      await new Promise(resolve => setTimeout(resolve, 500));

      // Load all data dari localStorage
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
      const todayFeedbacks = feedbacks.filter(f => {
        const date = new Date(f.created_at);
        const today = new Date();
        return date.toDateString() === today.toDateString();
      });

      const pendingTickets = tickets.filter(t => t.status === 'Pending' || t.status === 'open');
      const presentAttendance = attendance.filter(a => a.status === 'present');
      const totalAttendance = attendance.length || employees.length || 1;
      const attendanceRate = totalAttendance > 0 ? Math.round((presentAttendance.length / totalAttendance) * 100) : 0;

      const dashboardData = {
        current_date: new Date().toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'long',
          year: 'numeric',
          weekday: 'long'
        }),
        quick_stats: {
          products: {
            count: products.length,
            growth: products.length > 0 ? '+12%' : '0%'
          },
          transactions: {
            count: tickets.length,
            growth: '+8%'
          },
          employees: {
            count: employees.length,
            growth: employees.length > 0 ? '+2%' : '0%'
          },
          attendance: {
            present: presentAttendance.length,
            total: totalAttendance,
            rate: attendanceRate + '%'
          },
          feedback: {
            count: feedbacks.length,
            new: todayFeedbacks.length
          },
          pending_tickets: {
            count: pendingTickets.length,
            status: pendingTickets.length > 0 ? 'Urgent' : 'Normal'
          }
        },
        business_overview: {
          products_count: products.length,
          transactions_count: tickets.length,
          growth_rate: 15.5,
          growth_message: 'Pertumbuhan bisnis sangat baik bulan ini!'
        },
        people_overview: {
          employees_count: employees.length,
          attendance_rate: attendanceRate,
          attendance_message: 'Tingkat kehadiran karyawan sangat baik'
        },
        support_feedback: {
          pending_tickets: pendingTickets.length,
          recent_feedbacks: feedbacks.slice(0, 5).map(f => ({
            customer_name: f.customer_name || 'Customer',
            comment: f.message || 'Feedback tidak tersedia'
          })),
          ai_insight: generateAIInsight(
            products.length,
            tickets.length,
            employees.length,
            attendanceRate,
            pendingTickets.length
          )
        }
      };

      return {
        data: {
          success: true,
          data: dashboardData
        }
      };
    } catch (error) {
      console.error('Error fetching dashboard overview:', error);
      throw error;
    }
  },

  /**
   * Get business metrics
   */
  getBusinessMetrics: async () => {
    try {
      const ticketsData = localStorage.getItem('ticketsData');
      const tickets = ticketsData ? JSON.parse(ticketsData) : [];

      const metrics = {
        total_transactions: tickets.length,
        this_month: tickets.filter(t => {
          const date = new Date(t.created_at);
          const now = new Date();
          return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
        }).length,
        growth_rate: 15.5,
        status: 'Growing'
      };

      return {
        data: {
          success: true,
          data: metrics
        }
      };
    } catch (error) {
      console.error('Error fetching business metrics:', error);
      throw error;
    }
  },

  /**
   * Get people metrics
   */
  getPeopleMetrics: async () => {
    try {
      const employeesData = localStorage.getItem('employeesData');
      const attendanceData = localStorage.getItem('attendanceData');

      const employees = employeesData ? JSON.parse(employeesData) : [];
      const attendance = attendanceData ? JSON.parse(attendanceData) : [];

      const present = attendance.filter(a => a.status === 'present').length;
      const total = attendance.length || employees.length || 1;
      const rate = total > 0 ? Math.round((present / total) * 100) : 0;

      const metrics = {
        total_employees: employees.length,
        present_today: present,
        total_expected: total,
        attendance_rate: rate,
        active_count: employees.filter(e => e.status === 'active').length
      };

      return {
        data: {
          success: true,
          data: metrics
        }
      };
    } catch (error) {
      console.error('Error fetching people metrics:', error);
      throw error;
    }
  },

  /**
   * Get support & feedback metrics
   */
  getSupportMetrics: async () => {
    try {
      const ticketsData = localStorage.getItem('ticketsData');
      const feedbackData = localStorage.getItem('feedbackData');

      const tickets = ticketsData ? JSON.parse(ticketsData) : [];
      const feedbacks = feedbackData ? JSON.parse(feedbackData) : [];

      const pending = tickets.filter(t => t.status === 'Pending' || t.status === 'open').length;
      const resolved = tickets.filter(t => t.status === 'Resolved' || t.status === 'closed').length;

      const metrics = {
        pending_tickets: pending,
        resolved_tickets: resolved,
        total_feedbacks: feedbacks.length,
        positive_feedbacks: feedbacks.filter(f => f.sentiment === 'positive').length,
        response_rate: total > 0 ? Math.round(((resolved) / (pending + resolved + 1)) * 100) : 0
      };

      return {
        data: {
          success: true,
          data: metrics
        }
      };
    } catch (error) {
      console.error('Error fetching support metrics:', error);
      throw error;
    }
  }
};

/**
 * Generate AI Insights berdasarkan data
 */
function generateAIInsight(productsCount, transactionsCount, employeesCount, attendanceRate, pendingTickets) {
  const insights = [
    `Performa penjualan meningkat dengan ${transactionsCount} transaksi bulan ini`,
    `Tim Anda memiliki tingkat kehadiran yang baik di ${attendanceRate}%`,
    `Ada ${pendingTickets} ticket yang perlu perhatian segera`,
    `Total ${employeesCount} karyawan aktif dalam sistem`,
    `Rekomendasi: Fokus pada ${pendingTickets > 0 ? 'resolusi ticket' : 'peningkatan layanan'}`,
    `Katalog produk mencakup ${productsCount} item tersedia untuk dijual`
  ];

  return insights[Math.floor(Math.random() * insights.length)];
}

export default dashboardService;