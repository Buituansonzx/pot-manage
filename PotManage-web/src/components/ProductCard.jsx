import React, { useState, useRef } from 'react';
import './ProductCard.css';
import { orderService } from '../services/orderService';
import { showToast } from './Toast';

const formatPrice = (price) => {
  if (price === undefined || price === null) return 'Liên hệ';
  return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';
};

const ProductCard = ({ product, user }) => {
  const [isHovered, setIsHovered] = useState(false);
  const [orders, setOrders] = useState([]);
  const [isLoadingOrders, setIsLoadingOrders] = useState(false);
  const ordersLoaded = useRef(false);

  // States for creating new order
  const [isCreatingNew, setIsCreatingNew] = useState(false);
  const [newCustomerName, setNewCustomerName] = useState('');
  const [newCustomerPhone, setNewCustomerPhone] = useState('');

  // Lắng nghe thay đổi chung để xoá cache giỏ hàng cho toàn bộ sản phẩm
  React.useEffect(() => {
    const invalidateCartCache = () => {
      ordersLoaded.current = false;
    };
    window.addEventListener('cart-updated', invalidateCartCache);
    return () => window.removeEventListener('cart-updated', invalidateCartCache);
  }, []);

  const getProductImage = () => {
    if (product.productImages && product.productImages.length > 0) {
      return product.productImages[0].url || product.productImages[0].image_url;
    }
    if (product.product_images && product.product_images.length > 0) {
      return product.product_images[0].url || product.product_images[0].image_url;
    }
    if (product.images && product.images.length > 0) {
      return product.images[0].url || product.images[0].image_url;
    }
    return null;
  };

  const imageUrl = getProductImage();

  // Kiểm tra phân quyền (Roles)
  const roles = user?.roles || [];
  const isAdmin = roles.includes('admin') || roles.includes('quản_trị_viên');
  const isCollaborator = isAdmin || roles.includes('cong_tac_vien') || roles.includes('collaborator');
  const isSales = isAdmin || roles.includes('nhan_vien_ban_hang') || roles.includes('sales_staff') || roles.includes('sales');

  // Phân tích giá cả từ mảng price hoặc giá trị price
  let suggestedPrice = 0;
  let minRetailPrice = null;
  let floorPrice = null;

  if (Array.isArray(product.price) && product.price.length > 0) {
    const p = product.price[0];
    suggestedPrice = p.suggested_retail_price || p.price;
    minRetailPrice = p.min_retail_price;
    floorPrice = p.floor_price;
  } else if (typeof product.price === 'number') {
    suggestedPrice = product.price; // Dữ liệu cũ (nếu có)
  }

  const handleMouseEnter = async () => {
    setIsHovered(true);
    if (ordersLoaded.current) return;
    
    try {
      setIsLoadingOrders(true);
      const res = await orderService.getOrders();
      setOrders(res?.data || []);
      ordersLoaded.current = true;
    } catch (e) {
      console.error(e);
    } finally {
      setIsLoadingOrders(false);
    }
  };

  const handleMouseLeave = () => {
    setIsHovered(false);
    // Reset state when hover leaves
    setIsCreatingNew(false);
    setNewCustomerName('');
    setNewCustomerPhone('');
  };

  const handleAddToCart = async (e, orderId) => {
    e.preventDefault();
    e.stopPropagation();
    try {
      await orderService.addProductToOrder(orderId, {
        product_id: product.id,
        quantity: 1,
        price: suggestedPrice,
        note: ""
      });
      showToast('Đã thêm sản phẩm vào giỏ hàng!');
      window.dispatchEvent(new Event('cart-updated'));
    } catch (error) {
      showToast('Lỗi: ' + error.message);
    }
  };

  const handleCreateOrder = async (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (!newCustomerName && !newCustomerPhone) {
      showToast("Vui lòng nhập tên hoặc SĐT khách hàng");
      return;
    }
    
    try {
      setIsLoadingOrders(true);
      await orderService.createOrder({
        customer_name: newCustomerName,
        customer_phone: newCustomerPhone,
        product_id: product.id,
        quantity: 1,
        price: suggestedPrice,
        note: ""
      });
      showToast('Đã tạo đơn và thêm sản phẩm thành công!');
      window.dispatchEvent(new Event('cart-updated'));
      setIsCreatingNew(false);
      setNewCustomerName('');
      setNewCustomerPhone('');
      setIsHovered(false);
      
      // Request list reset next time hovered
      ordersLoaded.current = false; 
    } catch (error) {
      showToast('Lỗi: ' + error.message);
    } finally {
      setIsLoadingOrders(false);
    }
  };

  const handleCardClick = (e) => {
    // Only navigate if we aren't clicking inside the popover
    if (e.target.closest('.cart-popup') || e.target.closest('.btn-details')) {
      return;
    }
    window.location.href = `/products/${product.id}`;
  };

  return (
    <div className="product-card" onClick={handleCardClick} onMouseEnter={handleMouseEnter} onMouseLeave={handleMouseLeave}>
      <div className="product-image">
        {imageUrl ? (
          <img src={imageUrl} alt={product.name} />
        ) : (
          <span className="no-image">No Image</span>
        )}
        
        {/* Nút xem chi tiết */}
        <a href={`/products/${product.id}`} className="btn-details" onClick={(e) => e.stopPropagation()}>
          Xem chi tiết
        </a>

        {/* Popup giỏ hàng hiển thị khi hover */}
        {isHovered && (
          <div className="cart-popup-overlay">
            <div className="cart-popup" onClick={(e) => e.stopPropagation()}>
              <div className="cart-popup-header">
                {isCreatingNew ? 'Tạo giỏ hàng mới' : 'Chọn giỏ hàng'}
              </div>
              
              {!isCreatingNew ? (
                <>
                  <div className="cart-popup-body">
                    {isLoadingOrders ? (
                      <div className="cart-item loading">Đang tải...</div>
                    ) : orders.length > 0 ? (
                      orders.map(order => (
                        <div 
                          key={order.id} 
                          className="cart-item" 
                          onClick={(e) => handleAddToCart(e, order.id)}
                        >
                          {order.code || `Giỏ hàng #${order.id}`} {order.customer_name ? `- ${order.customer_name}` : ''}
                        </div>
                      ))
                    ) : (
                      <div className="cart-item empty">Chưa có giỏ hàng</div>
                    )}
                  </div>
                  <div 
                    className="cart-popup-footer" 
                    onClick={(e) => { e.stopPropagation(); setIsCreatingNew(true); }}
                  >
                    + Tạo mới
                  </div>
                </>
              ) : (
                <>
                  <div className="cart-popup-body create-form">
                    <input 
                      type="text" 
                      placeholder="Tên khách hàng" 
                      value={newCustomerName}
                      onChange={(e) => setNewCustomerName(e.target.value)}
                      className="cart-input"
                      onClick={(e) => e.stopPropagation()}
                    />
                    <input 
                      type="text" 
                      placeholder="Số điện thoại" 
                      value={newCustomerPhone}
                      onChange={(e) => setNewCustomerPhone(e.target.value)}
                      className="cart-input"
                      onClick={(e) => e.stopPropagation()}
                    />
                  </div>
                  <div className="cart-popup-footer flex-footer">
                    <button onClick={handleCreateOrder} className="cart-btn-confirm" disabled={isLoadingOrders}>
                      {isLoadingOrders ? 'Đang tạo...' : 'Xác nhận'}
                    </button>
                    <button onClick={(e) => { e.stopPropagation(); setIsCreatingNew(false); }} className="cart-btn-cancel">
                      Hủy
                    </button>
                  </div>
                </>
              )}
            </div>
          </div>
        )}
      </div>
      <div className="product-info">
        <h3 className="product-name">{product.name}</h3>
        <div className="product-meta">
          <span className="product-tag">{product.category_name || 'GỐM THỦ CÔNG'}</span>
          
          <div className="product-prices">
            <span className="price-main">{formatPrice(suggestedPrice)}</span>
            
            {/* Hiển thị min_retail_price nếu là Nhân viên bán hàng */}
            {isSales && minRetailPrice !== null && minRetailPrice !== undefined && (
              <span className="price-secondary">
                <span className="price-dot dot-sales"></span>
                NVB: <strong>{formatPrice(minRetailPrice)}</strong>
              </span>
            )}
            
            {/* Hiển thị floor_price nếu là Cộng tác viên */}
            {isCollaborator && floorPrice !== null && floorPrice !== undefined && (
              <span className="price-secondary">
                <span className="price-dot dot-collab"></span>
                CTV: <strong>{formatPrice(floorPrice)}</strong>
              </span>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductCard;
