// services/ticketService.js
import axios from 'axios';

const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://localhost:8000/api';

const ticketService = {
  /**
   * Get all tickets with optional filters
   */
  getAll: async (params = {}) => {
    try {
      const response = await axios.get(`${API_BASE_URL}/tickets`, { params });
      return response;
    } catch (error) {
      console.error('Error fetching tickets:', error);
      throw error;
    }
  },

  /**
   * Get ticket by ID
   */
  getById: async (id) => {
    try {
      const response = await axios.get(`${API_BASE_URL}/tickets/${id}`);
      return response;
    } catch (error) {
      console.error('Error fetching ticket:', error);
      throw error;
    }
  },

  /**
   * Create new ticket
   */
  create: async (data) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/tickets`, data);
      return response;
    } catch (error) {
      console.error('Error creating ticket:', error);
      throw error;
    }
  },

  /**
   * Update ticket
   */
  update: async (id, data) => {
    try {
      const response = await axios.put(`${API_BASE_URL}/tickets/${id}`, data);
      return response;
    } catch (error) {
      console.error('Error updating ticket:', error);
      throw error;
    }
  },

  /**
   * Delete ticket
   */
  delete: async (id) => {
    try {
      const response = await axios.delete(`${API_BASE_URL}/tickets/${id}`);
      return response;
    } catch (error) {
      console.error('Error deleting ticket:', error);
      throw error;
    }
  },

  /**
   * Update ticket status
   */
  updateStatus: async (id, status) => {
    try {
      const response = await axios.patch(`${API_BASE_URL}/tickets/${id}/status`, { status });
      return response;
    } catch (error) {
      console.error('Error updating ticket status:', error);
      throw error;
    }
  },

  /**
   * Update ticket priority
   */
  updatePriority: async (id, priority) => {
    try {
      const response = await axios.patch(`${API_BASE_URL}/tickets/${id}/priority`, { priority });
      return response;
    } catch (error) {
      console.error('Error updating ticket priority:', error);
      throw error;
    }
  },

  /**
   * Add comment to ticket
   */
  addComment: async (ticketId, comment) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/tickets/${ticketId}/comments`, { comment });
      return response;
    } catch (error) {
      console.error('Error adding comment:', error);
      throw error;
    }
  },

  /**
   * Get ticket statistics
   */
  getStats: async () => {
    try {
      const response = await axios.get(`${API_BASE_URL}/tickets/stats`);
      return response;
    } catch (error) {
      console.error('Error fetching stats:', error);
      throw error;
    }
  },

  /**
   * Search tickets
   */
  search: async (query) => {
    try {
      const response = await axios.get(`${API_BASE_URL}/tickets/search`, {
        params: { q: query }
      });
      return response;
    } catch (error) {
      console.error('Error searching tickets:', error);
      throw error;
    }
  },

  /**
   * Export tickets to CSV
   */
  exportCSV: async () => {
    try {
      const response = await axios.get(`${API_BASE_URL}/tickets/export/csv`, {
        responseType: 'blob'
      });
      return response;
    } catch (error) {
      console.error('Error exporting tickets:', error);
      throw error;
    }
  }
};

export default ticketService;