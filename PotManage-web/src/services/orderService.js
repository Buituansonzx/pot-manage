import { apiClient } from './apiClient';

export const orderService = {
  getOrders: async (params = {}) => {
    const queryParams = new URLSearchParams();
    if (params.status) queryParams.append('status', params.status);
    if (params.customer_name) queryParams.append('customer_name', params.customer_name);
    if (params.customer_phone) queryParams.append('customer_phone', params.customer_phone);
    
    const response = await apiClient(`/orders?${queryParams.toString()}`);
    return response;
  },
  addProductToOrder: async (orderId, payload) => {
    return await apiClient(`/orders/${orderId}/products`, {
      method: 'POST',
      body: JSON.stringify(payload),
    });
  },
  createOrder: async (payload) => {
    return await apiClient('/orders', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
  }
};
