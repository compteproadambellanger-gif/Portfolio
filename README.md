# Portfolio – Adam Bellanger

Portfolio personnel développé en PHP/HTML, CSS et JavaScript vanilla avec une scène 3D interactive via Three.js.

---

## Structure des fichiers

```
portfolio-react-clean/
├── index.php           → Page principale (structure HTML complète)
├── css/
│   └── style.css       → Tous les styles du site
└── js/
    ├── threescene.js   → Scène 3D (Three.js)
    └── ui.js           → Interactions et navigation
```

---

## Détail des fichiers

### `index.php`
Contient l'intégralité du HTML de la page. Les sections sont dans cet ordre :
- **Splash screen** — écran de démarrage avec le bouton START
- **Modales** — fenêtres de détail pour chaque projet (Portfolio 3D, Infra Cisco, Scripts)
- **Navigation** — barre de navigation fixe en forme de pill
- **Accueil** — hero avec nom, titre et localisation
- **À propos** — présentation et parcours de formation
- **Mon Parcours** — formation scolaire et expériences professionnelles
- **Logiciels & Outils** — compétences dev (HTML, CSS, JS) et outils (Photoshop, Blender, etc.)
- **Projets** — cards cliquables qui ouvrent les modales
- **Compétences Techniques** — Réseaux, Téléphonie, Virtualisation, Systèmes
- **Footer**

Three.js est chargé via CDN jsDelivr grâce à un `importmap` en bas de page.

---

### `css/style.css`
Feuille de style unique sans framework. Organisation :
- **Variables CSS** — couleurs et effets réutilisables (`--primary-color`, `--glass-bg`, etc.)
- **Reset & bases** — normalisation, scrollbar cachée, fond noir
- **Canvas** — positionnement du rendu Three.js en arrière-plan fixe
- **Navigation pill** — barre de nav fixe avec marker animé
- **Hero** — typographie grande taille, badge et localisation
- **Content boxes / Glass bubble** — conteneurs glassmorphism
- **Parcours** — layout sticky sidebar + timeline
- **Grids** — grilles compétences et outils
- **Projets** — cards avec effet glow au survol
- **Modales** — overlay avec animation d'entrée scale
- **Splash screen** — plein écran avec animation `pulse-glow`
- **Delayed entry** — animation d'apparition des sections après le START
- **Footer** — ligne lumineuse cyan
- **Responsive** — breakpoints 1200px, 1024px, 768px

---

### `js/threescene.js`
Gère toute la scène 3D en arrière-plan. Chargé en `type="module"` pour utiliser l'import Three.js.

- **Shaders GLSL** — vertex et fragment shaders personnalisés pour déformer les blobs organiquement
- **Classe `Blob`** — objet Three.js avec géométrie icosaèdre, shader material et uniforms animés
- **`createParticleSystem()`** — génère 1200 particules/étoiles avec 3 types (normale, glow, halo)
- **`initThreeScene()`** — fonction principale qui :
  - Crée le renderer WebGL transparent en fond fixe
  - Place 1 blob principal + 3 satellites
  - Gère le suivi de la souris (parallaxe caméra)
  - Écoute le bouton START pour déclencher l'animation d'entrée (blob apparaît, satellites disparaissent, étoiles s'estompent)
  - Boucle d'animation `requestAnimationFrame`
  - Gère le resize de fenêtre

---

### `js/ui.js`
Gère toutes les interactions de la page, sans dépendance.

- **`openModal(id)`** — fonction globale appelée depuis les `onclick` HTML pour ouvrir une modale projet
- **Splash / START** — masque l'écran de démarrage et révèle les sections avec un délai
- **Navigation pill** — déplace le marker animé sous le lien actif
- **IntersectionObserver** — détecte la section visible et met à jour la nav automatiquement
- **Scroll fluide** — interception des clics nav pour un scroll smooth sans rechargement
- **Fermeture des modales** — via bouton ✕ ou clic sur l'overlay

---

## Lancer le projet

Aucune compilation nécessaire. Il suffit d'un serveur PHP local (XAMPP par exemple) :

1. Placer le dossier dans `htdocs/`
2. Démarrer Apache via XAMPP
3. Ouvrir `http://localhost/portfolio-react-clean/`

Three.js est chargé depuis le CDN, une connexion internet est donc requise.
