<?php 
require_once 'config.php';

// Ambil semua buku dari database dengan stok real-time
$query = "SELECT * FROM buku ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    die("❌ Error: " . $conn->error);
}

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

$isLoggedIn = is_logged_in();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Koleksi Buku | E-Library</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body { font-family: 'Heebo', sans-serif; }
    .category-btn { transition: all 0.3s ease; }
    .category-btn.active { background-color: #06BBCC !important; color: white !important; border-color: #06BBCC !important; }
    .back-btn {
      position: fixed; bottom: 30px; left: 30px; z-index: 999; width: 50px; height: 50px;
      background: #06BBCC; color: white; border: none; border-radius: 50%;
      display: flex; align-items: center; justify-content: center; font-size: 20px;
      cursor: pointer; box-shadow: 0 4px 12px rgba(6, 187, 204, 0.4); transition: all 0.3s ease;
    }
    .back-btn:hover { background: #05a0b0; transform: translateY(-3px); }
    .book-card { cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .book-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    .stock-badge { position: absolute; top: 10px; left: 10px; z-index: 10; font-size: 0.85rem; padding: 5px 10px; }
    .stock-badge.low-stock { background: #dc3545 !important; animation: pulse 2s infinite; }
    .stock-badge.out-of-stock { background: #6c757d !important; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
    .login-notice {
      background: #fff3cd; padding: 15px; border-radius: 8px; text-align: center;
      border-left: 4px solid #ffc107; margin-bottom: 20px;
    }
    .review-item {
      background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 15px;
      border-left: 3px solid #06BBCC;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>E-Library</h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
      
      </div>
      <?php if ($isLoggedIn): ?>
        <a href="logout.php" class="btn btn-danger py-4 px-lg-5 d-none d-lg-block">
          <i class="fa fa-sign-out-alt me-2"></i>Logout
        </a>
      <?php else: ?>
       
        </a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 100px 0; margin-bottom: 50px; position: relative;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(24, 29, 56, .4);"></div>
    <div class="container position-relative text-center">
      <h1 class="display-3 text-white mb-3"><i class="fa fa-book-open me-3"></i>Koleksi Buku</h1>
      <p class="text-white lead">Temukan buku favoritmu dan pelajari hal baru setiap hari.</p>
    </div>
  </div>

  <section class="container py-5">
    <div class="row mb-4 justify-content-center">
      <div class="col-lg-6">
        <div class="input-group shadow-sm">
          <input type="text" id="searchBook" class="form-control" placeholder="Cari buku...">
          <button class="btn btn-primary" id="searchBtn"><i class="bi bi-search"></i></button>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-12 text-center">
        <div class="btn-group flex-wrap">
          <button class="btn btn-outline-primary category-btn active" data-category="semua"><i class="fa fa-th me-2"></i>Semua</button>
          <button class="btn btn-outline-primary category-btn" data-category="majalah"><i class="fa fa-newspaper me-2"></i>Majalah</button>
          <button class="btn btn-outline-primary category-btn" data-category="koleksi"><i class="fa fa-bookmark me-2"></i>Koleksi</button>
          <button class="btn btn-outline-primary category-btn" data-category="novel"><i class="fa fa-book me-2"></i>Novel</button>
          <button class="btn btn-outline-primary category-btn" data-category="komik"><i class="fa fa-images me-2"></i>Komik</button>
          <button class="btn btn-outline-primary category-btn" data-category="film"><i class="fa fa-film me-2"></i>Film</button>
        </div>
      </div>
    </div>

    <div class="row g-4" id="book-list"></div>
  </section>

  <footer class="bg-dark text-light pt-5 mt-5">
    <div class="container text-center pb-3">
      <p>&copy; 2025 E-Library. All Rights Reserved.</p>
    </div>
  </footer>

  <!-- Book Detail Modal -->
  <div class="modal fade" id="bookModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
          <h5 class="modal-title">Detail Buku</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
              <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-content"><i class="fa fa-info-circle me-2"></i>Detail</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-content"><i class="fa fa-star me-2"></i>Ulasan</button>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="detail-content">
              <div class="row">
                <div class="col-md-4 text-center mb-3">
                  <img id="modalCover" src="" class="img-fluid shadow" style="max-height: 400px; border-radius: 8px;">
                </div>
                <div class="col-md-8">
                  <h3 id="modalBookTitle" class="mb-3"></h3>
                  <p class="text-muted mb-2"><i class="fa fa-user me-2"></i><strong>Penulis:</strong> <span id="modalAuthor"></span></p>
                  <p class="text-muted mb-2"><i class="fa fa-calendar me-2"></i><strong>Tahun:</strong> <span id="modalYear"></span></p>
                  <p class="mb-3"><i class="fa fa-tag me-2"></i><strong>Kategori:</strong> <span id="modalCategory" class="badge bg-primary ms-2"></span></p>
                  <div class="mb-3"><i class="fa fa-star me-2 text-warning"></i><strong>Rating:</strong> <span id="modalRating" class="ms-2"></span></div>
                  <div class="mb-3" id="modalStockInfo"></div>
                  <hr>
                  <h5><i class="fa fa-info-circle me-2"></i>Deskripsi</h5>
                  <p id="modalDescription" class="text-muted" style="text-align: justify;"></p>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="reviews-content">
              <div class="mb-3">
                <h5><i class="fa fa-star me-2 text-warning"></i>Rating & Ulasan</h5>
                <div id="avgRating" class="mb-3"></div>
              </div>
              
              <?php if ($isLoggedIn): ?>
                <!-- Form ulasan untuk user yang sudah login -->
                <div class="mb-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
                  <h6><i class="fa fa-pen me-2"></i>Tulis Ulasan Anda:</h6>
                  <div class="mb-2">
                    <label class="mb-1"><strong>Rating:</strong></label>
                    <div id="starRating" style="font-size: 28px; cursor: pointer;">
                      <i class="far fa-star" data-rating="1"></i>
                      <i class="far fa-star" data-rating="2"></i>
                      <i class="far fa-star" data-rating="3"></i>
                      <i class="far fa-star" data-rating="4"></i>
                      <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <input type="hidden" id="selectedRating" value="0">
                  </div>
                  <textarea id="reviewText" class="form-control mb-2" rows="3" placeholder="Tulis ulasan Anda tentang buku ini..."></textarea>
                  <button class="btn btn-primary" onclick="submitReview()"><i class="fa fa-paper-plane me-2"></i>Kirim Ulasan</button>
                </div>
              <?php else: ?>
                <!-- Notice untuk user yang belum login -->
                <div class="login-notice">
                  <i class="fa fa-info-circle fa-2x mb-2"></i>
                  <h6>Ingin memberikan ulasan?</h6>
                  <p class="mb-2">Silakan login terlebih dahulu untuk memberikan rating dan ulasan pada buku ini.</p>
                  <a href="login.php" class="btn btn-primary btn-sm"><i class="fa fa-sign-in-alt me-2"></i>Login Sekarang</a>
                </div>
              <?php endif; ?>

              <!-- Daftar ulasan -->
              <hr>
              <h6 class="mb-3"><i class="fa fa-comments me-2"></i>Ulasan dari Pembaca</h6>
              <div id="reviewsList">
                <div class="text-center py-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times me-2"></i>Tutup</button>
          <?php if (!$isLoggedIn): ?>
            <a href="login.php" class="btn btn-primary">
              <i class="fa fa-sign-in-alt me-2"></i>Login untuk Meminjam & Review
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <button class="back-btn" onclick="window.history.back()"><i class="fa fa-arrow-left"></i></button>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const booksData = <?php echo json_encode($books); ?>;
  const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
  let currentCategory = 'semua', searchQuery = '';
  let selectedBook = null;
  let bookModal;

  console.log('📚 Total books loaded:', booksData.length);
  console.log('👤 User logged in:', isLoggedIn);

  function displayBooks(category = 'semua', search = '') {
    const bookList = document.getElementById('book-list');
    bookList.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary"></div></div>';

    setTimeout(() => {
      const filteredBooks = booksData.filter(book => {
        const bookCategory = (book.category || '').toLowerCase();
        const matchCategory = category === 'semua' || bookCategory === category.toLowerCase();
        
        const bookTitle = (book.title || '').toLowerCase();
        const bookAuthor = (book.author || '').toLowerCase();
        const searchLower = search.toLowerCase();
        
        const matchSearch = search === '' || 
          bookTitle.includes(searchLower) ||
          bookAuthor.includes(searchLower);
        
        return matchCategory && matchSearch;
      });

      bookList.innerHTML = '';

      if (filteredBooks.length === 0) {
        bookList.innerHTML = `
          <div class="col-12 text-center py-5">
            <i class="fa fa-book-open fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Tidak ada buku ditemukan</h4>
            <p class="text-muted">Coba kata kunci atau kategori lain</p>
          </div>`;
        return;
      }

      filteredBooks.forEach(book => {
        const stock = parseInt(book.stok) || 0;
        const isOutOfStock = stock <= 0;
        const categoryDisplay = (book.category || 'Lainnya').toUpperCase();
        const descPreview = (book.description || 'Tidak ada deskripsi').substring(0, 100);
        
        bookList.innerHTML += `
          <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 book-card" onclick="showBookDetail(${book.id})">
              <div class="position-relative">
                <img src="${book.cover}" class="card-img-top" style="height: 300px; object-fit: cover;" 
                     alt="${book.title}" onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
                <span class="badge stock-badge ${stock <= 0 ? 'out-of-stock' : stock <= 3 ? 'low-stock bg-warning' : 'bg-success'}">
                  ${isOutOfStock ? 'Stok Habis' : 'Stok: ' + stock}
                </span>
              </div>
              <div class="card-body d-flex flex-column">
                <span class="badge bg-primary mb-2 align-self-start">${categoryDisplay}</span>
                <h5 class="card-title">${book.title}</h5>
                <p class="text-muted mb-1 small"><i class="fa fa-user me-1"></i>${book.author}</p>
                <p class="text-muted mb-2 small"><i class="fa fa-calendar me-1"></i>${book.year}</p>
                <p class="card-text text-muted small flex-grow-1">${descPreview}...</p>
                <button class="btn btn-primary btn-sm w-100 mt-3" onclick="event.stopPropagation(); showBookDetail(${book.id})">
                  <i class="fa fa-eye me-1"></i>Lihat Detail
                </button>
              </div>
            </div>
          </div>`;
      });
    }, 300);
  }

  async function showBookDetail(bookId) {
    console.log('=== 📖 SHOW BOOK DETAIL ===');
    console.log('Book ID:', bookId);
    
    const book = booksData.find(b => parseInt(b.id) === parseInt(bookId));
    if (!book) {
      console.error('❌ Book not found:', bookId);
      return;
    }

    console.log('✅ Book found:', book);
    selectedBook = book;
    const stock = parseInt(book.stok) || 0;
    const categoryDisplay = (book.category || 'Lainnya').toUpperCase();

    document.getElementById('modalCover').src = book.cover || 'https://via.placeholder.com/300x400?text=No+Image';
    document.getElementById('modalBookTitle').textContent = book.title;
    document.getElementById('modalAuthor').textContent = book.author;
    document.getElementById('modalYear').textContent = book.year;
    document.getElementById('modalCategory').textContent = categoryDisplay;
    document.getElementById('modalDescription').textContent = book.description || 'Tidak ada deskripsi';

    let stockHTML = `<div class="alert ${stock <= 0 ? 'alert-danger' : stock <= 3 ? 'alert-warning' : 'alert-success'}">
      <i class="fa fa-box me-2"></i><strong>Stok:</strong> ${stock} buku tersedia
      ${stock <= 3 && stock > 0 ? '<br><small><i class="fa fa-exclamation-triangle me-1"></i>Stok terbatas!</small>' : ''}
      ${stock <= 0 ? '<br><small><i class="fa fa-ban me-1"></i>Stok habis saat ini.</small>' : ''}
    </div>`;
    document.getElementById('modalStockInfo').innerHTML = stockHTML;

    // Load reviews
    await loadReviews(book.id);

    bookModal.show();
  }

  async function loadReviews(bookId) {
    console.log('=== ⭐ LOADING REVIEWS ===');
    console.log('Book ID:', bookId);
    
    const reviewsList = document.getElementById('reviewsList');
    reviewsList.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
    
    try {
      // PERBAIKAN: Gunakan api_review.php yang benar
      const response = await fetch(`api_review.php?action=get_reviews&book_id=${bookId}`);
      
      console.log('Response status:', response.status);
      
      const contentType = response.headers.get('content-type');
      console.log('Content-Type:', contentType);
      
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        console.error('❌ Response bukan JSON:', text.substring(0, 500));
        throw new Error('Server tidak mengembalikan JSON');
      }
      
      const result = await response.json();
      console.log('📊 Review result:', result);
      
      if (result.success) {
        const reviews = result.data;
        console.log('✅ Reviews loaded:', reviews.length);
        
        let avgRating = 0;
        
        if (reviews.length > 0) {
          avgRating = (reviews.reduce((sum, r) => sum + parseInt(r.rating), 0) / reviews.length).toFixed(1);
        }

        // Update rating display
        document.getElementById('modalRating').textContent = `${avgRating} / 5.0 (${reviews.length} ulasan)`;
        document.getElementById('avgRating').innerHTML = `
          <div class="d-flex align-items-center gap-3">
            <div style="font-size: 2.5rem; font-weight: bold; color: #ffc107;">${avgRating}</div>
            <div>
              <div style="color: #ffc107; font-size: 1.3rem;">${'★'.repeat(Math.round(avgRating))}${'☆'.repeat(5-Math.round(avgRating))}</div>
              <div class="text-muted">${reviews.length} ulasan</div>
            </div>
          </div>
        `;

        // Display reviews list
        if (reviews.length === 0) {
          reviewsList.innerHTML = `
            <div class="text-center py-4">
              <i class="fa fa-comment-slash fa-3x text-muted mb-3"></i>
              <p class="text-muted">Belum ada ulasan untuk buku ini.</p>
              <small class="text-muted">Jadilah yang pertama memberikan ulasan${isLoggedIn ? '!' : ' setelah login!'}</small>
            </div>
          `;
        } else {
          reviewsList.innerHTML = reviews.map(r => `
            <div class="review-item">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <strong style="color: #06BBCC;"><i class="fa fa-user-circle me-1"></i>${r.user_name}</strong>
                  <div style="color: #ffc107; font-size: 1.1rem;">${'★'.repeat(parseInt(r.rating))}${'☆'.repeat(5-parseInt(r.rating))}</div>
                </div>
                <small class="text-muted"><i class="fa fa-clock me-1"></i>${r.date}</small>
              </div>
              <p class="mb-0" style="line-height: 1.6;">${r.text}</p>
            </div>
          `).join('');
          
          console.log('✅ Reviews displayed successfully');
        }
      } else {
        console.error('❌ Load reviews failed:', result);
        reviewsList.innerHTML = `
          <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle me-2"></i>${result.message || 'Gagal memuat ulasan'}
          </div>
        `;
      }
    } catch (error) {
      console.error('❌ Error loading reviews:', error);
      reviewsList.innerHTML = `
        <div class="alert alert-danger">
          <i class="fa fa-times-circle me-2"></i>Terjadi kesalahan: ${error.message}
        </div>
      `;
    }
  }

  async function submitReview() {
    console.log('=== ✍️ SUBMIT REVIEW ===');
    
    const rating = parseInt(document.getElementById('selectedRating').value);
    const text = document.getElementById('reviewText').value.trim();

    console.log('Rating:', rating);
    console.log('Review text:', text);

    if (rating == 0) {
      showToast('❌ Pilih rating terlebih dahulu!', 'error');
      return;
    }

    if (!text) {
      showToast('❌ Tulis ulasan Anda!', 'error');
      return;
    }

    if (!selectedBook) {
      showToast('❌ Buku tidak ditemukan!', 'error');
      return;
    }

    try {
      const bookId = parseInt(selectedBook.id);
      console.log('Submitting review for book ID:', bookId);
      
      const formData = new FormData();
      formData.append('action', 'add');
      formData.append('book_id', bookId);
      formData.append('rating', rating);
      formData.append('review_text', text);

      console.log('📤 Sending review to API...');

      const response = await fetch('api_review.php', {
        method: 'POST',
        body: formData
      });

      console.log('Response status:', response.status);

      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const responseText = await response.text();
        console.error('❌ Response bukan JSON:', responseText.substring(0, 500));
        showToast('❌ Error: Server tidak mengembalikan JSON', 'error');
        return;
      }

      const result = await response.json();
      console.log('📨 Result:', result);
      
      if (result.success) {
        showToast('✅ ' + result.message, 'success');
        
        // Reset form
        document.getElementById('selectedRating').value = '0';
        document.getElementById('reviewText').value = '';
        document.querySelectorAll('#starRating i').forEach(s => s.className = 'far fa-star');
        
        // Reload reviews
        console.log('🔄 Reloading reviews...');
        await loadReviews(selectedBook.id);
      } else {
        showToast('❌ ' + (result.message || 'Gagal mengirim ulasan'), 'error');
      }
    } catch (error) {
      console.error('❌ Error:', error);
      showToast('❌ Terjadi kesalahan: ' + error.message, 'error');
    }
  }

  function showToast(message, type = 'info') {
    const bgColor = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#06BBCC';
    const toast = document.createElement('div');
    toast.style.cssText = `
      position: fixed; top: 20px; right: 20px; z-index: 99999;
      background: ${bgColor}; color: white; padding: 15px 20px;
      border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      animation: slideIn 0.3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  document.addEventListener("DOMContentLoaded", () => {
    console.log('🚀 Initializing books.php...');
    bookModal = new bootstrap.Modal(document.getElementById('bookModal'));
    displayBooks();

    // Star rating (hanya jika user login)
    if (isLoggedIn) {
      const stars = document.querySelectorAll('#starRating i');
      stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
          const rating = this.getAttribute('data-rating');
          stars.forEach((s, i) => {
            s.className = i < rating ? 'fas fa-star' : 'far fa-star';
            s.style.color = '#ffc107';
          });
        });
        
        star.addEventListener('click', function() {
          const rating = this.getAttribute('data-rating');
          document.getElementById('selectedRating').value = rating;
          console.log('⭐ Rating selected:', rating);
          stars.forEach((s, i) => {
            s.className = i < rating ? 'fas fa-star' : 'far fa-star';
          });
        });
      });

      document.getElementById('starRating').addEventListener('mouseleave', function() {
        const selectedRating = document.getElementById('selectedRating').value;
        stars.forEach((s, i) => {
          s.className = i < selectedRating ? 'fas fa-star' : 'far fa-star';
        });
      });
    }

    // Category buttons
    document.querySelectorAll('.category-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCategory = btn.getAttribute('data-category');
        displayBooks(currentCategory, searchQuery);
      });
    });

    // Search
    document.getElementById('searchBtn').addEventListener('click', () => {
      searchQuery = document.getElementById('searchBook').value;
      displayBooks(currentCategory, searchQuery);
    });

    document.getElementById('searchBook').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        searchQuery = e.target.value;
        displayBooks(currentCategory, searchQuery);
      }
    });
  });

  // Add CSS animation
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(100%); opacity: 0; }
    }
  `;
  document.head.appendChild(style);
</script>
</body>
</html>