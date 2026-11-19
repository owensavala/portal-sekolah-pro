/**
 * Portal Sekolah Pro - Client Script
 * Ultra-smooth interactive 3D Tilt, Dynamic Themes & Live Search
 */

document.addEventListener('DOMContentLoaded', () => {
  initClock();
  initSearch();
  initModals();
  initClickTracking();
  initKeyboardNav();
  init3DTilt();
});

function initClock() {
  const clockEl = document.getElementById('realtime-clock');
  if (!clockEl) return;

  function update() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    clockEl.innerText = `${hours}:${minutes}:${seconds} WIT`;
  }
  update();
  setInterval(update, 1000);
}

// 🌟 Interactive 3D Card Tilt Effect & Dynamic Spotlight Cursor
function init3DTilt() {
  const cards = document.querySelectorAll('.glass-card-dynamic, .shortcut-card-tile');
  if (!cards.length) return;

  cards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      
      const rotateX = ((y - centerY) / centerY) * -10;
      const rotateY = ((x - centerX) / centerX) * 10;
      
      card.style.setProperty('--mouse-x', `${x}px`);
      card.style.setProperty('--mouse-y', `${y}px`);
      card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px) scale3d(1.025, 1.025, 1.025)`;
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0) scale3d(1, 1, 1)';
    });
  });
}

function initSearch() {
  const searchInput = document.getElementById('main-search-input') || document.getElementById('simple-search-input');
  const searchResults = document.getElementById('search-dropdown-results');
  if (!searchInput || !searchResults) return;

  let debounceTimer;

  searchInput.addEventListener('input', (e) => {
    const query = e.target.value.trim();
    clearTimeout(debounceTimer);

    if (query.length < 1) {
      searchResults.classList.add('hidden');
      searchResults.innerHTML = '';
      return;
    }

    debounceTimer = setTimeout(() => {
      fetch(`api/search.php?q=${encodeURIComponent(query)}`)
        .then((r) => r.json())
        .then((data) => {
          renderSearchResults(data.results || [], query);
        })
        .catch((err) => console.error('Search error:', err));
    }, 200);
  });

  document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
      searchResults.classList.add('hidden');
    }
  });

  searchInput.addEventListener('focus', () => {
    if (searchInput.value.trim().length >= 1) {
      searchResults.classList.remove('hidden');
    }
  });
}

function renderSearchResults(results, query) {
  const searchResults = document.getElementById('search-dropdown-results');
  if (!searchResults) return;

  if (results.length === 0) {
    searchResults.innerHTML = `
      <div class="p-5 text-center text-slate-300">
        <i class="fa-solid fa-magnifying-glass mb-2 text-2xl text-slate-400"></i>
        <p class="text-sm font-semibold">Tidak ada aplikasi dengan kata kunci "<b>${escapeHtml(query)}</b>"</p>
        <p class="text-xs text-slate-400 mt-1">Coba cari dengan nama lain (contoh: EMIS, Raport, CBT, Dapodik).</p>
      </div>
    `;
    searchResults.classList.remove('hidden');
    return;
  }

  let html = `
    <div class="p-2.5 border-b border-white/10 text-xs font-bold text-slate-300 flex justify-between items-center bg-white/5">
      <span>HASIL PENCARIAN (${results.length})</span>
      <span class="text-[11px]" style="color: var(--secondary);">Klik untuk membuka tautan</span>
    </div>
    <div class="max-h-80 overflow-y-auto divide-y divide-white/5">
  `;

  results.forEach((item) => {
    html += `
      <a href="${item.url}" target="${item.target || '_blank'}" 
         data-id="${item.id}"
         class="search-result-item flex items-center justify-between p-3.5 hover:bg-white/10 transition group text-white">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm group-hover:scale-110 transition border"
               style="background: rgba(var(--secondary-rgb), 0.15); border-color: rgba(var(--secondary-rgb), 0.35); color: var(--secondary);">
            <i class="${item.icon || 'fa-solid fa-link'}"></i>
          </div>
          <div>
            <div class="font-bold text-sm transition flex items-center gap-2 group-hover:text-white" style="color: var(--secondary);">
              ${escapeHtml(item.title)}
              ${item.badge ? `<span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-white/15 text-slate-200 border border-white/20">${escapeHtml(item.badge)}</span>` : ''}
            </div>
            <div class="text-xs text-slate-300 line-clamp-1">${escapeHtml(item.description || item.category)}</div>
          </div>
        </div>
        <div class="text-xs flex items-center gap-1 group-hover:translate-x-1 transition font-bold" style="color: var(--secondary);">
          <span class="hidden sm:inline text-[11px]">Buka</span>
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </div>
      </a>
    `;
  });

  html += `</div>`;
  searchResults.innerHTML = html;
  searchResults.classList.remove('hidden');

  searchResults.querySelectorAll('.search-result-item').forEach((link) => {
    link.addEventListener('click', () => {
      const id = link.getAttribute('data-id');
      if (id) trackClick(id);
    });
  });
}

function initModals() {
  const modal = document.getElementById('category-modal');
  const modalBackdrop = document.getElementById('modal-backdrop');
  const modalClose = document.getElementById('modal-close-btn');
  const modalTitle = document.getElementById('modal-title');
  const modalSubtitle = document.getElementById('modal-subtitle');
  const modalBadge = document.getElementById('modal-badge');
  const modalIcon = document.getElementById('modal-icon');
  const modalList = document.getElementById('modal-shortcuts-list');
  const modalSearch = document.getElementById('modal-filter-input');

  if (!modal) return;

  let currentCategoryShortcuts = [];

  document.querySelectorAll('.category-card-btn').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const catId = btn.getAttribute('data-category-id');
      const catData = window.PORTAL_CATEGORIES ? window.PORTAL_CATEGORIES[catId] : null;
      const shortcuts = window.PORTAL_SHORTCUTS ? window.PORTAL_SHORTCUTS[catId] || [] : [];

      if (!catData) return;

      currentCategoryShortcuts = shortcuts;

      modalTitle.innerText = catData.name;
      modalSubtitle.innerText = catData.description || catData.subname || '';
      modalBadge.innerText = `${shortcuts.length} Aplikasi / Link`;

      if (catData.icon_type === 'image' && catData.icon_image) {
        modalIcon.innerHTML = `<img src="${catData.icon_image}" class="w-10 h-10 object-contain drop-shadow" alt="${catData.name}">`;
      } else {
        modalIcon.innerHTML = `<i class="fa-solid ${catData.fa_icon || 'fa-folder'} text-2xl" style="color: var(--secondary);"></i>`;
      }

      if (modalSearch) {
        modalSearch.value = '';
      }

      renderModalShortcuts(shortcuts);
      openModal();
    });
  });

  function openModal() {
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => {
      if (modalSearch) modalSearch.focus();
    }, 100);
  }

  function closeModal() {
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }

  if (modalClose) modalClose.addEventListener('click', closeModal);
  if (modalBackdrop) modalBackdrop.addEventListener('click', closeModal);

  if (modalSearch) {
    modalSearch.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      if (!q) {
        renderModalShortcuts(currentCategoryShortcuts);
        return;
      }
      const filtered = currentCategoryShortcuts.filter(
        (s) =>
          (s.title && s.title.toLowerCase().includes(q)) ||
          (s.description && s.description.toLowerCase().includes(q)) ||
          (s.badge && s.badge.toLowerCase().includes(q))
      );
      renderModalShortcuts(filtered, q);
    });
  }

  function renderModalShortcuts(items, query = '') {
    if (!modalList) return;

    if (items.length === 0) {
      modalList.innerHTML = `
        <div class="text-center py-10 text-slate-400 col-span-2">
          <i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-500"></i>
          <p class="font-semibold text-slate-300">Tidak ada tautan ditemukan${query ? ` untuk "${escapeHtml(query)}"` : ''}.</p>
          <p class="text-xs text-slate-500 mt-1">Admin dapat menambah tautan baru melalui Dashboard Admin.</p>
        </div>
      `;
      return;
    }

    let html = '';
    items.forEach((item, idx) => {
      html += `
        <a href="${item.url}" target="${item.target || '_blank'}"
           data-id="${item.id}"
           style="animation-delay: ${idx * 0.04}s;"
           class="modal-shortcut-card group flex items-start justify-between p-4 rounded-2xl bg-white/5 hover:bg-white/15 border border-white/10 hover:border-[var(--secondary)] transition-all duration-200 transform hover:-translate-y-1 hover:shadow-xl text-white">
          <div class="flex items-start space-x-3.5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg shrink-0 group-hover:scale-110 transition border"
                 style="background: rgba(var(--secondary-rgb), 0.15); border-color: rgba(var(--secondary-rgb), 0.35); color: var(--secondary);">
              <i class="${item.fa_icon || 'fa-solid fa-link'}"></i>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold text-sm text-white group-hover:text-[var(--secondary)] transition">
                  ${escapeHtml(item.title)}
                </span>
                ${item.badge ? `<span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: rgba(var(--secondary-rgb), 0.2); color: var(--secondary); border: 1px solid rgba(var(--secondary-rgb), 0.35);">${escapeHtml(item.badge)}</span>` : ''}
              </div>
              <p class="text-xs text-slate-300 mt-1 line-clamp-2 leading-relaxed">
                ${escapeHtml(item.description || 'Klik untuk membuka sistem atau aplikasi resmi ini')}
              </p>
            </div>
          </div>
          <div class="shrink-0 ml-3 mt-1">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition shadow group-hover:translate-x-1"
                  style="background: rgba(255, 255, 255, 0.1); color: var(--secondary);">
              <span>Buka</span>
              <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </span>
          </div>
        </a>
      `;
    });

    modalList.innerHTML = html;

    modalList.querySelectorAll('.modal-shortcut-card').forEach((card) => {
      card.addEventListener('click', () => {
        const id = card.getAttribute('data-id');
        if (id) trackClick(id);
      });
    });
  }

  window.closePortalModal = closeModal;
}

function initClickTracking() {
  document.querySelectorAll('a[data-track-id]').forEach((a) => {
    a.addEventListener('click', () => {
      const id = a.getAttribute('data-track-id');
      if (id) trackClick(id);
    });
  });
}

function trackClick(shortcutId) {
  try {
    navigator.sendBeacon ? 
      navigator.sendBeacon(`api/click.php?id=${encodeURIComponent(shortcutId)}`) :
      fetch(`api/click.php?id=${encodeURIComponent(shortcutId)}`, { mode: 'no-cors' });
  } catch (e) {
    // ignore
  }
}

function initKeyboardNav() {
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (window.closePortalModal) window.closePortalModal();
      const searchResults = document.getElementById('search-dropdown-results');
      if (searchResults) searchResults.classList.add('hidden');
    }

    if ((e.ctrlKey && e.key.toLowerCase() === 'k') || (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA')) {
      e.preventDefault();
      const searchInput = document.getElementById('simple-search-input') || document.getElementById('main-search-input');
      if (searchInput) {
        searchInput.focus();
        searchInput.select();
      }
    }
  });
}

function escapeHtml(text) {
  if (!text) return '';
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  };
  return String(text).replace(/[&<>"']/g, (m) => map[m]);
}
