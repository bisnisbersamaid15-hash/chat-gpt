const config = {
  brandName: "Aurora Arcade",
  logoPath: "assets/images/logo.svg",
  accentColor: "#d6b15a",
  heroTitle: "TOP 10 GAME TERFAVORIT",
  heroSubtitle: "Dari Pragmatic Play, PG Soft, Habanero, dan Microgaming",
};

const allowedProviders = ["Pragmatic Play", "PG Soft", "Habanero", "Microgaming"];

const state = {
  items: [],
  provider: "All Top 10",
};

const elements = {
  brandLogo: document.getElementById("brandLogo"),
  brandName: document.getElementById("brandName"),
  heroTitle: document.getElementById("heroTitle"),
  heroSubtitle: document.getElementById("heroSubtitle"),
  providerTabs: document.getElementById("providerTabs"),
  catalogGrid: document.getElementById("catalogGrid"),
  detailModal: document.getElementById("detailModal"),
  modalTitle: document.getElementById("modalTitle"),
  modalInfo: document.getElementById("modalInfo"),
  modalClose: document.getElementById("modalClose"),
  modalAction: document.getElementById("modalAction"),
};

const applyBranding = () => {
  document.documentElement.style.setProperty("--accent", config.accentColor);
  elements.brandLogo.src = config.logoPath;
  elements.brandName.textContent = config.brandName;
  elements.heroTitle.textContent = config.heroTitle;
  elements.heroSubtitle.textContent = config.heroSubtitle;
};

const progressColor = (percent) => {
  if (percent >= 80) return "var(--success)";
  if (percent >= 60) return "var(--warning)";
  return "var(--danger)";
};

const getTopItems = (provider) => {
  const pool = state.items.filter((item) => allowedProviders.includes(item.provider));
  const filtered = provider === "All Top 10" ? pool : pool.filter((item) => item.provider === provider);
  return filtered
    .slice()
    .sort((a, b) => b.favoriteScore - a.favoriteScore)
    .slice(0, 10);
};

const renderTabs = () => {
  const allTabs = ["All Top 10", ...allowedProviders];
  elements.providerTabs.innerHTML = allTabs
    .map(
      (provider) => `
      <button class="tab ${provider === state.provider ? "active" : ""}" data-provider="${provider}">
        ${provider}
      </button>
    `
    )
    .join("");
};

const renderGrid = () => {
  const topItems = getTopItems(state.provider);
  elements.catalogGrid.innerHTML = topItems
    .map(
      (item, index) => `
      <article class="card">
        <span class="card__badge">Favorit #${index + 1}</span>
        <div class="card__thumb">
          <img src="${item.image}" alt="${item.title}" loading="lazy" />
        </div>
        <div>
          <div class="card__title">${item.title}</div>
          <div class="card__provider">${item.provider}</div>
        </div>
        <div>
          <div class="progress__label">
            <span>Indikator</span>
            <span>${item.percent}%</span>
          </div>
          <div class="progress">
            <div class="progress__fill" style="width: ${item.percent}%; background: ${progressColor(
              item.percent
            )};"></div>
          </div>
        </div>
        <button class="card__action" type="button" data-detail="${item.id}">
          Detail
        </button>
      </article>
    `
    )
    .join("");
};

const openModal = (item) => {
  elements.modalTitle.textContent = item.title;
  elements.modalInfo.textContent = `Provider: ${item.provider} · Favorit: ${item.favoriteScore} · Indikator: ${item.percent}%`;
  elements.detailModal.classList.add("open");
  elements.detailModal.setAttribute("aria-hidden", "false");
};

const closeModal = () => {
  elements.detailModal.classList.remove("open");
  elements.detailModal.setAttribute("aria-hidden", "true");
};

const bindEvents = () => {
  elements.providerTabs.addEventListener("click", (event) => {
    const button = event.target.closest(".tab");
    if (!button) return;
    state.provider = button.dataset.provider;
    renderTabs();
    renderGrid();
  });

  elements.catalogGrid.addEventListener("click", (event) => {
    const action = event.target.closest(".card__action");
    if (!action) return;
    const item = state.items.find((entry) => entry.id === action.dataset.detail);
    if (item) openModal(item);
  });

  elements.modalClose.addEventListener("click", closeModal);
  elements.modalAction.addEventListener("click", closeModal);
  elements.detailModal.addEventListener("click", (event) => {
    if (event.target === elements.detailModal) closeModal();
  });
};

const init = async () => {
  applyBranding();
  bindEvents();

  try {
    const response = await fetch("data/catalog.json");
    if (!response.ok) throw new Error("Failed to load catalog");
    state.items = await response.json();
  } catch (error) {
    state.items = [];
    elements.catalogGrid.innerHTML =
      "<p class=\"hero__subtitle\">Data belum tersedia. Jalankan via Live Server.</p>";
  }

  renderTabs();
  renderGrid();
};

init();
