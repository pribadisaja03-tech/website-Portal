// ===============================
// Portal Cerita Rakyat Lombok
// Script interaktif & responsif
// ===============================

// --- Search + Filter ---
const searchInput = document.getElementById("search");
const filterSelect = document.getElementById("filterSelect");
const cards = document.querySelectorAll(".card");

function applyFilter() {
  const searchText = searchInput ? searchInput.value.toLowerCase().trim() : "";
  const daerah = filterSelect ? filterSelect.value : "all";

  cards.forEach(card => {
    let titleElem = card.querySelector("h3");
    let title = titleElem ? titleElem.textContent.toLowerCase() : "";
    let daerahCard = card.dataset.daerah ? card.dataset.daerah : "";

    const cocokCari = !searchText || title.includes(searchText);
    const cocokDaerah = (daerah === "all" || daerahCard === daerah);

    card.style.display = (cocokCari && cocokDaerah) ? "block" : "none";
  });
}

if (searchInput) searchInput.addEventListener("input", applyFilter);
if (filterSelect) filterSelect.addEventListener("change", applyFilter);

// --- Tombol Filter Kategori ---
const filterButtons = document.querySelectorAll(".btn-filter");

filterButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    filterButtons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    const daerah = btn.dataset.daerah;
    const searchText = searchInput ? searchInput.value.toLowerCase().trim() : "";

    cards.forEach(card => {
      let titleElem = card.querySelector("h3");
      let title = titleElem ? titleElem.textContent.toLowerCase() : "";
      let daerahCard = card.dataset.daerah ? card.dataset.daerah : "";

      const cocokCari = !searchText || title.includes(searchText);
      const cocokDaerah = (daerah === "all" || daerahCard === daerah);

      card.style.display = (cocokCari && cocokDaerah) ? "block" : "none";
    });
  });
});

// --- Favorit (localStorage) ---
const favoriteButtons = document.querySelectorAll(".btn-favorite");
let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");

favoriteButtons.forEach(btn => {
  const id = btn.dataset.id;
  if (favorites.includes(id)) btn.classList.add("favorited");

  btn.addEventListener("click", () => {
    if (favorites.includes(id)) {
      favorites.splice(favorites.indexOf(id), 1);
      btn.classList.remove("favorited");
    } else {
      favorites.push(id);
      btn.classList.add("favorited");
    }
    localStorage.setItem("favorites", JSON.stringify(favorites));
  });
});

// --- Back to Top button ---
let backToTop = document.getElementById("backToTop");
if (!backToTop) {
  backToTop = document.createElement("button");
  backToTop.id = "backToTop";
  backToTop.textContent = "↑";
  backToTop.setAttribute("aria-label", "Kembali ke atas");
  backToTop.style.position = "fixed";
  backToTop.style.right = "20px";
  backToTop.style.bottom = "20px";
  backToTop.style.padding = "10px 12px";
  backToTop.style.borderRadius = "8px";
  backToTop.style.border = "none";
  backToTop.style.cursor = "pointer";
  backToTop.style.display = "none";
  backToTop.style.background = "#0b6b62";
  backToTop.style.color = "#fff";
  backToTop.style.transition = "opacity 0.4s";
  backToTop.style.opacity = "0";
  document.body.appendChild(backToTop);
}
window.addEventListener("scroll", () => {
  if (window.scrollY > 250) {
    backToTop.style.display = "block";
    setTimeout(() => (backToTop.style.opacity = "1"), 10);
  } else {
    backToTop.style.opacity = "0";
    setTimeout(() => (backToTop.style.display = "none"), 400);
  }
});
backToTop.addEventListener("click", () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// --- Sticky navbar shadow ---
const navbar = document.querySelector(".header-top");
window.addEventListener("scroll", () => {
  if (!navbar) return;
  if (window.scrollY > 8) navbar.classList.add("sticky");
  else navbar.classList.remove("sticky");
});

// --- Scroll reveal untuk fade-in ---
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) entry.target.classList.add("show");
  });
}, { threshold: 0.12 });

// Semua elemen dengan class fade-in
document.querySelectorAll(".fade-in").forEach(el => {
  observer.observe(el);
});


// --- Simpan posisi scroll (UX lebih baik) ---
window.addEventListener("beforeunload", () => {
  localStorage.setItem("scrollPos", window.scrollY);
});
window.addEventListener("load", () => {
  const pos = localStorage.getItem("scrollPos");
  if (pos) window.scrollTo(0, parseInt(pos));
});

// --- Navbar Mobile (Hamburger Menu) ---
const menuToggle = document.getElementById("menuToggle");
const navMenu = document.getElementById("navMenu");

if (menuToggle && navMenu) {
  menuToggle.addEventListener("click", () => {
    navMenu.classList.toggle("open");
    menuToggle.classList.toggle("active");
  });
}

/// --- Peta Interaktif ---
document.addEventListener("DOMContentLoaded", function () {
  const map = L.map("map").setView([-8.65, 116.28], 9); // posisi tengah Lombok

  // --- Smooth fade-in saat halaman dibuka ---
document.addEventListener("DOMContentLoaded", () => {
  const elements = document.querySelectorAll(".fade-in");
  elements.forEach((el, i) => {
    setTimeout(() => {
      el.classList.add("show");
    }, i * 200); // delay 200ms biar muncul berurutan
  });
});


  // Tile layer
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
  }).addTo(map);

  // --- Looping cerita dari PHP ---
  if (typeof ceritaData !== "undefined" && ceritaData.length > 0) {
    ceritaData.forEach(cerita => {
      if (cerita.lat && cerita.lng) {
        L.marker([cerita.lat, cerita.lng])
          .addTo(map)
          .bindPopup(`<b>${cerita.judul}</b><br>Asal: ${cerita.daerah}`);
      }
    });
  }
});




