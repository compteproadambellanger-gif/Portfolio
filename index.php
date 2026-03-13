<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  FORMULAIRE DE CONTACT
// ═══════════════════════════════════════════════════════════════════════════════
$contact_status  = '';   // 'success' | 'error' | ''
$contact_values  = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
  $name    = trim(strip_tags($_POST['name']    ?? ''));
  $email   = trim(strip_tags($_POST['email']   ?? ''));
  $subject = trim(strip_tags($_POST['subject'] ?? ''));
  $message = trim(strip_tags($_POST['message'] ?? ''));

  $contact_values = compact('name', 'email', 'subject', 'message');

  if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $subject && $message) {
    $to      = 'adam.bellanger.pro@gmail.com';
    $headers = implode("\r\n", [
      'From: Portfolio <' . $email . '>',
      'Reply-To: ' . $email,
      'MIME-Version: 1.0',
      'Content-Type: text/plain; charset=UTF-8',
    ]);
    $body = "Nom    : $name\nEmail  : $email\nSujet  : $subject\n\n$message";

    if (@mail($to, '[Portfolio] ' . $subject, $body, $headers)) {
      $contact_status = 'success';
      $contact_values = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
    } else {
      // mail() non configuré (XAMPP local) → on simule le succès en dev
      $contact_status = 'success_dev';
      $contact_values = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
    }
  } else {
    $contact_status = 'error';
  }
}

// ═══════════════════════════════════════════════════════════════════════════════
//  VEILLE TECHNOLOGIQUE – Fetch RSS réel + cache 2 semaines + fallback statique
// ═══════════════════════════════════════════════════════════════════════════════

define('VEILLE_CACHE', __DIR__ . '/cache/veille_cache.json');
define('VEILLE_TTL',   7 * 24 * 3600); // 7 jours

// ── Mots-clés → catégorie, couleur, icône ─────────────────────────────────────
$veille_keywords = [
  'ransomware'             => ['Cybersécurité', '#ff4757', 'fa-shield-alt'],
  'cyberattaque'           => ['Cybersécurité', '#ff4757', 'fa-shield-alt'],
  'vulnérabilité'          => ['Cybersécurité', '#ff4757', 'fa-shield-alt'],
  'phishing'               => ['Cybersécurité', '#ff4757', 'fa-shield-alt'],
  'zero-day'               => ['Cybersécurité', '#ff4757', 'fa-shield-alt'],
  'cybersécurité'          => ['Cybersécurité', '#ff4757', 'fa-shield-alt'],
  'sécurité informatique'  => ['Cybersécurité', '#ff4757', 'fa-shield-alt'],
  'wifi'                   => ['Réseau',         '#00d4ff', 'fa-wifi'],
  'wi-fi'                  => ['Réseau',         '#00d4ff', 'fa-wifi'],
  'réseau'                 => ['Réseau',         '#00d4ff', 'fa-network-wired'],
  'routeur'                => ['Réseau',         '#00d4ff', 'fa-network-wired'],
  'cisco'                  => ['Réseau',         '#00d4ff', 'fa-network-wired'],
  'ipv6'                   => ['Réseau',         '#00d4ff', 'fa-network-wired'],
  'sd-wan'                 => ['Réseau',         '#00d4ff', 'fa-network-wired'],
  'virtualisation'         => ['Virtualisation', '#a855f7', 'fa-server'],
  'proxmox'                => ['Virtualisation', '#a855f7', 'fa-server'],
  'vmware'                 => ['Virtualisation', '#a855f7', 'fa-server'],
  'docker'                 => ['Virtualisation', '#a855f7', 'fa-server'],
  'conteneur'              => ['Virtualisation', '#a855f7', 'fa-server'],
  'hyperviseur'            => ['Virtualisation', '#a855f7', 'fa-server'],
  'cloud'                  => ['Cloud',          '#f97316', 'fa-cloud'],
  'azure'                  => ['Cloud',          '#f97316', 'fa-cloud'],
  'aws'                    => ['Cloud',          '#f97316', 'fa-cloud'],
  'saas'                   => ['Cloud',          '#f97316', 'fa-cloud'],
  'linux'                  => ['Systèmes',       '#22c55e', 'fa-linux'],
  'windows server'         => ['Systèmes',       '#22c55e', 'fa-windows'],
  'active directory'       => ['Systèmes',       '#22c55e', 'fa-server'],
  'debian'                 => ['Systèmes',       '#22c55e', 'fa-linux'],
  'intelligence artificielle' => ['IA & Infra',  '#e879f9', 'fa-robot'],
  'chatgpt'                => ['IA & Infra',     '#e879f9', 'fa-robot'],
  'llm'                    => ['IA & Infra',     '#e879f9', 'fa-robot'],
];

function veille_categorize(string $text, array $keywords): array {
  $lower = mb_strtolower($text);
  foreach ($keywords as $kw => $info) {
    if (mb_strpos($lower, $kw) !== false) return $info;
  }
  return ['Technologie', '#00d4ff', 'fa-microchip'];
}

function veille_fetch_rss(string $url): ?\SimpleXMLElement {
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 2,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Portfolio/1.0)',
      CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $data = curl_exec($ch);
    return $data ? @simplexml_load_string($data) : null;
  }
  $data = @file_get_contents($url);
  return $data ? @simplexml_load_string($data) : null;
}

function veille_get_live(array $keywords): ?array {
  // Vérification du cache
  if (file_exists(VEILLE_CACHE) && (time() - filemtime(VEILLE_CACHE)) < VEILLE_TTL) {
    $c = json_decode(file_get_contents(VEILLE_CACHE), true);
    if (is_array($c) && count($c) >= 4) return $c;
  }

  $feeds = [
    ['url' => 'https://www.cert.ssi.gouv.fr/feed/',    'label' => 'ANSSI CERT'],
    ['url' => 'https://www.numerama.com/feed/',         'label' => 'Numerama'],
    ['url' => 'https://www.01net.com/rss/actualites/',  'label' => '01net'],
    ['url' => 'https://www.lemondeinformatique.fr/flux-rss/thematique/toutes-les-actualites/rss.xml', 'label' => 'LMI'],
  ];

  $articles        = [];
  $used_categories = [];

  foreach ($feeds as $feed) {
    if (count($articles) >= 4) break;
    $xml = veille_fetch_rss($feed['url']);
    if (!$xml) continue;

    $items = isset($xml->channel->item) ? $xml->channel->item : [];
    foreach ($items as $item) {
      if (count($articles) >= 4) break;

      $title    = trim((string)$item->title);
      $desc_raw = strip_tags((string)($item->description ?? $item->summary ?? ''));
      $desc_raw = preg_replace('/\s+/', ' ', $desc_raw);
      $pub      = (string)($item->pubDate ?? $item->published ?? '');
      $link     = (string)($item->link ?? '');

      if (empty($title)) continue;

      $cat = veille_categorize($title . ' ' . $desc_raw, $keywords);
      if (in_array($cat[0], $used_categories)) continue; // 1 par catégorie
      $used_categories[] = $cat[0];

      $articles[] = [
        'accent'   => $cat[1],
        'icon'     => $cat[2],
        'category' => $cat[0],
        'date'     => $pub ? date('d/m/Y', strtotime($pub)) : date('d/m/Y'),
        'title'    => $title,
        'source'   => $feed['label'],
        'desc'     => mb_strlen($desc_raw) > 200 ? mb_substr($desc_raw, 0, 197) . '…' : $desc_raw,
        'link'     => $link,
      ];
    }
  }

  if (count($articles) >= 4) {
    if (!is_dir(dirname(VEILLE_CACHE))) mkdir(dirname(VEILLE_CACHE), 0755, true);
    file_put_contents(VEILLE_CACHE, json_encode(array_slice($articles, 0, 4), JSON_UNESCAPED_UNICODE));
    return array_slice($articles, 0, 4);
  }
  return null;
}

// ── Fallback statique (si RSS indisponible) ────────────────────────────────────
$veille_fallback = [
  ['accent'=>'#ff4757','icon'=>'fa-shield-alt','category'=>'Cybersécurité','date'=>'2026','title'=>'Zero Trust Architecture','source'=>'ANSSI','desc'=>'Approche "ne jamais faire confiance, toujours vérifier" qui remplace le modèle périmétrique traditionnel. Pertinente dans un contexte de télétravail et d\'accès cloud.','link'=>''],
  ['accent'=>'#00d4ff','icon'=>'fa-wifi','category'=>'Réseau','date'=>'2026','title'=>'Wi-Fi 7 (802.11be)','source'=>'ZDNet France','desc'=>'Nouvelle génération Wi-Fi avec des débits théoriques jusqu\'à 46 Gbps et une latence très réduite. Impact direct sur les déploiements réseau en entreprise.','link'=>''],
  ['accent'=>'#a855f7','icon'=>'fa-server','category'=>'Virtualisation','date'=>'2026','title'=>'Proxmox VE & Conteneurisation','source'=>'LeMagIT','desc'=>'Montée en puissance de Proxmox comme alternative open source à VMware suite au rachat par Broadcom. Couplé à Docker/LXC pour une gestion flexible des workloads.','link'=>''],
  ['accent'=>'#f97316','icon'=>'fa-cloud','category'=>'Cloud','date'=>'2026','title'=>'SASE & SD-WAN en entreprise','source'=>'Le Monde Informatique','desc'=>'Convergence du réseau et de la sécurité via le modèle SASE. Les entreprises migrent vers des architectures SD-WAN pour plus d\'agilité.','link'=>''],
];

// ── Résolution finale ──────────────────────────────────────────────────────────
$veille_now  = veille_get_live($veille_keywords);
$veille_live = ($veille_now !== null);
if (!$veille_live) $veille_now = $veille_fallback;

$update_label = file_exists(VEILLE_CACHE)
  ? date('d/m/Y', filemtime(VEILLE_CACHE))
  : date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="favicon.svg" />
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
          <i class="fab fa-github"></i> Explorer sur GitHub
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
          <i class="fab fa-github"></i> Explorer sur GitHub
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
        <a href="https://github.com/compteproadambellanger-gif" class="btn-modal"><i class="fab fa-github"></i> Explorer sur GitHub</a>
      </div>
    </div>
  </div>
  
  <div id="modal-boutique" class="modal-overlay">
    <div class="modal-content">
      <button class="close-modal">&times;</button>
      <div class="modal-body">
        <h2>Boutique en ligne Aesop</h2>
        <div class="modal-tags">
          <span class="modal-tag">PHP</span>
          <span class="modal-tag">MySQL</span>
          <span class="modal-tag">HTML/CSS</span>
          <span class="modal-tag">JavaScript</span>
          <span class="modal-tag">API stripe</span>
        </div>
        <p>
          Ensemble de scripts php / js pour créer une boutique en ligne.
        </p>
        <ul>
          <li>Création d'une base de données pour stocker les produits et les commandes.</li>
          <li>Création d'une interface pour ajouter des produits à la boutique.</li>
          <li>Nouveau design de la boutique Aesop</li>
        </ul>
        <a href="https://github.com/compteproadambellanger-gif/Aesop" class="btn-modal"><i class="fab fa-github"></i> Explorer sur GitHub</a>
      </div>
    </div>
  </div>

  <div id="modal-studio" class="modal-overlay">
    <div class="modal-content">
      <button class="close-modal">&times;</button>
      <div class="modal-body">
        <h2>Studio Landing Pages</h2>
        <div class="modal-tags">
          <span class="modal-tag">PHP / MySQL</span>
          <span class="modal-tag">REACT.JS / JS</span>
          <span class="modal-tag">TAILWIND CSS</span>
          <span class="modal-tag">SERVER / VITE</span>
        </div>
        <p>
          Gros Projet de création de site web pour une entreprise de création de site web.
        </p>
        <ul>
          <li>création d'une base de donnée MySQL pour stocker les informations des clients et de leurs projets.</li>
          <li>création d'une interface pour ajouter des clients et de leurs projets.</li>
          <li>Nouveau design futuriste avec des animations fluides et des transitions rapides.</li>
        </ul>
        <a href="https://github.com/compteproadambellanger-gif/StudioLandingPages" class="btn-modal"><i class="fab fa-github"></i> Explorer sur GitHub</a>
      </div>
    </div>
  </div>
  <!-- NAVIGATION -->
  <nav class="github-pill delayed-entry">
    <div class="nav-marker"></div>
    <a href="#accueil" class="nav-link active">Accueil</a>
    <a href="#apropos" class="nav-link">À propos</a>
    <a href="#parcours" class="nav-link">Mon Parcours</a>
    <a href="#competences-1" class="nav-link">Logiciel &amp; Outils</a>
    <a href="#projets" class="nav-link">Projets</a>
    <a href="#competences" class="nav-link">Compétences</a>
    <a href="#veille" class="nav-link">Veille</a>
    <a href="#apprentissage" class="nav-link">Apprentissage</a>
    <a href="#contact" class="nav-link">Contact</a>
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
      <a href="cv/Adam%20BELLANGER.pdf" download="CV_Adam_Bellanger.pdf" class="btn-cv">
        <i class="fas fa-download"></i> Télécharger mon CV
      </a>
    </div>
  </section>

  <!-- À PROPOS -->
  <section id="apropos" class="delayed-entry">
    <div class="container">
      <div class="content-box">
        <h2>À propos</h2>
        <p>
          Actuellement étudiant en première année de BTS SIO (option SISR) en alternance au campus IRIS Rouen, 
          je cultive un profil hybride entre l'administration système et le développement. 
          Passionné par l'écosystème des réseaux, je me spécialise dans le déploiement d'infrastructures Cisco, 
          la virtualisation et la sécurité des environnements Windows et Linux.
        </p>
        <p>
          Mon expertise s'étend également au développement. Côté <strong>Front-end</strong>, je m'attache à créer des 
          interfaces immersives et performantes en utilisant des technologies comme Three.js et des animations CSS avancées 
          pour offrir une expérience utilisateur fluide. Côté <strong>Back-end</strong>, je maîtrise la logique serveur 
          avec PHP et la gestion de bases de données MySQL, tout en utilisant Python pour l'automatisation de tâches techniques.
        </p>
        <p>
          Mon rythme en alternance chez Socacom me permet de confronter quotidiennement la théorie à la réalité du terrain, 
          développant ainsi une forte réactivité face aux incidents et une volonté constante d'optimisation des systèmes d'information.
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
        <div class="grid-item" data-tooltip="Structuration des pages web">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg" alt="HTML5" class="tool-logo" />
          <span>HTML</span>
        </div>
        <div class="grid-item" data-tooltip="Mise en forme et animations">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-original.svg" alt="CSS3" class="tool-logo" />
          <span>CSS</span>
        </div>
        <div class="grid-item" data-tooltip="Interactivité côté client">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg" alt="JavaScript" class="tool-logo" />
          <span>JavaScript</span>
        </div>
        <div class="grid-item" data-tooltip="Scripts et automatisation">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-original.svg" alt="Python" class="tool-logo" />
          <span>Python</span>
        </div>
        <div class="grid-item" data-tooltip="Développement backend et base de données">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" alt="PHP" class="tool-logo" />
          <span>PHP</span>
        </div>
      </div>
      <div class="divider"></div>
      <div style="text-align: center;">
        <h2 class="title-tools">Mes outils / Logiciel</h2>
      </div>
      <div class="custom-grid">
        <div class="grid-item" data-tooltip="Retouche photo & graphisme">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/photoshop/photoshop-original.svg" alt="Photoshop" class="tool-logo" />
          <span>Photoshop</span>
        </div>
        <div class="grid-item" data-tooltip="Modélisation 3D & rendu">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/blender/blender-original.svg" alt="Blender" class="tool-logo" />
          <span>Blender</span>
        </div>
        <div class="grid-item" data-tooltip="IDE principal de développement">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/vscode/vscode-original.svg" alt="VS Code" class="tool-logo" />
          <span>Visual Studio</span>
        </div>
        <div class="grid-item" data-tooltip="IDE Java & projets scolaires">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/eclipse/eclipse-original.svg" alt="Eclipse" class="tool-logo" />
          <span>Eclipse</span>
        </div>
        <div class="grid-item" data-tooltip="Versioning & collaboration">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg" alt="GitHub" class="tool-logo" />
          <span>GitHub</span>
        </div>
        <div class="grid-item" data-tooltip="Assistant IA de développement en terminal">
          <img src="https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/anthropic.svg" alt="Claude Code" class="tool-logo" style="filter: invert(1);" />
          <span>Claude Code</span>
        </div>
        <div class="grid-item" data-tooltip="IDE nouvelle génération by Google">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/google/google-original.svg" alt="Antigravity" class="tool-logo" />
          <span>Antigravity</span>
        </div>
      </div>
    </div>
  </section>

  <!-- PROJETS -->
  <section id="projets" class="delayed-entry section-projets-fullwidth">
    <div class="projets-header">
      <h2>Mes Projets</h2>
      <p>Cliquez sur un projet pour voir les détails.</p>
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

      <div class="bubble-project" onclick="openModal('modal-boutique')">
        <div class="bubble-content">
          <i class="fas fa-store project-icon-main"></i>
          <h4 class="project-title">Boutique en ligne</h4>
          <span class="project-tech">PHP / MySQL / JS</span>
        </div>
        <div class="bubble-glow" style="background: rgba(145, 73, 223, 0.4);"></div>
      </div>

      <div class="bubble-project" onclick="openModal('modal-studio')">
        <div class="bubble-content">
          <i class="fas fa-rocket project-icon-main"></i>
          <h4 class="project-title">Studio de Landing Pages</h4>
          <span class="project-tech">PHP / MySQL / TAILWIND CSS / REACT.JS</span>
        </div>
        <div class="bubble-glow" style="background: rgba(179, 182, 29, 0.4);"></div>
      </div>
    </div>
      
  </section>

  <!-- COMPÉTENCES -->
  <section id="competences" class="delayed-entry">
    <div class="container">
      <div class="content-box">
        <h2>Compétences Techniques</h2>
        <div class="skills-grid">
          <div class="skill-card" data-tooltip="Cisco, Huawei, VLANs, routage inter-VLAN">
            <h3>🌐 Réseaux &amp; Infra</h3>
            <ul>
              <li>Cisco et Huawei</li>
              <li>Architecture &amp; VLANs</li>
              <li>Solutions opérateurs</li>
            </ul>
          </div>
          <div class="skill-card" data-tooltip="VoIP SIP, Alcatel OXO, Centrex cloud">
            <h3>📞 Téléphonie</h3>
            <ul>
              <li>Alcatel (OXO, IP)</li>
              <li>VoIP / ToIP / SIP</li>
              <li>Centrex et UnyCX</li>
            </ul>
          </div>
          <div class="skill-card" data-tooltip="VMware, Proxmox, Hyper-V, VirtualBox">
            <h3>☁️ Virtualisation</h3>
            <ul>
              <li>VMware, VirtualBox</li>
              <li>Hyper-V &amp; Proxmox</li>
              <li>Déploiement de VMs</li>
            </ul>
          </div>
          <div class="skill-card" data-tooltip="Windows Server, Linux Debian, supervision">
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

  <!-- VEILLE TECHNOLOGIQUE -->
  <section id="veille" class="delayed-entry">
    <div class="container">
      <div class="content-box">
        <h2>Veille Technologique</h2>
        <p style="color: rgba(255,255,255,0.55); margin-bottom: 0.5rem; font-size: 0.95rem; margin-top: -1rem;">
          Domaines surveillés activement dans le cadre de ma formation BTS SIO SISR.
        </p>
        <p style="color: rgba(0,212,255,0.5); font-size: 0.72rem; margin-bottom: 2rem; letter-spacing: 0.5px;">
          <?php if ($veille_live): ?>
            <i class="fas fa-circle" style="color:#22c55e;font-size:0.55rem;vertical-align:middle;"></i> Flux RSS live · Mis à jour le <?= $update_label ?> · Refresh automatique toutes les semaines
          <?php else: ?>
            <i class="fas fa-circle" style="color:#f97316;font-size:0.55rem;vertical-align:middle;"></i> Contenu de référence · Actualisation au prochain accès réseau
          <?php endif; ?>
        </p>
        <div class="veille-grid">
          <?php foreach ($veille_now as $a): ?>
          <div class="veille-card" style="--accent: <?= $a['accent'] ?>;">
            <div class="veille-header">
              <span class="veille-category" style="color:<?= $a['accent'] ?>;"><i class="fas <?= $a['icon'] ?>"></i> <?= $a['category'] ?></span>
              <span class="veille-date"><?= $a['date'] ?></span>
            </div>
            <h3 class="veille-title"><?= htmlspecialchars($a['title']) ?></h3>
            <p class="veille-source"><i class="fas fa-globe"></i> <?= $a['source'] ?></p>
            <p class="veille-desc"><?= htmlspecialchars($a['desc']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- EN COURS D'APPRENTISSAGE -->
  <section id="apprentissage" class="delayed-entry">
    <div class="container">
      <div class="content-box">
        <h2>En cours d'apprentissage</h2>
        <p style="color: rgba(255,255,255,0.5); margin-bottom: 2.5rem; font-size: 0.9rem; margin-top: -0.8rem;">
          Technologies que j'explore et développe activement en ce moment.
        </p>
        <div class="learning-grid">

          <div class="learning-item">
            <div class="learning-header">
              <span class="learning-icon"><i class="fab fa-python"></i></span>
              <span class="learning-name">Python</span>
              <span class="learning-percent">90%</span>
            </div>
            <div class="learning-bar-track">
              <div class="learning-bar-fill" data-width="90" style="background: linear-gradient(90deg, #3776ab, #ffd43b);"></div>
            </div>
            <p class="learning-desc">Scripts d'automatisation, manipulation de fichiers, initiation à la data</p>
          </div>

          <div class="learning-item">
            <div class="learning-header">
              <span class="learning-icon"><i class="fab fa-docker"></i></span>
              <span class="learning-name">Docker</span>
              <span class="learning-percent">40%</span>
            </div>
            <div class="learning-bar-track">
              <div class="learning-bar-fill" data-width="40" style="background: linear-gradient(90deg, #0db7ed, #384d54);"></div>
            </div>
            <p class="learning-desc">Conteneurisation d'applications, docker-compose, déploiement de services</p>
          </div>

          <div class="learning-item">
            <div class="learning-header">
              <span class="learning-icon"><i class="fab fa-php"></i></span>
              <span class="learning-name">PHP / SQL</span>
              <span class="learning-percent">90%</span>
            </div>
            <div class="learning-bar-track">
              <div class="learning-bar-fill" data-width="90" style="background: linear-gradient(90deg, #777bb4, #4f5b93);"></div>
            </div>
            <p class="learning-desc">Développement backend, PDO, authentification, CRUD complet</p>
          </div>

          <div class="learning-item">
            <div class="learning-header">
              <span class="learning-icon"><i class="fas fa-shield-alt"></i></span>
              <span class="learning-name">Cybersécurité</span>
              <span class="learning-percent">35%</span>
            </div>
            <div class="learning-bar-track">
              <div class="learning-bar-fill" data-width="35" style="background: linear-gradient(90deg, #ff4757, #c0392b);"></div>
            </div>
            <p class="learning-desc">Bases de la sécurité réseau, pare-feu, veille ANSSI, CTF débutant</p>
          </div>

          <div class="learning-item">
            <div class="learning-header">
              <span class="learning-icon"><i class="fas fa-cloud"></i></span>
              <span class="learning-name">Azure / Cloud</span>
              <span class="learning-percent">25%</span>
            </div>
            <div class="learning-bar-track">
              <div class="learning-bar-fill" data-width="25" style="background: linear-gradient(90deg, #0078d4, #005a9e);"></div>
            </div>
            <p class="learning-desc">Initiation aux services cloud Microsoft, VMs Azure, notions AD DS</p>
          </div>

          <div class="learning-item">
            <div class="learning-header">
              <span class="learning-icon"><i class="fab fa-react"></i></span>
              <span class="learning-name">React</span>
              <span class="learning-percent">80%</span>
            </div>
            <div class="learning-bar-track">
              <div class="learning-bar-fill" data-width="80" style="background: linear-gradient(90deg, #0078d4, #005a9e);"></div>
            </div>
            <p class="learning-desc">Création d'interfaces utilisateur fluides et interactives</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section id="contact" class="delayed-entry">
    <div class="container">
      <div class="content-box">
        <h2>Me Contacter</h2>
        <p style="color: rgba(255,255,255,0.5); margin-bottom: 2rem; font-size: 0.9rem; margin-top: -0.8rem;">
          Une question, une opportunité de stage ou juste un bonjour ?
        </p>

        <?php if ($contact_status === 'success' || $contact_status === 'success_dev'): ?>
          <div class="contact-alert contact-alert--success">
            <i class="fas fa-check-circle"></i>
            Message envoyé ! Je vous répondrai dès que possible.
            <?php if ($contact_status === 'success_dev'): ?>
              <span style="opacity:0.5; font-size:0.78rem; display:block; margin-top:4px;">(Mode local XAMPP — configurer php.ini pour l'envoi réel)</span>
            <?php endif; ?>
          </div>
        <?php elseif ($contact_status === 'error'): ?>
          <div class="contact-alert contact-alert--error">
            <i class="fas fa-exclamation-triangle"></i>
            Veuillez remplir tous les champs correctement.
          </div>
        <?php endif; ?>

        <form class="contact-form" method="POST" action="#contact">
          <div class="contact-row">
            <div class="contact-field">
              <label for="cf-name">Nom</label>
              <input type="text" id="cf-name" name="name" placeholder="Votre nom"
                     value="<?= htmlspecialchars($contact_values['name']) ?>" required autocomplete="name" />
            </div>
            <div class="contact-field">
              <label for="cf-email">Email</label>
              <input type="email" id="cf-email" name="email" placeholder="votre@email.com"
                     value="<?= htmlspecialchars($contact_values['email']) ?>" required autocomplete="email" />
            </div>
          </div>
          <div class="contact-field">
            <label for="cf-subject">Sujet</label>
            <input type="text" id="cf-subject" name="subject" placeholder="Ex : Proposition de stage, Question..."
                   value="<?= htmlspecialchars($contact_values['subject']) ?>" required />
          </div>
          <div class="contact-field">
            <label for="cf-message">Message</label>
            <textarea id="cf-message" name="message" rows="5"
                      placeholder="Votre message..."><?= htmlspecialchars($contact_values['message']) ?></textarea>
          </div>
          <button type="submit" name="contact_submit" class="btn-modal contact-submit">
            <i class="fas fa-paper-plane"></i> Envoyer le message
          </button>
        </form>

        <div class="contact-links">
          <a href="https://www.linkedin.com/in/adam-bellanger-652919386/" target="_blank" rel="noreferrer" class="contact-link" data-tooltip="Mon profil LinkedIn">
            <i class="fab fa-linkedin"></i> LinkedIn
          </a>
          <a href="https://github.com/compteproadambellanger-gif" target="_blank" rel="noreferrer" class="contact-link" data-tooltip="Mes repos GitHub">
            <i class="fab fa-github"></i> GitHub
          </a>
          <a href="cv/Adam%20BELLANGER.pdf" download="CV_Adam_Bellanger.pdf" class="contact-link" data-tooltip="Télécharger mon CV PDF">
            <i class="fas fa-file-pdf"></i> CV PDF
          </a>
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
