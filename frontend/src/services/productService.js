import api from './api';

export default {
  // Get all products
  getAll(params = {}) {
    return api.get('/products', { params });
  },

  // Get single product
  getById(id) {
    return api.get(`/products/${id}`);
  },

  // Create product
  create(data) {
    return api.post('/products', data);
  },

  // Update product
  update(id, data) {
    return api.put(`/products/${id}`, data);
  },

  // Delete product
  delete(id) {
    return api.delete(`/products/${id}`);
  },

  // Update stock
  updateStock(id, data) {
    return api.patch(`/products/${id}/stock`, data);
  },

  // Get categories
  getCategories() {
    return api.get('/products/categories/list');
  },
};