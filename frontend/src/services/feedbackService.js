// services/feedbackService.js
import axios from 'axios';

const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://localhost:8000/api';

const feedbackService = {
  /**
   * Get all feedback
   */
  getAll: async () => {
    try {
      const response = await axios.get(`${API_BASE_URL}/feedback`);
      return response;
    } catch (error) {
      console.error('Error fetching feedback:', error);
      throw error;
    }
  },

  /**
   * Get feedback by ID
   */
  getById: async (id) => {
    try {
      const response = await axios.get(`${API_BASE_URL}/feedback/${id}`);
      return response;
    } catch (error) {
      console.error('Error fetching feedback:', error);
      throw error;
    }
  },

  /**
   * Create new feedback
   */
  create: async (data) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/feedback`, data);
      return response;
    } catch (error) {
      console.error('Error creating feedback:', error);
      throw error;
    }
  },

  /**
   * Update feedback
   */
  update: async (id, data) => {
    try {
      const response = await axios.put(`${API_BASE_URL}/feedback/${id}`, data);
      return response;
    } catch (error) {
      console.error('Error updating feedback:', error);
      throw error;
    }
  },

  /**
   * Delete feedback
   */
  delete: async (id) => {
    try {
      const response = await axios.delete(`${API_BASE_URL}/feedback/${id}`);
      return response;
    } catch (error) {
      console.error('Error deleting feedback:', error);
      throw error;
    }
  },

  /**
   * Get feedback statistics
   */
  getStats: async () => {
    try {
      const response = await axios.get(`${API_BASE_URL}/feedback/stats`);
      return response;
    } catch (error) {
      console.error('Error fetching stats:', error);
      throw error;
    }
  }
};

export default feedbackService;