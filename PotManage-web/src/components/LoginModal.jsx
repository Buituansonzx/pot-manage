import React, { useState } from 'react';
import { Eye, EyeOff, X, ArrowRight } from 'lucide-react';
import { authService } from '../services/authService';
import './LoginModal.css';

const LoginModal = ({ isOpen, onClose, onSuccess }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [isSuccess, setIsSuccess] = useState(false);

  if (!isOpen) return null;

  const handleClose = () => {
    setIsSuccess(false);
    setLoading(false);
    setError('');
    onClose();
  };

  const handleLogin = async (e) => {
    e.preventDefault();
    if (!email || !password) {
      setError('Vui lòng nhập đầy đủ email và mật khẩu.');
      return;
    }

    setLoading(true);
    setError('');

    try {
      const response = await authService.login({ email, password });
      setIsSuccess(true);
      
      // Delay to let animation play
      setTimeout(() => {
        setLoading(false);
        setEmail('');
        setPassword('');
        onSuccess(response.user);
        setTimeout(() => setIsSuccess(false), 500); // Reset for next time
      }, 2000);
      
    } catch (err) {
      setError(err.message || 'Email hoặc mật khẩu không chính xác.');
      setLoading(false);
    }
  };

  return (
    <div className="login-modal-overlay" onClick={handleClose}>
      <div className="login-modal-content" onClick={(e) => e.stopPropagation()}>
        <button className="login-close-btn" onClick={handleClose}>
          <X size={24} />
        </button>
        
        <div className="login-modal-left">
          <div className="login-quote">
            "Hình thành từ đất, tạo tác bởi ánh sáng, dành riêng cho không gian hiện đại."
          </div>
        </div>
        
        <div className="login-modal-right">
          {isSuccess ? (
            <div className="success-animation-container">
              <svg className="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle className="checkmark-circle" cx="26" cy="26" r="25" fill="none" />
                <path className="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
              </svg>
              <p className="success-text">Đăng nhập thành công</p>
            </div>
          ) : (
            <>
              <h2 className="login-title">Đăng nhập</h2>
              <p className="login-subtitle">Nhập thông tin để truy cập vào tài khoản của bạn.</p>
              
              <form className="login-form" onSubmit={handleLogin}>
                <div className="input-group">
                  <label>Địa chỉ Email</label>
                  <input 
                    type="email" 
                    className="input-field" 
                    placeholder="name@example.com" 
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                  />
                </div>
                
                <div className="input-group">
                  <label>Mật khẩu</label>
                  <div className="password-wrapper">
                    <input 
                      type={showPassword ? "text" : "password"} 
                      className="input-field" 
                      placeholder="••••••••" 
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                    />
                    <button 
                      type="button" 
                      className="password-toggle"
                      onClick={() => setShowPassword(!showPassword)}
                    >
                      {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                    </button>
                  </div>
                </div>

                {error && <div className="login-error">{error}</div>}

                <button type="submit" className="login-btn" disabled={loading}>
                  {loading ? 'Đang xử lý...' : 'Đăng nhập'} <ArrowRight size={18} />
                </button>
              </form>
            </>
          )}

          <div className="login-footer">
            <span>© {new Date().getFullYear()} Atelier Ceramics.</span>
            <div className="login-footer-links">
              <span>Bảo mật</span>
              <span>Điều khoản</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoginModal;
