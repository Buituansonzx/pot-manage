import React, { useState, useRef, useEffect } from 'react';
import { ChevronDown, LogOut, User, ShoppingCart, Plus } from 'lucide-react';
import { orderService } from '../services/orderService';
import './Header.css';

const Header = ({ onOpenLogin, user, onLogout }) => {
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [activeOrders, setActiveOrders] = useState([]);
  const dropdownRef = useRef(null);
  const cartRef = useRef(null);

  const fetchActiveOrders = async () => {
    if (!user) return;
    try {
      const resp = await orderService.getOrders({ status: 'pending' });
      setActiveOrders(resp.data || []);
    } catch (e) {
      console.error('Failed to load active orders', e);
    }
  };

  useEffect(() => {
    fetchActiveOrders();

    const handleCartUpdate = () => fetchActiveOrders();
    window.addEventListener('cart-updated', handleCartUpdate);
    return () => window.removeEventListener('cart-updated', handleCartUpdate);
  }, [user]);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setIsDropdownOpen(false);
      }
      if (cartRef.current && !cartRef.current.contains(event.target)) {
        setIsCartOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const getRoleName = (roles) => {
    if (!roles || roles.length === 0) return 'Thành viên';
    if (roles.includes('admin')) return 'Quản trị viên';
    if (roles.includes('nhan_vien_ban_hang') || roles.includes('sales')) return 'Nhân viên bán hàng';
    if (roles.includes('cong_tac_vien') || roles.includes('collaborator')) return 'Cộng tác viên';
    return roles[0];
  };

  return (
    <header className="header-glass">
      <div className="header-container">
        <div className="header-logo">PotManage.</div>
        <nav className="header-nav">
          <a href="/shop" className="nav-link">Cửa hàng</a>
          <a href="/about" className="nav-link">Câu chuyện</a>
          <a href="/journal" className="nav-link">Tạp chí</a>
        </nav>
        <div className="header-actions">
          {user ? (
            <>
              <div className="cart-container" ref={cartRef}>
                <button 
                  className={`cart-icon-btn ${isCartOpen ? 'active' : ''}`}
                  onClick={() => setIsCartOpen(!isCartOpen)}
                >
                  <ShoppingCart size={24} color="var(--on-surface)" />
                  {activeOrders.length > 0 && (
                    <span className="cart-badge">{activeOrders.length}</span>
                  )}
                </button>

                {isCartOpen && (
                  <div className="cart-dropdown-menu">
                    <div className="cart-dropdown-header">GIỎ HÀNG ĐANG HOẠT ĐỘNG</div>
                    <div className="cart-orders-list">
                      {activeOrders.length === 0 ? (
                        <p className="cart-empty-text">Chưa có giỏ hàng nào.</p>
                      ) : (
                        activeOrders.map((order) => (
                          <div key={order.id} className="cart-order-item">
                            <div className="cart-order-info">
                              <span className="cart-customer-name">
                                {order.customer_name || 'Khách vãng lai'}
                                <span className="active-dot"></span>
                              </span>
                              <span className="cart-customer-phone">
                                {order.customer_phone || 'Không có số điện thoại'}
                              </span>
                            </div>
                            <div className="cart-order-meta">
                              <span className="cart-items-count">
                                <ShoppingCart size={12} style={{marginRight: 4}} />
                                {(order.items || []).length} sản phẩm
                              </span>
                            </div>
                          </div>
                        ))
                      )}
                    </div>
                    <button 
                      className="cart-create-new-btn"
                      onClick={() => window.location.href = '/carts'}
                    >
                      XEM CHI TIẾT
                    </button>
                  </div>
                )}
              </div>

              <div className="user-profile-container" ref={dropdownRef}>
              <button 
                className={`user-profile-pill ${isDropdownOpen ? 'active' : ''}`}
                onClick={() => setIsDropdownOpen(!isDropdownOpen)}
              >
                <div className="user-avatar">
                  <User size={16} color="var(--on-primary)" />
                </div>
                <span className="user-profile-text">
                  {getRoleName(user.roles)} - {user.name}
                </span>
                <ChevronDown size={16} className={`chevron-icon ${isDropdownOpen ? 'open' : ''}`} />
              </button>

              {/* Dropdown Menu */}
              {isDropdownOpen && (
                <div className="profile-dropdown-menu">
                  <div className="dropdown-header">
                    <p className="dropdown-email">{user.email}</p>
                  </div>
                  <div className="dropdown-divider"></div>
                  <button className="dropdown-item option-logout" onClick={() => {
                    setIsDropdownOpen(false);
                    onLogout();
                  }}>
                    <LogOut size={16} />
                    <span>Đăng xuất</span>
                  </button>
                </div>
              )}
            </div>
            </>
          ) : (
            <button className="btn-primary" onClick={onOpenLogin}>
              Đăng nhập
            </button>
          )}
        </div>
      </div>
    </header>
  );
};

export default Header;
