import api from './api';

export default {
  getAll(params = {}) {
    return api.get('/transactions', { params });
  },

  getById(id) {
    return api.get(`/transactions/${id}`);
  },

  create(data) {
    return api.post('/transactions', data);
  },

  updateStatus(id, status) {
    return api.patch(`/transactions/${id}/status`, { status });
  },

  getInvoice(id) {
    return api.get(`/transactions/${id}/invoice`);
  },

  getStatistics(params = {}) {
    return api.get('/transactions/statistics/summary', { params });
  },
};