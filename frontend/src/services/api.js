import axios from 'axios';

const API = axios.create({
  baseURL: 'http://localhost:8080/api', // Symfony backend URL
  withCredentials: true, // Include credentials (cookies) in requests
});

export const loginUser = (credentials) => API.post('/auth', credentials);
export const registerUser = (userData) => API.post('/register', userData);

export default API;