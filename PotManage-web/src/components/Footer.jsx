import React from 'react';
import './Footer.css';

const Footer = () => {
  return (
    <footer className="footer-section">
      <div className="footer-container">
        <div className="footer-top">
          <div className="footer-brand">
            <h2 className="footer-title">PotManage.</h2>
            <p className="footer-desc">
              Mang hơi thở thủ công vào không gian số.<br/>
              Gốm sứ tự nhiên, chạm đến tâm hồn.
            </p>
          </div>
          <div className="footer-links-grid">
            <div className="footer-column">
              <h4>Bộ sưu tập</h4>
              <a href="#bonsai">Chậu Bonsai</a>
              <a href="#angda">Ang Đá</a>
              <a href="#tuonggom">Tượng Gốm</a>
              <a href="#dontrungbay">Đôn Trưng Bày</a>
            </div>
            <div className="footer-column">
              <h4>Về chúng tôi</h4>
              <a href="#story">Câu chuyện xưởng</a>
              <a href="#artisan">Nghệ nhân</a>
              <a href="#process">Quy trình nung</a>
            </div>
            <div className="footer-column">
              <h4>Chăm sóc</h4>
              <a href="#care">Bảo quản</a>
              <a href="#shipping">Giao hàng</a>
              <a href="#contact">Liên hệ</a>
            </div>
          </div>
        </div>
        
        <div className="footer-bottom">
          <p>&copy; {new Date().getFullYear()} PotManage. The Curated Atelier.</p>
          <div className="footer-social">
            <a href="#instagram">Instagram</a>
            <a href="#pinterest">Pinterest</a>
          </div>
        </div>
      </div>
    </footer>
  );
};
export default Footer;
