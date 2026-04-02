import React, { useState, useEffect } from 'react';
import { CheckCircle } from 'lucide-react';
import './Toast.css';

export const showToast = (message) => {
  window.dispatchEvent(new CustomEvent('show-toast', { detail: { message } }));
};

const Toast = () => {
  const [toasts, setToasts] = useState([]);

  useEffect(() => {
    const handleAddToast = (e) => {
      const id = Date.now();
      setToasts((prev) => [...prev, { id, message: e.detail.message }]);

      setTimeout(() => {
        setToasts((prev) => prev.filter((t) => t.id !== id));
      }, 3000); // 3 seconds
    };

    window.addEventListener('show-toast', handleAddToast);
    return () => window.removeEventListener('show-toast', handleAddToast);
  }, []);

  if (toasts.length === 0) return null;

  return (
    <div className="toast-container">
      {toasts.map((toast) => (
        <div key={toast.id} className="toast">
          <CheckCircle size={18} className="toast-icon" />
          <span className="toast-message">{toast.message}</span>
        </div>
      ))}
    </div>
  );
};

export default Toast;
