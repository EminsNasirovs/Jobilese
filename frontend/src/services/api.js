import axios from "axios";

const apiBase = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const api = axios.create({
  baseURL: `${apiBase.replace(/\/$/, "")}/api`,
});

export const storageUrl = (path) => {
  if (!path) return "";
  const base = import.meta.env.VITE_STORAGE_URL || `${apiBase}/storage`;
  return `${base.replace(/\/$/, "")}/${path}`;
};

// Add token automatically if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

export default api;
