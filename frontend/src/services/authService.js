import api from './api';

export default {
  login(credentials) {
    return api.post('/login', credentials);
  },

  register(userData) {
    return api.post('/register', userData);
  },

  logout() {
    return api.post('/logout');
  },

  getUser() {
    return api.get('/user');
  },
};