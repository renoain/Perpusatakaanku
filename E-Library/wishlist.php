<?php 
require_once 'config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Wishlist Saya | E-Library</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body { font-family: 'Heebo', sans-serif; }
    .back-btn {
      position: fixed; bottom: 30px; left: 30px; z-index: 999; width: 50px; height: 50px;
      background: #06BBCC; color: white; border: none; border-radius: 50%;
      display: flex; align-items: center; justify-content: center; font-size: 20px;
      cursor: pointer; box-shadow: 0 4px 12px rgba(6, 187, 204, 0.4); transition: all 0.3s ease;
    }
    .back-btn:hover { background: #05a0b0; transform: translateY(-3px); }
    .book-card { 
      cursor: pointer; 
      transition: transform 0.3s ease, box-shadow 0.3s ease; 
      position: relative;
      border-radius: 12px;
      overflow: hidden;
    }
    .book-card:hover { 
      transform: translateY(-5px); 
      box-shadow: 0 8px 20px rgba(0,0,0,0.15); 
    }
    .stock-badge { 
      position: absolute; 
      top: 10px; 
      left: 10px; 
      z-index: 10; 
      font-size: 0.85rem; 
      padding: 5px 10px; 
    }
    .remove-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 10;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: rgba(220, 53, 69, 0.9);
      border: 2px solid white;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
    }
    .remove-btn:hover {
      background: #dc3545;
      transform: scale(1.1);
    }
    .empty-state {
      padding: 100px 20px;
      text-align: center;
    }
    .empty-state i {
      font-size: 100px;
      color: #e0e0e0;
      margin-bottom: 30px;
    }
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(100%); opacity: 0; }
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="user.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>E-Library</h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="user.php" class="nav-item nav-link">Beranda</a>
        <a href="books-user.php" class="nav-item nav-link">Koleksi Buku</a>
        <a href="wishlist.php" class="nav-item nav-link active">Wishlist</a>
        <a href="logout.php" class="nav-item nav-link">Logout</a>
      </div>
    </div>
  </nav>

  <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 100px 0; margin-bottom: 50px; position: relative;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(24, 29, 56, .4);"></div>
    <div class="container position-relative text-center">
      <h1 class="display-3 text-white mb-3"><i class="fa fa-heart me-3"></i>Wishlist Saya</h1>
      <p class="text-white lead">Buku-buku favorit yang ingin Anda baca</p>
    </div>
  </div>

  <section class="container py-5">
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h3><i class="fa fa-heart text-danger me-2"></i>Daftar Wishlist</h3>
            <p class="text-muted mb-0">Total: <span id="totalWishlist" class="fw-bold">0</span> buku</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4" id="wishlist-container">
      <div class="col-12 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </section>

  <footer class="bg-dark text-light pt-5 mt-5">
    <div class="container text-center pb-3">
      <p>&copy; 2025 E-Library. All Rights Reserved.</p>
    </div>
  </footer>

  <button class="back-btn" onclick="window.location.href='user.php'"><i class="fa fa-arrow-left"></i></button>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let wishlistData = [];
  const userId = <?php echo $user_id; ?>;

  async function loadWishlist() {
    try {
      console.log('📚 Loading wishlist for user:', userId);
      
      const response = await fetch('api_wishlist.php?action=list');
      const result = await response.json();
      
      console.log('Wishlist API Response:', result);
      
      if (result.success) {
        wishlistData = result.data || [];
        displayWishlist();
      } else {
        console.error('Failed to load wishlist:', result.message);
        showError('Gagal memuat wishlist: ' + result.message);
      }
    } catch (error) {
      console.error('Error loading wishlist:', error);
      showError('Terjadi kesalahan saat memuat wishlist');
    }
  }

  function displayWishlist() {
    const container = document.getElementById('wishlist-container');
    const totalSpan = document.getElementById('totalWishlist');
    
    totalSpan.textContent = wishlistData.length;

    if (wishlistData.length === 0) {
      container.innerHTML = `
        <div class="col-12">
          <div class="empty-state">
            <i class="fa fa-heart-broken"></i>
            <h3 class="text-muted mb-3">Wishlist Kosong</h3>
            <p class="text-muted mb-4">Anda belum menambahkan buku apapun ke wishlist.</p>
            <a href="books-user.php" class="btn btn-primary btn-lg">
              <i class="fa fa-book me-2"></i>Jelajahi Koleksi Buku
            </a>
          </div>
        </div>
      `;
      return;
    }

    container.innerHTML = '';

    wishlistData.forEach(item => {
      const stock = parseInt(item.stok) || 0;
      const isOutOfStock = stock <= 0;
      const categoryDisplay = (item.category || 'Lainnya').toUpperCase();
      const descPreview = (item.description || 'Tidak ada deskripsi').substring(0, 100);
      
      container.innerHTML += `
        <div class="col-lg-3 col-md-4 col-sm-6" id="wishlist-item-${item.id}">
          <div class="card h-100 shadow-sm border-0 book-card" onclick="goToBook(${item.book_id})">
            <div class="position-relative">
              <img src="${item.cover}" class="card-img-top" style="height: 300px; object-fit: cover;" 
                   alt="${item.title}" onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
              <span class="badge stock-badge ${stock <= 0 ? 'bg-secondary' : stock <= 3 ? 'bg-warning' : 'bg-success'}">
                ${isOutOfStock ? 'Stok Habis' : 'Stok: ' + stock}
              </span>
              <button class="remove-btn" onclick="event.stopPropagation(); removeFromWishlist(${item.id}, ${item.book_id}, '${item.title.replace(/'/g, "\\'")}')">
                <i class="fa fa-times"></i>
              </button>
            </div>
            <div class="card-body d-flex flex-column">
              <span class="badge bg-primary mb-2 align-self-start">${categoryDisplay}</span>
              <h5 class="card-title">${item.title}</h5>
              <p class="text-muted mb-1 small"><i class="fa fa-user me-1"></i>${item.author}</p>
              <p class="text-muted mb-2 small"><i class="fa fa-calendar me-1"></i>${item.year}</p>
              <p class="card-text text-muted small flex-grow-1">${descPreview}...</p>
              <button class="btn btn-success btn-sm w-100 mt-3" onclick="event.stopPropagation(); goToBook(${item.book_id})">
                <i class="fa fa-eye me-1"></i>Lihat Detail
              </button>
            </div>
          </div>
        </div>
      `;
    });

    console.log('✅ Wishlist displayed:', wishlistData.length, 'books');
  }

  async function removeFromWishlist(wishlistId, bookId, bookTitle) {
    if (!confirm(`Hapus "${bookTitle}" dari wishlist?`)) return;

    try {
      console.log('🗑️ Removing from wishlist:', { wishlistId, bookId, bookTitle });

      // Show loading state
      const itemElement = document.getElementById(`wishlist-item-${wishlistId}`);
      if (itemElement) {
        itemElement.style.opacity = '0.5';
        itemElement.style.pointerEvents = 'none';
      }

      const formData = new FormData();
      formData.append('action', 'remove');
      formData.append('book_id', bookId);

      const response = await fetch('api_wishlist.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();
      console.log('Remove API Response:', result);

      if (result.success) {
        // Remove item from UI with animation
        if (itemElement) {
          itemElement.style.transition = 'all 0.3s ease';
          itemElement.style.transform = 'scale(0)';
          itemElement.style.opacity = '0';
          
          setTimeout(() => {
            itemElement.remove();
            
            // Update wishlist data
            wishlistData = wishlistData.filter(item => item.id !== wishlistId);
            document.getElementById('totalWishlist').textContent = wishlistData.length;
            
            // Show empty state if no items left
            if (wishlistData.length === 0) {
              displayWishlist();
            }
          }, 300);
        }
        
        showToast(result.message || 'Berhasil dihapus dari wishlist', 'success');
      } else {
        // Restore item state on error
        if (itemElement) {
          itemElement.style.opacity = '1';
          itemElement.style.pointerEvents = 'auto';
        }
        showToast(result.message || 'Gagal menghapus dari wishlist', 'error');
      }
    } catch (error) {
      console.error('Error removing from wishlist:', error);
      
      // Restore item state on error
      const itemElement = document.getElementById(`wishlist-item-${wishlistId}`);
      if (itemElement) {
        itemElement.style.opacity = '1';
        itemElement.style.pointerEvents = 'auto';
      }
      
      showToast('Terjadi kesalahan saat menghapus', 'error');
    }
  }

  function goToBook(bookId) {
    window.location.href = 'books-user.php?book=' + bookId;
  }

  function showError(message) {
    const container = document.getElementById('wishlist-container');
    container.innerHTML = `
      <div class="col-12">
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle me-2"></i>${message}
        </div>
      </div>
    `;
  }

  function showToast(message, type = 'info') {
    const bgColor = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#06BBCC';
    const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ';
    
    const toast = document.createElement('div');
    toast.style.cssText = `
      position: fixed; top: 20px; right: 20px; z-index: 99999;
      background: ${bgColor}; color: white; padding: 15px 20px;
      border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      animation: slideIn 0.3s ease; display: flex; align-items: center; gap: 10px;
      font-weight: 500;
    `;
    toast.innerHTML = `<span style="font-size: 20px;">${icon}</span> ${message}`;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Load wishlist on page load
  document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Page loaded, loading wishlist...');
    loadWishlist();
  });
</script>
</body>
</html>