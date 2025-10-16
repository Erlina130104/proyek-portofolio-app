import axios from 'axios';

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add response interceptor for error handling
apiClient.interceptors.response.use(
  response => response,
  error => {
    console.error('API Error:', error.response || error);
    return Promise.reject(error);
  }
);

const attendanceService = {
  getAll(params = {}) {
    return apiClient.get('/attendances', { params });
  },

  getById(id) {
    return apiClient.get(`/attendances/${id}`);
  },

  create(data) {
    return apiClient.post('/attendances', data);
  },

  record(data) {
    return apiClient.post('/attendances/record', data);
  },

  getStatistics(params = {}) {
    return apiClient.get('/attendances/statistics', { params });
  },

  getTodaySummary() {
    return apiClient.get('/attendances/today-summary');
  },

  update(id, data) {
    return apiClient.put(`/attendances/${id}`, data);
  },

  delete(id) {
    return apiClient.delete(`/attendances/${id}`);
  }
};

export default attendanceService;