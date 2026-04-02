import React, { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight, Filter } from 'lucide-react';
import { productService } from '../services/productService';
import { categoryService } from '../services/categoryService';
import SidebarFilter from '../components/SidebarFilter';
import ProductCard from '../components/ProductCard';
import './ShopPage.css';

const ShopPage = ({ user }) => {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(false);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  
  const [isMobileFilterOpen, setIsMobileFilterOpen] = useState(false);

  // Filters state
  const [page, setPage] = useState(() => {
    const params = new URLSearchParams(window.location.search);
    const p = parseInt(params.get('page'), 10);
    return !isNaN(p) && p > 0 ? p : 1;
  });
  const [search, setSearch] = useState('');
  const [categoryId, setCategoryId] = useState('');
  const [minPrice, setMinPrice] = useState('');
  const [maxPrice, setMaxPrice] = useState('');
  const [sortBy, setSortBy] = useState('');

  // Sinc page state with URL query ?page=
  useEffect(() => {
    const currentUrl = new URL(window.location);
    const currentParam = currentUrl.searchParams.get('page');
    const paramVal = page === 1 ? null : String(page);

    if (currentParam !== paramVal) {
      if (paramVal === null) {
        currentUrl.searchParams.delete('page');
      } else {
        currentUrl.searchParams.set('page', paramVal);
      }
      window.history.pushState({}, '', currentUrl); // Tạo lịch sử duyệt web để nút Back hoạt động
    }
  }, [page]);

  // Debounce state
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [debouncedMin, setDebouncedMin] = useState('');
  const [debouncedMax, setDebouncedMax] = useState('');

  useEffect(() => {
    const loadCategories = async () => {
      try {
        const response = await categoryService.getCategories();
        if (response && response.data) {
          const flat = response.data;
          const rootNode = flat.filter(c => !c.parent_id);
          rootNode.forEach(root => {
            root.children = flat.filter(c => c.parent_id === root.id);
          });
          setCategories(rootNode);
        }
      } catch (err) {
        console.error('Lỗi tải danh mục', err);
      }
    };
    loadCategories();
  }, []);

  useEffect(() => {
    const timer = setTimeout(() => {
      setDebouncedSearch(search);
      setDebouncedMin(minPrice);
      setDebouncedMax(maxPrice);
      setPage(1);
    }, 600);
    return () => clearTimeout(timer);
  }, [search, minPrice, maxPrice]);

  useEffect(() => {
    const loadProducts = async () => {
      setLoading(true);
      try {
        const response = await productService.getProducts({
          page,
          limit: 20,
          search: debouncedSearch,
          category_id: categoryId,
          min_price: debouncedMin,
          max_price: debouncedMax,
          sort_by: sortBy
        });
        
        if (response && response.data) {
          setProducts(response.data);
          if (response.meta && response.meta.pagination) {
            setTotalPages(response.meta.pagination.total_pages || Math.ceil(response.meta.pagination.total / 20));
            setTotalItems(response.meta.pagination.total);
          }
        }
      } catch (err) {
        console.error('Lỗi tải sản phẩm', err);
      } finally {
        setLoading(false);
      }
    };
    loadProducts();
  }, [page, debouncedSearch, categoryId, debouncedMin, debouncedMax, sortBy]);

  const handleCategorySelect = (id) => {
    setCategoryId(categoryId === id ? '' : id);
    setPage(1);
  };

  const handleClearFilters = () => {
    setSearch('');
    setCategoryId('');
    setMinPrice('');
    setMaxPrice('');
    setSortBy('');
    setPage(1);
  };

  const renderPagination = () => {
    if (totalPages <= 1) return null;
    let pages = [];
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= page - 1 && i <= page + 1)) {
            pages.push(i);
        } else if (i === page - 2 || i === page + 2) {
            pages.push('...');
        }
    }
    pages = pages.filter((item, index, self) => !index || item !== self[index - 1]);

    return (
      <div className="pagination">
        <button className="page-btn" disabled={page === 1} onClick={() => setPage(page - 1)}>
          <ChevronLeft size={16} /> TRƯỚC
        </button>
        {pages.map((p, idx) => (
          p === '...' ? (
            <span key={`dots-${idx}`} className="page-dots">...</span>
          ) : (
            <button 
              key={p} 
              className={`page-item ${page === p ? 'active' : ''}`}
              onClick={() => setPage(p)}
            >
              {p}
            </button>
          )
        ))}
        <button className="page-btn" disabled={page === totalPages} onClick={() => setPage(page + 1)}>
          SAU <ChevronRight size={16} />
        </button>
      </div>
    );
  };

  return (
    <div className="shop-page">
      <div className="mobile-filter-bar">
        <button className="mobile-filter-btn" onClick={() => setIsMobileFilterOpen(true)}>
          <Filter size={18} />
          Bộ Lọc
        </button>
        <span className="mobile-results-count">{totalItems} Kết quả</span>
      </div>

      {isMobileFilterOpen && (
        <div className="filter-overlay" onClick={() => setIsMobileFilterOpen(false)}></div>
      )}

      <SidebarFilter 
        categories={categories}
        categoryId={categoryId}
        onCategorySelect={handleCategorySelect}
        search={search}
        setSearch={setSearch}
        minPrice={minPrice}
        setMinPrice={setMinPrice}
        maxPrice={maxPrice}
        setMaxPrice={setMaxPrice}
        sortBy={sortBy}
        setSortBy={setSortBy}
        onClearFilters={handleClearFilters}
        isOpen={isMobileFilterOpen}
        onClose={() => setIsMobileFilterOpen(false)}
      />

      <section className="products-content">
        <div className="products-header">
          <h1 className="products-title">Bộ Sưu Tập Nghệ Nhân</h1>
          <p className="products-subtitle">
            Hơn {totalItems > 0 ? totalItems : 'nhiều'} tác phẩm gốm thủ công
          </p>
        </div>

        {loading ? (
          <div style={{ textAlign: 'center', padding: '4rem', opacity: 0.5 }}>Đang tải thiết kế...</div>
        ) : products.length > 0 ? (
          <>
            <div className="products-grid">
              {products.map(product => (
                <ProductCard key={product.id} product={product} user={user} />
              ))}
            </div>
            {renderPagination()}
          </>
        ) : (
          <div style={{ padding: '4rem 0', opacity: 0.6, fontSize: '0.9rem' }}>
            Không tìm thấy thiết kế nào phù hợp với bộ lọc.
          </div>
        )}
      </section>
    </div>
  );
};

export default ShopPage;
