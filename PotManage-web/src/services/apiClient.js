const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080/v1';

/**
 * Hàm gọi API chung cho toàn dự án
 * Tự động thêm Bearer token nếu có
 */
export const apiClient = async (endpoint, options = {}) => {
  const token = localStorage.getItem('access_token');
  
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    ...options.headers,
  };

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    headers,
  });

  const data = await response.json();

  if (!response.ok) {
    const error = new Error(data.message || 'Có lỗi xảy ra khi gọi API');
    error.status = response.status;
    error.data = data;
    throw error;
  }

  return data;
};
