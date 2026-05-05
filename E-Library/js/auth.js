// Toggle Login / Register
function toggleForms() {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const title = document.getElementById("formTitle");

  if (loginForm.classList.contains("d-none")) {
    registerForm.classList.add("d-none");
    loginForm.classList.remove("d-none");
    title.innerText = "Login";
  } else {
    loginForm.classList.add("d-none");
    registerForm.classList.remove("d-none");
    title.innerText = "Registrasi";
  }
}

// ======== Register ========
document.getElementById("registerForm")?.addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("regName").value.trim();
  const email = document.getElementById("regEmail").value.trim();
  const password = document.getElementById("regPassword").value.trim();

  if (!name || !email || !password) {
    alert("Harap isi semua kolom!");
    return;
  }

  let users = JSON.parse(localStorage.getItem("users")) || [];

  if (users.find((u) => u.email === email)) {
    alert("Email sudah terdaftar!");
    return;
  }

  alert("Registrasi berhasil! Silakan login.");
  toggleForms();
});

// ======== Login ========
document.getElementById("loginForm")?.addEventListener("submit", function (e) {
  e.preventDefault();

  const email = document.getElementById("loginEmail").value.trim();
  const password = document.getElementById("loginPassword").value.trim();
  const users = JSON.parse(localStorage.getItem("users")) || [];

  const user = users.find((u) => u.email === email && u.password === password);
  if (!user) {
    alert("Email atau password salah!");
    return;
  }

  localStorage.setItem("loggedInUser", JSON.stringify(user));
  alert("Login berhasil!");

  if (user.role === "admin") {
    window.location.href = "admin.html";
  } else {
    window.location.href = "index.html";
  }
});

// ======== Logout (opsional di halaman profile) ========
function logout() {
  localStorage.removeItem("loggedInUser");
  alert("Anda telah keluar.");
  window.location.href = "auth.html";
}
