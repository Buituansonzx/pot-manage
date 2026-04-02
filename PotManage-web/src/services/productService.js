import { apiClient } from './apiClient';

export const productService = {
  getProducts: async (params = {}) => {
    const { page = 1, limit = 20, search, category_id, min_price, max_price, sort_by } = params;
    
    const queryParams = new URLSearchParams();
    queryParams.append('page', page);
    queryParams.append('limit', limit);
    
    // Tìm kiếm theo tên (Apiato có thể dùng search=name:từ_khóa)
    if (search) {
      queryParams.append('search', `${search}`);
    }
    
    if (category_id) queryParams.append('category_id', category_id);
    if (min_price) queryParams.append('min_price', min_price);
    if (max_price) queryParams.append('max_price', max_price);
    
    // Truyền trực tiếp sort_by lên backend
    if (sort_by) {
      queryParams.append('sort_by', sort_by);
    }

    const response = await apiClient(`/products?${queryParams.toString()}`);
    return response;
  }
};
