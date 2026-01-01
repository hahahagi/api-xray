import axios from "axios";

// Determine base URL based on environment
// In development: use Vite proxy (/api-proxy)
// In production: use direct file path (./api.php) since api.php is in the same folder
const baseURL = import.meta.env.DEV ? "/api-proxy" : "./api.php";

const apiClient = axios.create({
  baseURL: baseURL,
  headers: {
    "Content-Type": "application/json",
  },
});

// Request Interceptor
apiClient.interceptors.request.use(
  (config) => {
    // You can add auth tokens here if needed
    // const token = localStorage.getItem('token');
    // if (token) {
    //   config.headers.Authorization = `Bearer ${token}`;
    // }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response Interceptor
apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // Handle global errors (e.g., 401 Unauthorized)
    if (error.response && error.response.status === 401) {
      // Redirect to login or clear state
      console.warn("Unauthorized access, redirecting to login...");
    }
    return Promise.reject(error);
  }
);

export default apiClient;
