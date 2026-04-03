import React, { useState, useEffect } from 'react';
import Header from './components/Header';
import Footer from './components/Footer';
import LoginModal from './components/LoginModal';
import Toast from './components/Toast';
import { authService } from './services/authService';
import ShopPage from './pages/ShopPage';
import CartsPage from './pages/CartsPage';

const HomeHero = () => (
  <div style={{ maxWidth: '1440px', margin: '0 auto', padding: '6rem 3rem 0' }}>
    <section style={{ display: 'flex', alignItems: 'center', gap: '4rem', marginBottom: '8rem' }}>
      <div style={{ flex: '1', paddingRight: '2rem' }}>
        <h1 style={{ fontSize: '4.5rem', lineHeight: '1.1', marginBottom: '2rem', fontFamily: 'var(--font-display)', color: 'var(--on-surface)' }}>
          Nghệ thuật<br />từ đất nung.
        </h1>
        <p style={{ fontSize: '1.25rem', opacity: '0.8', marginBottom: '3rem', maxWidth: '500px', color: 'var(--on-surface)' }}>
          Mỗi sản phẩm là một tác phẩm độc bản. Sự bất đối xứng trong từng đường nét là tiếng nói của người nghệ nhân, nâng niu sức sống của thiên nhiên.
        </p>
        <a href="/shop" className="btn-primary" style={{ padding: '1.1rem 2.2rem', fontSize: '1.1rem', textDecoration: 'none', display: 'inline-block' }}>
          Khám phá bộ sưu tập
        </a>
      </div>
      <div style={{ flex: '1', position: 'relative' }}>
        <div style={{ 
          backgroundColor: 'var(--surface-container)', 
          borderRadius: 'var(--radius-xl)', 
          height: '600px', 
          width: '85%', 
          marginLeft: 'auto' 
        }}></div>
        <img 
          src="/photo-1578749556568-bc2c40e68b61.avif" 
          alt="Ceramic Pot Studio" 
          style={{
            position: 'absolute',
            top: '50%',
            left: '-10%',
            transform: 'translateY(-50%)',
            width: '80%',
            height: 'auto',
            borderRadius: 'var(--radius-lg)',
            boxShadow: 'var(--shadow-ambient)'
          }}
        />
      </div>
    </section>
  </div>
);

function App() {
  const [isLoginOpen, setIsLoginOpen] = useState(false);
  const [currentPath, setCurrentPath] = useState(window.location.pathname);
  
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem('user_info');
    return saved ? JSON.parse(saved) : null;
  });

  useEffect(() => {
    const onPopState = () => setCurrentPath(window.location.pathname);
    window.addEventListener('popstate', onPopState);
    return () => window.removeEventListener('popstate', onPopState);
  }, []);

  const handleLoginSuccess = (userData) => {
    setUser(userData);
    setIsLoginOpen(false);
  };

  const handleLogout = async () => {
    await authService.logout();
    setUser(null);
  };

  return (
    <>
      <Header onOpenLogin={() => setIsLoginOpen(true)} user={user} onLogout={handleLogout} />
      
      <main style={{ paddingTop: '80px', minHeight: '80vh' }}>
        {currentPath === '/shop' ? (
          <ShopPage user={user} />
        ) : currentPath === '/carts' ? (
          <CartsPage user={user} />
        ) : (
          <HomeHero />
        )}
      </main>

      <Footer />
      
      <LoginModal 
        isOpen={isLoginOpen} 
        onClose={() => setIsLoginOpen(false)} 
        onSuccess={handleLoginSuccess}
      />
      <Toast />
    </>
  );
}

export default App;
