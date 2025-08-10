import axios from 'axios';

export const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  timeout: 10000,
});

http.interceptors.response.use(
  (r) => r,
  (e) => Promise.reject(e)
);
