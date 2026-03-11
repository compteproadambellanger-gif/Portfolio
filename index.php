<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Adam Bellanger – Portfolio</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <!-- SCROLL PROGRESS BAR -->
  <div id="scroll-progress"></div>

  <!-- CURSEUR PERSONNALISÉ -->
  <div class="cursor-dot" id="cursor-dot"></div>
  <div class="cursor-ring" id="cursor-ring"></div>

  <!-- SCÈNE 3D (blobs + étoiles) -->
  <div class="three-container"></div>

  <!-- SPLASH SCREEN -->
  <div id="splash-screen">
    <div class="splash-content">
      <button id="start-btn">START</button>
    </div>
  </div>

  <!-- MODALES -->
  <div id="modal-portfolio" class="modal-overlay">
    <div class="modal-content">
      <button class="close-modal">&times;</button>
      <div class="modal-body">
        <h2>Portfolio 3D</h2>
        <div class="modal-tags">
          <span class="modal-tag">Three.js</span>
          <span class="modal-tag">WebGL</span>
          <span class="modal-tag">HTML/CSS</span>
        </div>
        <p>
          Ce portfolio est ma première réalisation majeure en BTS SIO.
          L'objectif était de créer une expérience immersive qui se démarque
          des portfolios classiques.
        </p>
        <p>
          J'ai utilisé la librairie <strong>Three.js</strong> pour générer des formes organiques
          ("blobs") animées par des shaders GLSL personnalisés. Le site intègre un système
          de navigation fluide, un design "Glassmorphism" et des animations CSS avancées.
        </p>
        <a
          href="https://github.com/compteproadambellanger-gif/Portfolio"
          target="_blank"
          rel="noreferrer"
          class="btn-modal"
        >
          Voir le code sur GitHub
        </a>
      </div>
    </div>
  </div>

  <div id="modal-infra" class="modal-overlay">
    <div class="modal-content">
      <button class="close-modal">&times;</button>
      <div class="modal-body">
        <h2>Architecture Réseau Cisco</h2>
        <div class="modal-tags">
          <span class="modal-tag">Cisco Packet Tracer</span>
          <span class="modal-tag">VLANs</span>
          <span class="modal-tag">Routage Inter-VLAN</span>
        </div>
        <p>
          Projet de conception d'une infrastructure réseau pour une PME de 300 employés répartis sur 3 étages.
        </p>
        <p>
          <strong>Réalisations :</strong><br>
          - Segmentation du réseau en 5 VLANs (Direction, RH, Tech, Invités, Serveurs).<br>
          - Configuration du protocole VTP et du routage "Router-on-a-stick".<br>
          - Mise en place de règles ACL pour sécuriser l'accès aux serveurs critiques.
        </p>
        <p><em>Documentation technique disponible prochainement.</em></p>
      </div>
    </div>
  </div>

  <div id="modal-univers" class="modal-overlay">
    <div class="modal-content">
      <button class="close-modal">&times;</button>
      <div class="modal-body">
        <h2>Manchester City Universe</h2>
        <div class="modal-tags">
          <span class="modal-tag">PHP</span>
          <span class="modal-tag">MySQL</span>
          <span class="modal-tag">Chart.js</span>
          <span class="modal-tag">OAuth Google</span>
        </div>
        <p>
          Application web complète de gestion du club Manchester City développée
          dans le cadre du module Développement Web Backend. Le projet repose sur une architecture PHP/MySQL avec
          un système d'authentification multi-rôles.
        </p>
        <p>
          <strong>3 rôles distincts :</strong><br>
          – <span style="color:#00d4ff;font-weight:600;">Staff</span> — CRUD complet joueurs &amp; matchs, saisie de stats, dashboard avec graphiques Chart.js, gestion des utilisateurs.<br>
          – <span style="color:#a3e635;font-weight:600;">Joueur</span> — Dashboard personnalisé avec ses propres statistiques match par match (buts, passes, notes…).<br>
          – <span style="color:#fb923c;font-weight:600;">Supporter</span> — Zone fan : dernier match, forme récente, top buteur/passeur, intégration vidéo YouTube.
        </p>
        <p>
          <strong>Fonctionnalités clés :</strong>
          recherche temps réel + pagination, upload photo de profil/joueur,
          fiche individuelle avec graphique, mode sombre/clair, connexion Google OAuth.
        </p>
        <a
          href="https://github.com/compteproadambellanger-gif/ProjetUniversManCity"
          target="_blank"
          rel="noreferrer"
          class="btn-modal"
        >
          Voir sur GitHub
        </a>
      </div>
    </div>
  </div>

  <div id="modal-scripts" class="modal-overlay">
    <div class="modal-content">
      <button class="close-modal">&times;</button>
      <div class="modal-body">
        <h2>Scripts d'Automatisation</h2>
        <div class="modal-tags">
          <span class="modal-tag">Bash</span>
          <span class="modal-tag">Python</span>
          <span class="modal-tag">Linux</span>
        </div>
        <p>
          Ensemble de scripts développés pour automatiser la maintenance des serveurs Linux (Debian/Ubuntu).
        </p>
        <ul>
          <li>Script de sauvegarde automatique des bases de données SQL vers un serveur distant.</li>
          <li>Script de surveillance des logs système avec alerte email en cas d'intrusion.</li>
          <li>Outil de déploiement rapide d'environnement LAMP.</li>
        </ul>
        <a href="#" class="btn-modal">Voir le dépôt Git</a>
      </div>
    </div>
  </div>

  <!-- STATS SIDEBAR -->
  <aside id="stats-sidebar" class="delayed-entry">
    <div class="sidebar-stat">
      <div class="stat-number" data-count="4">0</div>
      <div class="stat-label">Expériences</div>
    </div>
    <div class="sidebar-divider"></div>
    <div class="sidebar-stat">
      <div class="stat-number" data-count="4">0</div>
      <div class="stat-label">Projets</div>
    </div>
    <div class="sidebar-divider"></div>
    <div class="sidebar-stat">
      <div class="stat-number" data-count="2">0</div>
      <div class="stat-label">Ans de<br>formation</div>
    </div>
  </aside>

  <!-- NAVIGATION -->
  <nav class="github-pill delayed-entry">
    <div class="nav-marker"></div>
    <a href="#accueil" class="nav-link active">Accueil</a>
    <a href="#apropos" class="nav-link">À propos</a>
    <a href="#parcours" class="nav-link">Mon Parcours</a>
    <a href="#competences-1" class="nav-link">Logiciel &amp; Outils</a>
    <a href="#projets" class="nav-link">Projets</a>
    <a href="#competences" class="nav-link">Compétences</a>
    <div class="nav-icons">
      <a href="https://ecoleiris.fr/campus/rouen" target="_blank" rel="noreferrer" title="École IRIS">
        <i class="fas fa-graduation-cap"></i>
      </a>
      <a href="https://www.linkedin.com/in/adam-bellanger-652919386/" target="_blank" rel="noreferrer" title="LinkedIn">
        <i class="fab fa-linkedin"></i>
      </a>
      <a href="https://github.com/compteproadambellanger-gif" target="_blank" rel="noreferrer" title="GitHub">
        <i class="fab fa-github"></i>
      </a>
    </div>
  </nav>

  <!-- ACCUEIL -->
  <section id="accueil" class="hero delayed-entry">
    <div id="hero-content">
      <h1 id="hero-title">Adam Bellanger</h1>
      <h3>PORTFOLIO</h3>
      <div class="hero-badge">
        Étudiant <strong>BTS SIO</strong> | Infrastructure &amp; Réseau
      </div>
      <div class="hero-location">
        <i class="fas fa-map-marker-alt"></i> Rouen, Normandie
      </div>
    </div>
  </section>

  <!-- À PROPOS -->
  <section id="apropos" class="delayed-entry">
    <div class="container">
      <div class="content-box">
        <h2>À propos</h2>
        <p>
          Actuellement étudiant en première année de BTS SIO (option SISR) en alternance au campus IRIS Rouen,
          je me spécialise dans le déploiement et le maintien en condition opérationnelle d'infrastructures réseaux,
          la virtualisation et la téléphonie d'entreprise. Passionné par l'écosystème des systèmes et réseaux,
          je consolide quotidiennement mes compétences techniques en administration d'environnements hétérogènes
          (Windows Server, Linux) ainsi qu'en architecture Cisco et solutions télécom professionnelles.
        </p>
        <p>
          Mon rythme en alternance est un véritable atout qui me permet de confronter la théorie à la réalité du terrain,
          développant ainsi mon autonomie et ma réactivité face aux incidents techniques. Toujours en veille technologique
          active, je m'attache non seulement à assurer la disponibilité des services, mais aussi à intégrer les
          meilleures pratiques de sécurité et d'optimisation des performances au sein des systèmes d'information
          que j'administre.
        </p>
      </div>
    </div>
  </section>

  <!-- PARCOURS -->
  <section id="parcours" class="delayed-entry">
    <div class="parcours-container">
      <div class="titre-parcours">
        <h1 style="font-size: 3.5rem; color: #00d4ff; line-height: 1; margin: 0;">
          Mon<br>Parcours
        </h1>
      </div>

      <div class="glass-bubble">

        <!-- FORMATION -->
        <div>
          <h3 style="color: #00d4ff; font-size: 1.5rem; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
            <i class="fas fa-graduation-cap"></i> FORMATION
          </h3>

          <div style="margin-bottom: 25px; padding-left: 20px; border-left: 3px solid #00d4ff;">
            <strong style="font-size: 1.1rem;">IRIS – École supérieure d'informatique</strong><br>
            <span style="color: rgba(255,255,255,0.9);">BTS SIO, Services Informatiques aux Organisations</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">2025 – 2027</span>
          </div>

          <div style="margin-bottom: 25px; padding-left: 20px; border-left: 3px solid #00d4ff;">
            <strong style="font-size: 1.1rem;">Campus La Chataigneraie</strong><br>
            <span style="color: rgba(255,255,255,0.9);">Baccalauréat professionnel, système numérique</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Sept. 2022 – Juil. 2025</span><br>
            <span style="color: #ffd700; font-size: 0.9rem;"><i class="fas fa-star"></i> Mention Bien</span>
          </div>

          <div style="margin-bottom: 10px; padding-left: 20px; border-left: 3px solid #00d4ff;">
            <strong style="font-size: 1.1rem;">Collège Saint Victrice</strong><br>
            <span style="color: rgba(255,255,255,0.9);">Diplôme National du Brevet</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Sept. 2017 – Juil. 2022</span><br>
            <span style="color: #ffd700; font-size: 0.9rem;"><i class="fas fa-star"></i> Mention Bien</span>
          </div>
        </div>

        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 40px 0;">

        <!-- EXPÉRIENCE -->
        <div>
          <h3 style="color: #ff6b35; font-size: 1.5rem; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
            <i class="fas fa-briefcase"></i> EXPÉRIENCE PROFESSIONNELLE
          </h3>

          <div style="margin-bottom: 25px; padding-left: 20px; border-left: 3px solid #ff6b35;">
            <strong style="font-size: 1.1rem; color: #fff;">Apprenti – Contrat en alternance</strong><br>
            <span style="color: #00d4ff; font-weight: 600;">Socacom</span>
            – <span style="font-style: italic;">Entreprise de télécommunication en Normandie</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Sept. 2025 – En cours · 6 mois</span><br>
            <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">Rouen, Normandie, France</span>
          </div>

          <div style="margin-bottom: 25px; padding-left: 20px; border-left: 3px solid rgba(255,255,255,0.3);">
            <strong style="font-size: 1.1rem;">Stagiaire</strong><br>
            <span style="color: rgba(255,255,255,0.9);">Socacom – Stage</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Janv. 2025 – Févr. 2025 · 2 mois</span><br>
            <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">Rouen, Normandie, France</span>
          </div>

          <div style="margin-bottom: 25px; padding-left: 20px; border-left: 3px solid rgba(255,255,255,0.3);">
            <strong style="font-size: 1.1rem;">Stagiaire</strong><br>
            <span style="color: rgba(255,255,255,0.9);">Socacom – Stage</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Sept. 2024 – Oct. 2024 · 2 mois</span><br>
            <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">Rouen, Normandie, France</span>
          </div>

          <div style="margin-bottom: 25px; padding-left: 20px; border-left: 3px solid rgba(255,255,255,0.3);">
            <strong style="font-size: 1.1rem;">Stagiaire</strong><br>
            <span style="color: rgba(255,255,255,0.9);">AJ PHONE – Services de téléphonie d'entreprise</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Juin 2024 – Juil. 2024 · 2 mois</span><br>
            <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">Rouen, Normandie, France</span>
          </div>

          <div style="padding-left: 20px; border-left: 3px solid rgba(255,255,255,0.3);">
            <strong style="font-size: 1.1rem;">Stagiaire</strong><br>
            <span style="color: rgba(255,255,255,0.9);">Socacom – Stage</span><br>
            <span style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Nov. 2023 – Déc. 2023 · 2 mois</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- LOGICIELS & OUTILS -->
  <section id="competences-1" class="delayed-entry">
    <div class="glass-bubble-container">
      <div style="text-align: center;">
        <h2 class="title-dev">Compétences Développement</h2>
      </div>
      <div class="custom-grid">
        <div class="grid-item">
          <i class="fab fa-html5" style="color: #e34c26;"></i>
          <span>HTML</span>
        </div>
        <div class="grid-item">
          <i class="fab fa-css3-alt" style="color: #264de4;"></i>
          <span>CSS</span>
        </div>
        <div class="grid-item">
          <i class="fab fa-js" style="color: #f7df1e;"></i>
          <span>JavaScript</span>
        </div>
      </div>
      <div class="divider"></div>
      <div style="text-align: center;">
        <h2 class="title-tools">Mes outils / Logiciel</h2>
      </div>
      <div class="custom-grid">
        <div class="grid-item">
          <i class="fas fa-image" style="color: #31a8ff;"></i>
          <span>Photoshop</span>
        </div>
        <div class="grid-item">
          <i class="fas fa-cube" style="color: #e87d0d;"></i>
          <span>Blender</span>
        </div>
        <div class="grid-item">
          <i class="fas fa-code" style="color: #007acc;"></i>
          <span>Visual Studio</span>
        </div>
        <div class="grid-item">
          <i class="fas fa-moon" style="color: #2c2255;"></i>
          <span>Eclipse</span>
        </div>
        <div class="grid-item">
          <i class="fab fa-github" style="color: #ffffff;"></i>
          <span>GitHub</span>
        </div>
      </div>
    </div>
  </section>

  <!-- PROJETS -->
  <section id="projets" class="delayed-entry">
    <div class="glass-bubble-container" style="max-width: 1400px; width: 100%;">
      <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="color: #00d4ff;">Mes Projets</h2>
        <p style="color: #ccc; max-width: 600px; margin: 0 auto;">
          Cliquez sur un projet pour voir les détails.
        </p>
      </div>

      <div class="bubbles-grid">
        <div class="bubble-project" onclick="openModal('modal-portfolio')">
          <div class="bubble-content">
            <i class="fas fa-globe project-icon-main"></i>
            <h4 class="project-title">Portfolio 3D</h4>
            <span class="project-tech">Three.js / WebGL</span>
          </div>
          <div class="bubble-glow"></div>
        </div>

        <div class="bubble-project" onclick="openModal('modal-infra')">
          <div class="bubble-content">
            <i class="fas fa-network-wired project-icon-main"></i>
            <h4 class="project-title">Infra Cisco</h4>
            <span class="project-tech">VLAN / Routing</span>
          </div>
          <div class="bubble-glow" style="background: rgba(255, 107, 53, 0.4);"></div>
        </div>

        <div class="bubble-project" onclick="openModal('modal-scripts')">
          <div class="bubble-content">
            <i class="fas fa-terminal project-icon-main"></i>
            <h4 class="project-title">Scripts Sys</h4>
            <span class="project-tech">Bash / Python</span>
          </div>
          <div class="bubble-glow" style="background: rgba(46, 204, 113, 0.4);"></div>
        </div>

        <div class="bubble-project" onclick="openModal('modal-univers')">
          <div class="bubble-content">
            <i class="fas fa-futbol project-icon-main"></i>
            <h4 class="project-title">ProjetUnivers ManCity</h4>
            <span class="project-tech">PHP / MySQL / Chart.js</span>
          </div>
          <div class="bubble-glow" style="background: rgba(108, 171, 221, 0.4);"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- COMPÉTENCES -->
  <section id="competences" class="delayed-entry">
    <div class="container">
      <div class="content-box">
        <h2>Compétences Techniques</h2>
        <div class="skills-grid">
          <div class="skill-card">
            <h3>🌐 Réseaux &amp; Infra</h3>
            <ul>
              <li>Cisco et Huawei</li>
              <li>Architecture &amp; VLANs</li>
              <li>Solutions opérateurs</li>
            </ul>
          </div>
          <div class="skill-card">
            <h3>📞 Téléphonie</h3>
            <ul>
              <li>Alcatel (OXO, IP)</li>
              <li>VoIP / ToIP / SIP</li>
              <li>Centrex et UnyCX</li>
            </ul>
          </div>
          <div class="skill-card">
            <h3>☁️ Virtualisation</h3>
            <ul>
              <li>VMware, VirtualBox</li>
              <li>Hyper-V &amp; Proxmox</li>
              <li>Déploiement de VMs</li>
            </ul>
          </div>
          <div class="skill-card">
            <h3>🖥️ Systèmes</h3>
            <ul>
              <li>Windows Server</li>
              <li>Linux (Debian/Mint)</li>
              <li>Supervision</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer id="simple-footer">
    <div class="footer-line"></div>
    <p>Made by Adam Bellanger 2025 &copy;</p>
  </footer>

  <!-- SCRIPTS -->
  <script type="importmap">
    {
      "imports": {
        "three": "https://cdn.jsdelivr.net/npm/three@0.182.0/build/three.module.js"
      }
    }
  </script>
  <script type="module" src="js/threescene.js"></script>
  <script src="js/ui.js"></script>

  <!-- SCROLL-TO-TOP -->
  <button id="scroll-top-btn" title="Retour en haut">
    <i class="fas fa-chevron-up"></i>
  </button>

</body>
</html>
