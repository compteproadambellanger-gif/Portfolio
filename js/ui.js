// Ouvre une modale projet
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

document.addEventListener('DOMContentLoaded', function () {

  // 1) Splash – bouton START
  const splash   = document.getElementById('splash-screen');
  const startBtn = document.getElementById('start-btn');

  function handleStart() {
    if (!splash) return;
    splash.classList.add('hidden-splash');
    document.body.classList.add('site-revealed');

    const delayedElements = document.querySelectorAll('.delayed-entry');
    setTimeout(() => {
      delayedElements.forEach((el) => el.classList.add('show'));
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
    { root: null, rootMargin: '-40px 0px -40px 0px', threshold: 0 }
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

});
