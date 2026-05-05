<?php 
require_once 'config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();

// Ambil semua buku dari database
$query = "SELECT * FROM buku ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    die("❌ Error: " . $conn->error);
}

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
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
    .back-btn {
      position: fixed; bottom: 30px; left: 30px; z-index: 999; width: 50px; height: 50px;
      background: #06BBCC; color: white; border: none; border-radius: 50%;
      display: flex; align-items: center; justify-content: center; font-size: 20px;
      cursor: pointer; box-shadow: 0 4px 12px rgba(6, 187, 204, 0.4); transition: all 0.3s ease;
    }
    .back-btn:hover { background: #05a0b0; transform: translateY(-3px); }
    .category-btn { transition: all 0.3s ease; }
    .category-btn.active { background-color: #06BBCC !important; color: white !important; border-color: #06BBCC !important; }
    .btn-wishlist { position: relative; transition: all 0.3s ease; }
    .btn-wishlist.in-wishlist { background: #dc3545 !important; border-color: #dc3545 !important; color: white !important; }
    .btn-wishlist:not(.in-wishlist) { background: white; border-color: #dc3545; color: #dc3545; }
    .book-card { cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .book-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    .stock-badge { position: absolute; top: 10px; left: 10px; z-index: 10; font-size: 0.85rem; padding: 5px 10px; }
    .stock-badge.low-stock { background: #dc3545 !important; animation: pulse 2s infinite; }
    .stock-badge.out-of-stock { background: #6c757d !important; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
    
    /* Modal scrollable */
    .modal-body-scrollable {
      max-height: 60vh;
      overflow-y: auto;
    }
    
    .modal-dialog-centered {
      display: flex;
      align-items: center;
      min-height: calc(100% - 3.5rem);
    }
    
    /* Review stars */
    .star-rating {
      font-size: 28px;
      cursor: pointer;
      user-select: none;
    }
    
    .star-rating i {
      transition: color 0.2s ease;
    }
    
    .star-rating i:hover {
      transform: scale(1.1);
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
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-info d-flex justify-content-between align-items-center" style="border-left: 4px solid #06BBCC;">
          <div><i class="fa fa-info-circle me-2"></i><strong>Info:</strong> Buku yang Anda pinjam akan masuk ke halaman Riwayat Peminjaman. Stok otomatis berkurang saat dipinjam dan bertambah saat dikembalikan.</div>
          <a href="riwayat.php" class="btn btn-primary btn-sm"><i class="fa fa-history me-2"></i>Lihat Riwayat</a>
        </div>
      </div>
    </div>

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
          <button class="btn btn-outline-primary category-btn" data-category="kamus"><i class="fa fa-bookmark me-2"></i>Kamus</button>
          <button class="btn btn-outline-primary category-btn" data-category="novel"><i class="fa fa-book me-2"></i>Novel</button>
          <button class="btn btn-outline-primary category-btn" data-category="komik"><i class="fa fa-images me-2"></i>Komik</button>
          <button class="btn btn-outline-primary category-btn" data-category="film"><i class="fa fa-film me-2"></i>Film</button>
        </div>
      </div>
    </div>

    <div class="row g-4" id="book-list">
      <div class="col-12 text-center"><div class="spinner-border text-primary"></div></div>
    </div>
  </section>

  <footer class="bg-dark text-light pt-5 mt-5">
    <div class="container text-center pb-3">
      <p>&copy; 2025 E-Library. All Rights Reserved.</p>
    </div>
  </footer>

  <button class="back-btn" onclick="window.location.href='user.php'"><i class="fa fa-arrow-left"></i></button>

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
                <h5>Rating & Ulasan</h5>
                <div id="avgRating" class="mb-3"></div>
              </div>
              <div class="mb-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
                <h6><i class="fa fa-pen me-2"></i>Tulis Ulasan Anda:</h6>
                <div class="mb-2">
                  <label class="mb-1"><strong>Rating:</strong></label>
                  <div id="starRating" class="star-rating">
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
              <hr>
              <h6 class="mb-3"><i class="fa fa-comments me-2"></i>Ulasan Pengguna</h6>
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
          <button type="button" id="modalWishlistBtn" class="btn btn-outline-danger" onclick="modalToggleWishlist()"><i class="far fa-heart me-2"></i>Wishlist</button>
          <button type="button" id="modalBorrowBtn" class="btn btn-success" onclick="showBorrowForm()"><i class="fa fa-book-reader me-2"></i>Pinjam Buku</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Borrow Form Modal -->
  <div class="modal fade" id="borrowFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="fa fa-book-reader me-2"></i>Form Peminjaman Buku</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body modal-body-scrollable">
          <div class="alert alert-info">
            <i class="fa fa-info-circle me-2"></i>
            <strong>Perhatian:</strong> Buku yang dipinjam harus dikembalikan sesuai tanggal yang ditentukan. Keterlambatan pengembalian akan dikenakan denda.
          </div>
          <form id="borrowForm">
            <input type="hidden" id="borrowBookId" name="book_id">
            
            <div class="mb-3">
              <label class="form-label"><i class="fa fa-book me-2"></i>Buku yang Dipinjam</label>
              <input type="text" id="borrowBookTitle" class="form-control" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label"><i class="fa fa-calendar-alt me-2"></i>Tanggal Peminjaman</label>
              <input type="date" id="borrowDate" name="borrow_date" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label"><i class="fa fa-calendar-check me-2"></i>Tanggal Pengembalian (Maksimal 14 hari)</label>
              <input type="date" id="returnDate" name="return_date" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label"><i class="fa fa-comment me-2"></i>Catatan (Opsional)</label>
              <textarea id="borrowNote" name="note" class="form-control" rows="3" placeholder="Tulis catatan jika ada..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-success" onclick="submitBorrow()">
            <i class="fa fa-check me-2"></i>Konfirmasi Peminjaman
          </button>
        </div>
      </div>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let booksData = <?php echo json_encode($books); ?>;
  let currentCategory = 'semua', searchQuery = '';
  let selectedBook = null;
  let bookModal, borrowFormModal;

  console.log('📚 Books loaded:', booksData.length);

  async function loadBooks() {
    try {
      const response = await fetch('api_books.php?action=list');
      const result = await response.json();
      
      if (result.success) {
        booksData = result.data;
        displayBooks(currentCategory, searchQuery);
      }
    } catch (error) {
      console.error('Error loading books:', error);
    }
  }

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
                <div class="d-flex gap-2 mt-3">
                  <button class="btn btn-sm btn-outline-danger btn-wishlist flex-fill" onclick="event.stopPropagation(); toggleWishlist(${book.id}, this)">
                    <i class="far fa-heart"></i>
                  </button>
                  <button class="btn btn-sm btn-success flex-fill" onclick="event.stopPropagation(); showBookDetail(${book.id})">
                    <i class="fa fa-eye me-1"></i>Detail
                  </button>
                </div>
              </div>
            </div>
          </div>`;
      });

      loadWishlistStatus();
    }, 300);
  }

  async function loadWishlistStatus() {
    try {
      const response = await fetch('api_wishlist.php?action=list');
      const result = await response.json();
      
      if (result.success) {
        const wishlistIds = result.data.map(item => String(item.book_id));
        
        document.querySelectorAll('.btn-wishlist').forEach(btn => {
          const onclick = btn.getAttribute('onclick');
          const match = onclick.match(/toggleWishlist\((\d+)/);
          if (match && wishlistIds.includes(match[1])) {
            btn.classList.add('in-wishlist');
            btn.innerHTML = '<i class="fas fa-heart"></i>';
          }
        });
      }
    } catch (error) {
      console.error('Error loading wishlist status:', error);
    }
  }

  async function toggleWishlist(bookId, btnElement) {
    try {
      const formData = new FormData();
      formData.append('action', 'toggle');
      formData.append('book_id', bookId);

      const response = await fetch('api_wishlist.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();
      
      if (result.success) {
        if (btnElement) {
          if (result.action === 'added') {
            btnElement.classList.add('in-wishlist');
            btnElement.innerHTML = '<i class="fas fa-heart"></i>';
          } else {
            btnElement.classList.remove('in-wishlist');
            btnElement.innerHTML = '<i class="far fa-heart"></i>';
          }
        }
        showToast(result.message, 'success');
      } else {
        showToast(result.message, 'error');
      }
    } catch (error) {
      console.error('Error toggling wishlist:', error);
      showToast('Terjadi kesalahan saat mengubah wishlist', 'error');
    }
  }

  async function checkWishlist(bookId) {
    try {
      const response = await fetch(`api_wishlist.php?action=check&book_id=${bookId}`);
      const result = await response.json();
      return result.inWishlist;
    } catch (error) {
      return false;
    }
  }

  async function checkBorrowed(bookId) {
    try {
      const response = await fetch(`api_books.php?action=check_borrowed&book_id=${bookId}`);
      const result = await response.json();
      return result.isBorrowed;
    } catch (error) {
      return false;
    }
  }

  function showBorrowForm() {
    if (!selectedBook || !selectedBook.id) {
      showToast('Error: Data buku tidak ditemukan!', 'error');
      return;
    }

    const bookId = parseInt(selectedBook.id);
    const bookTitle = selectedBook.title;
    const stock = parseInt(selectedBook.stok || 0);
    
    if (stock <= 0) {
      showToast('Stok buku habis! Tidak dapat meminjam.', 'error');
      return;
    }

    document.getElementById('borrowBookId').value = bookId;
    document.getElementById('borrowBookTitle').value = bookTitle;
    
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('borrowDate').value = today;
    document.getElementById('borrowDate').min = today;
    
    const maxDate = new Date();
    maxDate.setDate(maxDate.getDate() + 14);
    const maxDateStr = maxDate.toISOString().split('T')[0];
    
    document.getElementById('returnDate').value = '';
    document.getElementById('returnDate').min = today;
    document.getElementById('returnDate').max = maxDateStr;
    
    document.getElementById('borrowNote').value = '';

    bookModal.hide();
    
    setTimeout(() => {
      borrowFormModal.show();
    }, 300);
  }

  async function submitBorrow() {
    const bookId = parseInt(document.getElementById('borrowBookId').value);
    const borrowDate = document.getElementById('borrowDate').value;
    const returnDate = document.getElementById('returnDate').value;
    const note = document.getElementById('borrowNote').value;

    if (!bookId || isNaN(bookId) || bookId <= 0) {
      showToast('❌ ID buku tidak valid!', 'error');
      return;
    }

    if (!borrowDate || !returnDate) {
      showToast('Tanggal peminjaman dan pengembalian harus diisi!', 'error');
      return;
    }

    const borrow = new Date(borrowDate);
    const returnD = new Date(returnDate);
    const diffDays = Math.ceil((returnD - borrow) / (1000 * 60 * 60 * 24));

    if (diffDays > 14) {
      showToast('Maksimal peminjaman adalah 14 hari!', 'error');
      return;
    }

    if (diffDays < 1) {
      showToast('Tanggal pengembalian harus setelah tanggal peminjaman!', 'error');
      return;
    }

    try {
      const formData = new FormData();
      formData.append('action', 'borrow');
      formData.append('book_id', bookId);
      formData.append('borrow_date', borrowDate);
      formData.append('return_date', returnDate);
      formData.append('note', note);
      
      const response = await fetch('api_books.php', {
        method: 'POST',
        body: formData
      });

      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        console.error('Response bukan JSON:', text);
        showToast('Error: Server error. Periksa file api_books.php', 'error');
        return;
      }
      
      const result = await response.json();
      
      if (result.success) {
        showToast('✅ ' + result.message, 'success');
        borrowFormModal.hide();
        document.getElementById('borrowForm').reset();
        
        await loadBooks();
        
        setTimeout(() => {
          window.location.href = 'riwayat.php';
        }, 2000);
      } else {
        showToast('❌ ' + result.message, 'error');
      }
    } catch (error) {
      console.error('Exception:', error);
      showToast('Terjadi kesalahan: ' + error.message, 'error');
    }
  }

  async function showBookDetail(bookId) {
    console.log('=== 📖 SHOW BOOK DETAIL ===');
    bookId = parseInt(bookId);
    
    const book = booksData.find(b => parseInt(b.id) === bookId);
    
    if (!book) {
      showToast('Error: Buku tidak ditemukan!', 'error');
      return;
    }

    selectedBook = book;
    console.log('Selected book:', selectedBook);
    
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
      ${stock <= 3 && stock > 0 ? '<br><small><i class="fa fa-exclamation-triangle me-1"></i>Stok terbatas, segera pinjam!</small>' : ''}
      ${stock <= 0 ? '<br><small><i class="fa fa-ban me-1"></i>Stok habis, tidak dapat meminjam saat ini.</small>' : ''}
    </div>`;
    document.getElementById('modalStockInfo').innerHTML = stockHTML;

    // Load reviews
    await loadReviews(book.id);

    const inWishlist = await checkWishlist(book.id);
    const wishlistBtn = document.getElementById('modalWishlistBtn');
    wishlistBtn.innerHTML = inWishlist ? '<i class="fas fa-heart me-2"></i>Hapus dari Wishlist' : '<i class="far fa-heart me-2"></i>Tambah ke Wishlist';
    wishlistBtn.className = inWishlist ? 'btn btn-danger' : 'btn btn-outline-danger';

    const isBorrowed = await checkBorrowed(book.id);
    const borrowBtn = document.getElementById('modalBorrowBtn');
    borrowBtn.disabled = isBorrowed || stock <= 0;
    borrowBtn.innerHTML = isBorrowed ? '<i class="fa fa-check me-2"></i>Sudah Dipinjam' : stock <= 0 ? '<i class="fa fa-ban me-2"></i>Stok Habis' : '<i class="fa fa-book-reader me-2"></i>Pinjam Buku';

    bookModal.show();
  }

  async function loadReviews(bookId) {
    console.log('=== ⭐ LOADING REVIEWS ===');
    console.log('Book ID:', bookId);
    
    const reviewsList = document.getElementById('reviewsList');
    reviewsList.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
    
    try {
      const response = await fetch(`api_review.php?action=get_reviews&book_id=${bookId}`);
      
      console.log('Response status:', response.status);
      
      const contentType = response.headers.get('content-type');
      console.log('Content-Type:', contentType);
      
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        console.error('Response bukan JSON:', text.substring(0, 500));
        throw new Error('Server tidak mengembalikan JSON');
      }
      
      const result = await response.json();
      console.log('Review result:', result);
      
      if (result.success) {
        const reviews = result.data;
        let avgRating = 0;
        
        if (reviews.length > 0) {
          avgRating = (reviews.reduce((sum, r) => sum + parseFloat(r.rating), 0) / reviews.length).toFixed(1);
        }

        document.getElementById('modalRating').textContent = `${avgRating} / 5.0 (${reviews.length} ulasan)`;
        document.getElementById('avgRating').innerHTML = `
          <div class="d-flex align-items-center gap-2">
            <div style="font-size: 2rem; color: #ffc107;">★ ${avgRating}</div>
            <div>
              <div><strong>${avgRating} / 5.0</strong></div>
              <div class="text-muted">${reviews.length} ulasan</div>
            </div>
          </div>
        `;

        if (reviews.length === 0) {
          reviewsList.innerHTML = '<p class="text-muted text-center py-3"><i class="fa fa-comment-slash me-2"></i>Belum ada ulasan. Jadilah yang pertama memberikan ulasan!</p>';
        } else {
          reviewsList.innerHTML = reviews.map(r => `
            <div class="mb-3 p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 3px solid #06BBCC;">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <strong style="color: #06BBCC;"><i class="fa fa-user-circle me-1"></i>${r.user_name}</strong>
                  <div style="color: #ffc107; font-size: 1.1rem;">${'★'.repeat(r.rating)}${'☆'.repeat(5-r.rating)}</div>
                </div>
                <small class="text-muted"><i class="fa fa-clock me-1"></i>${r.date}</small>
              </div>
              <p class="mb-0" style="line-height: 1.6;">${r.text}</p>
            </div>
          `).join('');
          
          console.log('✅ Reviews displayed:', reviews.length);
        }
      } else {
        console.error('Load reviews failed:', result);
        reviewsList.innerHTML = '<p class="text-muted text-center py-3">Gagal memuat ulasan.</p>';
      }
    } catch (error) {
      console.error('❌ Error loading reviews:', error);
      reviewsList.innerHTML = '<p class="text-muted text-center py-3 text-danger"><i class="fa fa-exclamation-triangle me-2"></i>Error: ' + error.message + '</p>';
    }
  }

  function modalToggleWishlist() {
    if (selectedBook) {
      toggleWishlist(selectedBook.id);
    }
  }

  async function submitReview() {
    console.log('=== ✍️ SUBMIT REVIEW ===');
    
    const rating = parseInt(document.getElementById('selectedRating').value);
    const text = document.getElementById('reviewText').value.trim();

    console.log('Rating:', rating);
    console.log('Review text length:', text.length);
    console.log('Selected book:', selectedBook);

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
        console.error('Response bukan JSON:', responseText.substring(0, 500));
        showToast('❌ Error: Server tidak mengembalikan JSON. Periksa api_review.php', 'error');
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
        showToast('❌ ' + result.message, 'error');
      }
    } catch (error) {
      console.error('❌ Error submitting review:', error);
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

  document.addEventListener('DOMContentLoaded', () => {
    bookModal = new bootstrap.Modal(document.getElementById('bookModal'));
    borrowFormModal = new bootstrap.Modal(document.getElementById('borrowFormModal'));

    displayBooks();

    // Star rating functionality
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

    // Category buttons
    document.querySelectorAll('.category-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCategory = btn.getAttribute('data-category');
        displayBooks(currentCategory, searchQuery);
      });
    });

    // Search functionality
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

  // Add CSS animations
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