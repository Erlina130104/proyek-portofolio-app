import api from './api';

export default {
  sendMessage(message) {
    return api.post('/chatbot/chat', { message });
  },
};