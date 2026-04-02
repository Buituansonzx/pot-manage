import { apiClient } from './apiClient';

export const categoryService = {
  getCategories: async () => {
    // Không giới hạn số page để lấy tất cả categories cho menu
    const response = await apiClient('/categories?limit=0');
    return response;
  }
};
