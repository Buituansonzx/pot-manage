import React, { useState } from 'react';
import { Search, ChevronDown, ChevronUp, X } from 'lucide-react';
import './SidebarFilter.css';

const SidebarFilter = ({ 
  categories, 
  categoryId, 
  onCategorySelect,
  search,
  setSearch,
  minPrice,
  setMinPrice,
  maxPrice,
  setMaxPrice,
  sortBy,
  setSortBy,
  onClearFilters,
  isOpen,
  onClose
}) => {
  const [expandedParents, setExpandedParents] = useState({});

  const toggleParent = (id) => {
    setExpandedParents(prev => ({ ...prev, [id]: !prev[id] }));
  };

  const hasActiveFilters = Boolean(search || categoryId || minPrice || maxPrice || sortBy);

  return (
    <aside className={`filters-sidebar ${isOpen ? 'open' : ''}`}>
      <div className="mobile-sidebar-header">
        <h2 className="filters-title" style={{ margin: 0 }}>BỘ LỌC</h2>
        <button className="close-sidebar-btn" onClick={onClose}>
          <X size={24} />
        </button>
      </div>

      <div className="sidebar-scrollable-content">
        <div className="filters-header desktop-only">
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <div>
              <h2 className="filters-title">BỘ LỌC</h2>
              <span className="filters-subtitle">TINH CHỈNH</span>
            </div>
            {hasActiveFilters && (
              <button className="clear-filters-btn" onClick={onClearFilters}>
                Xóa lọc
              </button>
            )}
          </div>
        </div>

        <div className="filter-group">
          <label className="filter-label">TÌM KIẾM TÊN</label>
          <div className="search-input-wrapper">
            <Search className="search-icon" size={16} />
            <input 
              type="text" 
              className="search-input" 
              placeholder="Chậu đất nung..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
        </div>

        <div className="filter-group">
          <label className="filter-label">DANH MỤC</label>
          <div className="category-menu">
            {categories.map(cat => (
              <div key={cat.id} className="category-item">
                <button 
                  className={`category-parent-btn ${categoryId === cat.id ? 'active' : ''}`}
                  onClick={() => {
                    if (cat.children && cat.children.length > 0) {
                      toggleParent(cat.id);
                    } else {
                      onCategorySelect(cat.id);
                    }
                  }}
                >
                  {cat.name}
                  {cat.children && cat.children.length > 0 && (
                    expandedParents[cat.id] ? <ChevronUp size={16} /> : <ChevronDown size={16} />
                  )}
                </button>
                
                {cat.children && cat.children.length > 0 && expandedParents[cat.id] && (
                  <div className="category-children">
                    {cat.children.map(child => (
                      <button
                        key={child.id}
                        className={`category-child ${categoryId === child.id ? 'selected' : ''}`}
                        onClick={() => onCategorySelect(child.id)}
                      >
                        {child.name}
                      </button>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>

        <div className="filter-group">
          <label className="filter-label">KHOẢNG GIÁ</label>
          <div className="price-inputs">
            <input 
              type="number" 
              className="price-input" 
              placeholder="0 VNĐ"
              value={minPrice}
              onChange={(e) => setMinPrice(e.target.value)} 
            />
            <span className="price-separator">-</span>
            <input 
              type="number" 
              className="price-input" 
              placeholder="2.000.000 VNĐ"
              value={maxPrice}
              onChange={(e) => setMaxPrice(e.target.value)} 
            />
          </div>
        </div>

        <div className="filter-group">
          <label className="filter-label">SẮP XẾP THEO</label>
          <select 
            className="sort-select" 
            value={sortBy} 
            onChange={(e) => setSortBy(e.target.value)}
          >
            <option value="">Mới nhất</option>
            <option value="price_asc">Giá tăng dần</option>
            <option value="price_desc">Giá giảm dần</option>
          </select>
        </div>
      </div>
    </aside>
  );
};

export default SidebarFilter;
