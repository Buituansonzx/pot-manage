import React, { useState, useEffect } from 'react';
import { ShoppingCart } from 'lucide-react';
import { orderService } from '../services/orderService';
import './CartsPage.css';

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
};

const getProductImage = (product) => {
  if (!product) return null;
  if (product.productImages && product.productImages.length > 0) return product.productImages[0].url || product.productImages[0].image_url;
  if (product.product_images && product.product_images.length > 0) return product.product_images[0].url || product.product_images[0].image_url;
  if (product.images && product.images.length > 0) return product.images[0].url || product.images[0].image_url;
  return null;
};

const CartsPage = ({ user }) => {
  const [orders, setOrders] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const resp = await orderService.getOrders({ status: 'pending' });
        setOrders(resp.data || []);
      } catch (error) {
        console.error('Failed to fetch carts', error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchOrders();
  }, [user]);

  const calculateTotal = (items) => {
    if (!items || items.length === 0) return 0;
    return items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
  };

  return (
    <div className="carts-page">
      <div className="carts-header">
        <h1 className="carts-title">Active Carts</h1>
        <p className="carts-subtitle">
          Manage multiple procurement sessions simultaneously. Each atelier project maintains its own curated selection and billing.
        </p>
      </div>

      {isLoading ? (
        <div style={{ padding: '40px', textAlign: 'center', color: '#888' }}>Loading active carts...</div>
      ) : orders.length === 0 ? (
        <div style={{ padding: '40px', textAlign: 'center', color: '#888' }}>You have no active carts.</div>
      ) : (
        orders.map((order, index) => {
          const total = calculateTotal(order.items);
          const badgeText = index === 0 ? 'DRAFT SESSION' : 'PRIORITY HANDLING';

          return (
            <div key={order.id} className="cart-card">
              <div className="cart-card-header">
                <div>
                  <h2 className="cart-name">Giỏ hàng của <span className="highlight-customer">{order.customer_name || 'Khách vãng lai'}</span></h2>
                  <p className="cart-meta">
                    MÃ ĐƠN: #{order.id} — SĐT: {order.customer_phone || 'N/A'}
                  </p>
                </div>
                <div className="cart-session-badge">{badgeText}</div>
              </div>
              
              <div className="cart-items">
                {(!order.items || order.items.length === 0) ? (
                  <p style={{ padding: '20px 0', color: '#888', fontStyle: 'italic' }}>Giỏ hàng này đang trống.</p>
                ) : (
                  order.items.map((item, i) => {
                    const product = item || {};
                    
                    const imageUrl = getProductImage(product);
                    const itemTotal = item.price * item.quantity;
                    
                    return (
                      <div key={i} className="cart-item-row">
                        <div className="item-image">
                          {imageUrl ? (
                            <img src={imageUrl} alt={product.name} style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '4px' }} />
                          ) : (
                            <div style={{ width: '100%', height: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#ccc', fontSize: '0.8rem' }}>No Image</div>
                          )}
                        </div>
                        <div className="item-details">
                          <h3 className="item-name">{product.product_name || 'Sản phẩm không rõ'}</h3>
                          <p className="item-category">{product.category_name || 'Gốm thủ công'}</p>
                        </div>
                        <div className="item-column">
                          <span className="column-label">UNIT PRICE</span>
                          <div className="price-box">{formatPrice(item.price || 0)}</div>
                        </div>
                        <div className="item-column">
                          <span className="column-label">QUANTITY</span>
                          <div className="quantity-control">
                            <button className="qty-btn" disabled>−</button>
                            <span className="qty-value">{item.quantity || 1}</span>
                            <button className="qty-btn" disabled>+</button>
                          </div>
                        </div>
                        <div className="item-column" style={{ alignItems: 'flex-end' }}>
                          <span className="column-label">SUBTOTAL</span>
                          <div className="item-subtotal">{formatPrice(itemTotal)}</div>
                        </div>
                      </div>
                    );
                  })
                )}
              </div>

              <div className="cart-card-footer">
                <div className="cart-total-info">
                  <span className="total-label">CART TOTAL</span>
                  <h3 className="total-value">{formatPrice(total)}</h3>
                </div>
                <button className="btn-checkout">Thanh toán thành công</button>
              </div>
            </div>
          );
        })
      )}

      <div className="new-session-card">
        <ShoppingCart size={40} className="new-session-icon" />
        <h3 className="new-session-title">Initialize New Session</h3>
        <p className="new-session-desc">
          Create a separate session for a different project, client, or procurement stage.
        </p>
        <button className="btn-new-session" onClick={() => window.location.href = '/orders/create'}>NEW SESSION</button>
      </div>
    </div>
  );
};

export default CartsPage;
