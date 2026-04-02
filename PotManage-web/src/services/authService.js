import { apiClient } from './apiClient';

export const authService = {
  /**
   * Đăng nhập với số điện thoại hoặc email (dành riêng cho PotManage-api)
   * @param {Object} credentials - Thông tin đăng nhập
   * @param {string} credentials.email - Email đăng nhập
   * @param {string} credentials.password - Mật khẩu
   * @returns {Promise<Object>} Dữ liệu token (access_token, token_type, expires_in, refresh_token)
   */
  login: async (credentials) => {
    // Tùy vào Endpoint API thực tế của ứng dụng Apiato
    // Thường Apiato cho app client sẽ là /login
    const response = await apiClient('/login', {
      method: 'POST',
      body: JSON.stringify(credentials),
    });

    // Lưu token vào localStorage sau khi đăng nhập thành công
    if (response && response.data && response.data.access_token) {
      localStorage.setItem('access_token', response.data.access_token);
      if (response.data.refresh_token) {
        localStorage.setItem('refresh_token', response.data.refresh_token);
      }
      if (response.user) {
        localStorage.setItem('user_info', JSON.stringify(response.user));
      }
    }
    
    return response;
  },

  /**
   * Đăng xuất
   */
  logout: async () => {
    try {
      await apiClient('/logout', { method: 'DELETE' });
    } catch (error) {
      console.error('Lỗi khi đăng xuất:', error);
    } finally {
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
      localStorage.removeItem('user_info');
    }
  },

  /**
   * Lấy thông tin người dùng đang đăng nhập
   */
  getProfile: async () => {
    return await apiClient('/user/profile', { method: 'GET' });
  }
};
