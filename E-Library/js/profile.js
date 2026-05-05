document.addEventListener("DOMContentLoaded", () => {
  const profilePic = document.getElementById("profilePic");
  const uploadPhoto = document.getElementById("uploadPhoto");

  const profileName = document.getElementById("profileName");
  const profileEmail = document.getElementById("profileEmail");
  const profileRole = document.getElementById("profileRole");
  const profileAddress = document.getElementById("profileAddress");
  const profilePhone = document.getElementById("profilePhone");
  const profileBirth = document.getElementById("profileBirth");

  const editProfileBtn = document.getElementById("editProfileBtn");
  const logoutBtn = document.getElementById("logoutBtn");
  const deleteAccountBtn = document.getElementById("deleteAccountBtn");

  const editProfileModal = new bootstrap.Modal(document.getElementById("editProfileModal"));
  const editForm = document.getElementById("editProfileForm");
  const editName = document.getElementById("editName");
  const editEmail = document.getElementById("editEmail");
  const editPassword = document.getElementById("editPassword");
  const oldPassword = document.getElementById("oldPassword");
  const editProfilePic = document.getElementById("editProfilePic");
  const editProfilePicPreview = document.getElementById("editProfilePicPreview");
  const editAddress = document.getElementById("editAddress");
  const editPhone = document.getElementById("editPhone");
  const editBirth = document.getElementById("editBirth");

  const defaultUser = {
    name: "Pengguna E-Library",
    email: "guest@elibrary.com",
    password: "",
    role: "user",
    photo: "img/Profil Pengguna.jpeg",
    address: "",
    phone: "",
    birth: ""
  };

  const getCurrentUser = () => JSON.parse(localStorage.getItem("currentUser")) || defaultUser;
  const saveCurrentUser = (u) => localStorage.setItem("currentUser", JSON.stringify(u));

  function renderProfile() {
    const user = getCurrentUser();
    profilePic.src = user.photo || "img/default-profile.png";
    profileName.textContent = user.name;
    profileEmail.textContent = user.email;
    profileRole.textContent = `Peran: ${user.role === "admin" ? "Admin" : "User"}`;
    profileAddress.textContent = user.address || "Belum diatur";
    profilePhone.textContent = user.phone || "Belum diatur";
    profileBirth.textContent = user.birth || "Belum diatur";
  }

  profilePic.addEventListener("click", () => uploadPhoto.click());
  uploadPhoto.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (ev) => {
        const user = getCurrentUser();
        user.photo = ev.target.result;
        saveCurrentUser(user);
        renderProfile();
      };
      reader.readAsDataURL(file);
    }
  });

  editProfileBtn.addEventListener("click", () => {
    const user = getCurrentUser();
    editName.value = user.name;
    editEmail.value = user.email;
    editPassword.value = "";
    oldPassword.value = "";
    editProfilePicPreview.src = user.photo || "img/default-profile.png";
    editAddress.value = user.address || "";
    editPhone.value = user.phone || "";
    editBirth.value = user.birth || "";
    editProfileModal.show();
  });

  editProfilePic.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (ev) => (editProfilePicPreview.src = ev.target.result);
      reader.readAsDataURL(file);
    }
  });

  editForm.addEventListener("submit", (e) => {
    e.preventDefault();
    let user = getCurrentUser();

    // Cek password lama jika sebelumnya sudah pernah diset
    if (user.password && oldPassword.value.trim() !== user.password) {
      alert("❌ Password lama salah! Perubahan tidak disimpan.");
      return;
    }

    user.name = editName.value.trim();
    user.email = editEmail.value.trim();
    if (editPassword.value.trim()) user.password = editPassword.value.trim();
    user.photo = editProfilePicPreview.src;
    user.address = editAddress.value.trim();
    user.phone = editPhone.value.trim();
    user.birth = editBirth.value;

    user.role = (user.email === "admin@gmail.com" && user.password === "admin") ? "admin" : "user";

    saveCurrentUser(user);
    editProfileModal.hide();
    renderProfile();
    alert("✅ Profil berhasil diperbarui!");
  });

  logoutBtn.addEventListener("click", () => {
    if (confirm("Yakin ingin logout?")) {
      localStorage.removeItem("currentUser");
      alert("Kamu telah logout!");
      window.location.href = "index.html";
    }
  });

  deleteAccountBtn.addEventListener("click", () => {
    if (confirm("Apakah kamu yakin ingin menghapus akun ini secara permanen?")) {
      localStorage.removeItem("currentUser");
      alert("Akun berhasil dihapus ❌");
      window.location.href = "index.html";
    }
  });

  const user = getCurrentUser();
  if (user.role !== "admin") {
    const adminLink = document.querySelector('a[href="admin.html"]');
    adminLink.classList.add("disabled");
    adminLink.style.pointerEvents = "none";
    adminLink.style.opacity = "0.5";
  }

  renderProfile();
});
