document.addEventListener("DOMContentLoaded", () => {
  const wishlistContainer = document.getElementById("wishlist-list");

  function renderWishlist() {
    const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    wishlistContainer.innerHTML = "";

    if (wishlist.length === 0) {
      wishlistContainer.innerHTML = `
        <div class="text-center text-muted py-5">
          <i class="bi bi-heart display-1 d-block mb-3 text-secondary"></i>
          <h5>Belum ada buku di wishlist kamu</h5>
          <p>Tambahkan buku favorit dari halaman koleksi 📚</p>
        </div>
      `;
      return;
    }

    wishlist.forEach((book) => {
      const col = document.createElement("div");
      col.className = "col-lg-4 col-md-6 col-sm-10";
      col.innerHTML = `
        <div class="card h-100 shadow-sm border-0 hover-shadow" style="transition: all .3s;">
          <img src="${book.image}" class="card-img-top" alt="${book.title}" 
               onerror="this.src='img/default-book.jpg'" style="height: 300px; object-fit: cover;">
          <div class="card-body text-center">
            <h5 class="card-title fw-bold">${book.title}</h5>
            <p class="text-muted"><i class="bi bi-person"></i> ${book.author}</p>
            <p class="small text-secondary">${book.description}</p>
          </div>
          <div class="card-footer bg-white border-0 text-center pb-3">
            <button class="btn btn-sm btn-outline-danger me-2" onclick="removeFromWishlist(${book.id})">
              <i class="bi bi-trash"></i> Hapus
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="viewBook(${book.id})">
              <i class="bi bi-info-circle"></i> Detail
            </button>
          </div>
        </div>
      `;
      wishlistContainer.appendChild(col);
    });
  }

  window.removeFromWishlist = function (id) {
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    const book = wishlist.find((b) => b.id === id);
    wishlist = wishlist.filter((b) => b.id !== id);
    localStorage.setItem("wishlist", JSON.stringify(wishlist));
    alert(`🗑️ "${book.title}" dihapus dari wishlist.`);
    renderWishlist();
  };

  window.borrowBook = function (id) {
    const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    const book = wishlist.find((b) => b.id === id);
    let borrowed = JSON.parse(localStorage.getItem("borrowed")) || [];
    let history = JSON.parse(localStorage.getItem("history")) || [];

    if (borrowed.some((b) => b.id === id)) {
      alert(`📚 "${book.title}" sedang kamu pinjam.`);
      return;
    }

    borrowed.push({ ...book, borrowedAt: new Date().toISOString() });
    localStorage.setItem("borrowed", JSON.stringify(borrowed));

    history.push({ ...book, date: new Date().toISOString() });
    localStorage.setItem("history", JSON.stringify(history));

    alert(`✅ "${book.title}" berhasil dipinjam!`);
  };

  window.viewBook = function (id) {
    const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    const book = wishlist.find((b) => b.id === id);
    const modalHTML = `
      <div class="modal fade" id="bookModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">${book.title}</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
              <img src="${book.image}" class="img-fluid rounded mb-3" style="max-height:300px;object-fit:cover;">
              <p class="text-muted mb-1"><i class="bi bi-person"></i> ${book.author}</p>
              <p>${book.description}</p>
              <div id="qrcode" class="d-flex justify-content-center mt-4"></div>
              <p class="small text-secondary mt-2">Scan untuk membaca buku ini</p>
            </div>
            <div class="modal-footer">
              <a href="${book.link}" target="_blank" class="btn btn-success">
                <i class="bi bi-book"></i> Baca
              </a>
              <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    `;

    document.getElementById("bookModal")?.remove();
    document.body.insertAdjacentHTML("beforeend", modalHTML);

    const modal = new bootstrap.Modal(document.getElementById("bookModal"));
    modal.show();

    modal._element.addEventListener("shown.bs.modal", () => {
      const qr = new QRCode(document.getElementById("qrcode"), {
        text: window.location.origin + "/" + book.link,
        width: 128,
        height: 128,
      });
    });
  };

  renderWishlist();
});
