// Empêche le navigateur de restaurer la position de scroll
if ('scrollRestoration' in history) {
  history.scrollRestoration = 'manual';
}
window.scrollTo(0, 0);

// Ouvre une modale projet
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

// Curseur personnalisé
(function () {
  const dot  = document.getElementById('cursor-dot');
  const ring = document.getElementById('cursor-ring');
  if (!dot || !ring) return;

  let ringX = 0, ringY = 0;
  let dotX  = 0, dotY  = 0;
  document.addEventListener('mousemove', function (e) {
    dotX  = e.clientX;
    dotY  = e.clientY;
  });

  function animateCursor() {
    // Dot suit la souris directement
    dot.style.left = dotX + 'px';
    dot.style.top  = dotY + 'px';

    // Ring suit avec un léger lag
    ringX += (dotX - ringX) * 0.15;
    ringY += (dotY - ringY) * 0.15;
    ring.style.left = ringX + 'px';
    ring.style.top  = ringY + 'px';

    requestAnimationFrame(animateCursor);
  }
  animateCursor();

  // Agrandir le ring au survol des éléments interactifs
  const hoverTargets = 'a, button, [onclick], .project-card, .skill-card, .nav-link';
  document.addEventListener('mouseover', function (e) {
    if (e.target.closest(hoverTargets)) {
      document.body.classList.add('cursor-hover');
    }
  });
  document.addEventListener('mouseout', function (e) {
    if (e.target.closest(hoverTargets)) {
      document.body.classList.remove('cursor-hover');
    }
  });
})();

document.addEventListener('DOMContentLoaded', function () {

  // 1) Splash – bouton START
  const splash   = document.getElementById('splash-screen');
  const startBtn = document.getElementById('start-btn');

  function handleStart() {
    if (!splash) return;
    splash.classList.add('hidden-splash');
    document.body.classList.add('site-revealed');
    window.scrollTo({ top: 0, behavior: 'instant' });

    const delayedElements = document.querySelectorAll('.delayed-entry');
    setTimeout(() => {
      delayedElements.forEach((el) => el.classList.add('show'));

      // Typewriter sur le titre h1
      const heroH1 = document.getElementById('hero-title');
      if (heroH1) {
        const text = heroH1.textContent;
        heroH1.textContent = '';
        let i = 0;
        const typeInterval = setInterval(() => {
          heroH1.textContent += text[i++];
          if (i >= text.length) clearInterval(typeInterval);
        }, 65);
      }

      // Stagger + compteurs
      initStagger();
      initCounters();
    }, 1200);
  }

  startBtn?.addEventListener('click', handleStart);

  // 2) Navigation pill – marker animé
  const nav    = document.querySelector('nav.github-pill');
  const marker = nav?.querySelector('.nav-marker') || null;
  const links  = Array.from(nav?.querySelectorAll('a.nav-link') || []);

  function moveMarker(el) {
    if (!el || !marker || !nav) return;
    marker.style.opacity   = '1';
    marker.style.width     = el.offsetWidth + 'px';
    marker.style.left      = el.offsetLeft + 'px';
    marker.style.transform = 'translateX(0)';
  }

  // 3) IntersectionObserver pour la nav active
  const sections = document.querySelectorAll('section');
  let isManualScroll = false;
  let scrollTimeout;

  const observer = new IntersectionObserver(
    (entries) => {
      if (isManualScroll) return;
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const id = entry.target.getAttribute('id');
        if (!id) return;
        const activeLink = document.querySelector('.nav-link[href="#' + id + '"]');
        if (!activeLink) return;
        links.forEach((l) => l.classList.remove('active'));
        activeLink.classList.add('active');
        moveMarker(activeLink);
      });
    },
    { root: null, rootMargin: '-50% 0px -50% 0px', threshold: 0 }
  );

  sections.forEach((section) => observer.observe(section));

  // 4) Scroll fluide + blocage temporaire de l'auto-update
  links.forEach((link) => {
    link.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (!href?.startsWith('#')) return;
      e.preventDefault();

      const id      = href.slice(1);
      const section = document.getElementById(id);
      if (!section) return;

      isManualScroll = true;
      clearTimeout(scrollTimeout);

      links.forEach((l) => l.classList.remove('active'));
      this.classList.add('active');
      moveMarker(this);

      section.scrollIntoView({ behavior: 'smooth', block: 'start' });

      scrollTimeout = setTimeout(() => { isManualScroll = false; }, 1000);
    });
  });

  // Positionner le marker au chargement
  const initialActive = document.querySelector('.nav-link.active');
  if (initialActive) setTimeout(() => moveMarker(initialActive), 100);

  // 5) Modales – fermeture
  const overlays      = Array.from(document.querySelectorAll('.modal-overlay'));
  const closeButtons  = Array.from(document.querySelectorAll('.close-modal'));

  function closeModal() {
    overlays.forEach((m) => m.classList.remove('active'));
    document.body.style.overflow = '';
  }

  closeButtons.forEach((btn) => btn.addEventListener('click', closeModal));
  overlays.forEach((overlay) => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal();
    });
  });

  // 6) Scroll progress bar + scroll-to-top
  const progressBar = document.getElementById('scroll-progress');
  const scrollTopBtn = document.getElementById('scroll-top-btn');

  window.addEventListener('scroll', function () {
    if (progressBar) {
      const scrolled = window.scrollY;
      const total = document.documentElement.scrollHeight - window.innerHeight;
      progressBar.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
    }
    if (scrollTopBtn) {
      scrollTopBtn.classList.toggle('visible', window.scrollY > 300);
    }
  });

  scrollTopBtn?.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // 7) Tilt 3D sur les cartes
  function addTilt(selector, maxTilt, extraTransform) {
    document.querySelectorAll(selector).forEach(function (card) {
      card.addEventListener('mousemove', function (e) {
        const rect = card.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width  - 0.5) * maxTilt;
        const y = ((e.clientY - rect.top)  / rect.height - 0.5) * maxTilt;
        card.style.transform = 'perspective(700px) rotateY(' + x + 'deg) rotateX(' + (-y) + 'deg)' + extraTransform;
      });
      card.addEventListener('mouseleave', function () {
        card.style.transform = '';
      });
    });
  }
  addTilt('.skill-card',    12, ' translateY(-10px)');
  addTilt('.bubble-project', 9, ' translateY(-8px) scale(1.02)');

  // 8) Stagger d'apparition des cartes
  function initStagger() {
    const containers = document.querySelectorAll('.skills-grid, .bubbles-grid, .custom-grid');
    containers.forEach(function (container) {
      const children = Array.from(container.children);
      children.forEach(function (child) { child.classList.add('stagger-child'); });
      const obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          children.forEach(function (child, i) {
            setTimeout(function () { child.classList.add('visible'); }, i * 120);
          });
          obs.unobserve(container);
        });
      }, { threshold: 0.1 });
      obs.observe(container);
    });
  }

  // 9) Compteurs animés
  function initCounters() {
    const stats = document.querySelectorAll('.stat-number[data-count]');
    if (!stats.length) return;
    const counterObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = parseInt(el.dataset.count, 10);
        const duration = 1000;
        const interval = Math.max(duration / target, 50);
        let current = 0;
        const timer = setInterval(function () {
          current++;
          el.textContent = current;
          if (current >= target) clearInterval(timer);
        }, interval);
        counterObs.unobserve(el);
      });
    }, { threshold: 0.5 });
    stats.forEach(function (el) { counterObs.observe(el); });
  }

});
