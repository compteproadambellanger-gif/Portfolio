import * as THREE from 'three';

const glslNoiseChunk = `
vec3 mod289(vec3 x){return x-floor(x*(1.0/289.0))*289.0;}
vec4 mod289(vec4 x){return x-floor(x*(1.0/289.0))*289.0;}
vec4 permute(vec4 x){return mod289(((x*34.0)+1.0)*x);}
vec3 fade(vec3 t){return t*t*t*(t*(t*6.0-15.0)+10.0);}
vec3 rotateY(vec3 v,float angle){float s=sin(angle);float c=cos(angle);return mat3(c,0,-s,0,1,0,s,0,c)*v;}
`;

const vertexShader = `
varying vec2 vUv;
varying float vDistort;
uniform float uTime;
uniform float uSpeed;
uniform float uNoiseStrength;
uniform float uNoiseDensity;
uniform float uFreq;
uniform float uAmp;
uniform float uOffset;
${glslNoiseChunk}
void main(){
  vUv=uv;
  float t=uTime*uSpeed;
  vec3 noisePos=(normal+t)*uNoiseDensity;
  float distortion=(sin(noisePos.x)*cos(noisePos.y)*sin(noisePos.z))*uNoiseStrength;
  vec3 pos=position+(normal*distortion);
  float angle=sin(uv.y*uFreq+t)*uAmp;
  pos=rotateY(pos,angle);
  vDistort=distortion;
  gl_Position=projectionMatrix*modelViewMatrix*vec4(pos,1.);
}
`;

const fragmentShader = `
varying vec2 vUv;
varying float vDistort;
uniform float uHue;
uniform float uAlpha;
void main(){
  float distort=vDistort*2.0;
  vec3 color=vec3(0.5+0.5*cos(6.28318*(uHue+distort+vec3(0.0,0.1,0.2))));
  gl_FragColor=vec4(color,uAlpha);
}
`;

class Blob extends THREE.Object3D {
  constructor(size, speed, color, density, strength) {
    super();
    this.geometry = new THREE.IcosahedronGeometry(size, 128);
    this.material = new THREE.ShaderMaterial({
      vertexShader,
      fragmentShader,
      uniforms: {
        uTime:         { value: 0 },
        uSpeed:        { value: speed },
        uNoiseDensity: { value: density },
        uNoiseStrength:{ value: strength },
        uFreq:         { value: 3 },
        uAmp:          { value: 6 },
        uHue:          { value: color },
        uOffset:       { value: 0 },
        uAlpha:        { value: 1.0 },
      },
      transparent: true,
    });
    this.mesh = new THREE.Mesh(this.geometry, this.material);
    this.add(this.mesh);
  }

  dispose() {
    this.geometry.dispose();
    this.material.dispose();
  }
}

function createParticleSystem() {
  const particlesCount = 1200;
  const positions = new Float32Array(particlesCount * 3);
  const sizes     = new Float32Array(particlesCount);
  const types     = new Float32Array(particlesCount);

  for (let i = 0; i < particlesCount; i++) {
    positions[i * 3]     = (Math.random() - 0.5) * 50;
    positions[i * 3 + 1] = (Math.random() - 0.5) * 40;
    positions[i * 3 + 2] = (Math.random() - 0.5) * 60;

    const rand = Math.random();
    if (rand < 0.08) {
      sizes[i] = Math.random() * 3 + 2.5;
      types[i] = 2.0;
    } else if (rand < 0.2) {
      sizes[i] = Math.random() * 2 + 1.5;
      types[i] = 1.0;
    } else {
      sizes[i] = Math.random() * 1.5 + 0.3;
      types[i] = 0.0;
    }
  }

  const geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  geo.setAttribute('size',     new THREE.BufferAttribute(sizes, 1));
  geo.setAttribute('aType',    new THREE.BufferAttribute(types, 1));

  const mat = new THREE.ShaderMaterial({
    uniforms: {
      uTime:    { value: 0 },
      uOpacity: { value: 1.0 },
    },
    vertexShader: `
      uniform float uTime;
      attribute float size;
      attribute float aType;
      varying float vType;
      void main() {
        vType = aType;
        vec4 modelPosition = modelMatrix * vec4(position, 1.0);
        modelPosition.y += sin(uTime * 0.3 + position.x * 0.1) * 0.2;
        modelPosition.x += cos(uTime * 0.2 + position.y * 0.1) * 0.15;
        vec4 viewPosition = viewMatrix * modelPosition;
        vec4 projectedPosition = projectionMatrix * viewPosition;
        gl_Position = projectedPosition;
        gl_PointSize = size * 60.0;
        gl_PointSize *= (1.0 / -viewPosition.z);
      }
    `,
    fragmentShader: `
      uniform float uOpacity;
      varying float vType;
      void main() {
        float distanceToCenter = distance(gl_PointCoord, vec2(0.5));
        float alpha;
        if (vType > 1.5) {
          float core = smoothstep(0.5, 0.2, distanceToCenter);
          float halo = 0.10 / distanceToCenter - 0.25;
          halo = clamp(halo, 0.0, 0.6);
          alpha = max(core, halo);
        } else if (vType > 0.5) {
          float core = smoothstep(0.5, 0.3, distanceToCenter);
          float glow = 0.08 / distanceToCenter - 0.16;
          glow = clamp(glow, 0.0, 0.3);
          alpha = max(core, glow);
        } else {
          if (distanceToCenter > 0.5) discard;
          alpha = smoothstep(0.5, 0.4, distanceToCenter);
        }
        gl_FragColor = vec4(1.0, 1.0, 1.0, alpha * uOpacity);
      }
    `,
    transparent: true,
    blending: THREE.AdditiveBlending,
    depthWrite: false,
  });

  const points = new THREE.Points(geo, mat);
  points._dispose = () => { geo.dispose(); mat.dispose(); };
  return points;
}

function initThreeScene() {
  const container = document.querySelector('.three-container');
  if (!container) return;

  let rafId = 0;
  let disposed = false;

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setClearColor(0x000000, 0);

  const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
  camera.position.set(0, 0, 18);

  const scene = new THREE.Scene();
  const clock = new THREE.Clock();

  const canvas = renderer.domElement;
  canvas.classList.add('webgl');
  container.appendChild(canvas);

  // Blobs
  const blob = new Blob(4.5, 0.3, 0.6, 5.3, 0.5);
  blob.scale.set(0, 0, 0);
  scene.add(blob);

  const satellites = [];
  const satConfigs = [
    { size: 1.4, speed: 0.4, col: 0.6,  density: 4.0, strength: 0.25, x:  4, y: -1.5, z:  2 },
    { size: 1.1, speed: 0.5, col: 0.85, density: 4.0, strength: 0.25, x: -5, y:  3,   z: -2 },
    { size: 0.9, speed: 0.6, col: 0.08, density: 4.0, strength: 0.25, x: -2, y: -3.5, z:  0 },
  ];

  satConfigs.forEach((conf) => {
    const sat = new Blob(conf.size, conf.speed, conf.col, conf.density, conf.strength);
    sat.position.set(conf.x, conf.y, conf.z);
    sat.scale.set(1, 1, 1);
    sat.visible = true;
    scene.add(sat);
    satellites.push(sat);
  });

  const particles = createParticleSystem();
  scene.add(particles);

  let introOver = false;

  const mouse = { x: 0, y: 0 };
  const targetCameraPos = { x: 0, y: 0 };

  function forceHideSatellites() {
    satellites.forEach((sat) => {
      sat.scale.set(0, 0, 0);
      sat.visible = false;
      sat.mesh.material.uniforms.uAlpha.value = 0.0;
    });
  }

  function forceShowSatellites() {
    satellites.forEach((sat) => {
      sat.visible = true;
      sat.mesh.material.uniforms.uAlpha.value = 1.0;
    });
  }

  let startBtn = null;
  const handleStartClick = () => {
    introOver = true;
    forceHideSatellites();
    blob.mesh.material.uniforms.uAlpha.value = 1.0;
  };

  const tryBindStart = () => {
    startBtn = document.getElementById('start-btn');
    if (startBtn) {
      startBtn.addEventListener('click', handleStartClick, { passive: true });
      return true;
    }
    return false;
  };

  if (!tryBindStart()) {
    const mo = new MutationObserver(() => { if (tryBindStart()) mo.disconnect(); });
    mo.observe(document.body, { childList: true, subtree: true });
    window.__threeStartObserver = mo;
  }

  function onResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  }
  window.addEventListener('resize', onResize);

  function onMouseMove(event) {
    mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
    mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
    targetCameraPos.x = mouse.x * 3;
    targetCameraPos.y = mouse.y * 2;
  }
  window.addEventListener('mousemove', onMouseMove);

  function animate() {
    if (disposed) return;
    const t = clock.getElapsedTime();

    camera.position.x += (targetCameraPos.x - camera.position.x) * 0.05;
    camera.position.y += (targetCameraPos.y - camera.position.y) * 0.05;
    camera.position.z = 18;
    camera.lookAt(0, 0, 0);

    blob.mesh.material.uniforms.uTime.value = t;
    satellites.forEach((sat, i) => {
      sat.mesh.material.uniforms.uTime.value = t;
      sat.position.y += Math.sin(t * 0.5 + i) * 0.002;
    });
    particles.material.uniforms.uTime.value = t;
    particles.rotation.y += 0.0003;

    const pUniforms = particles.material.uniforms;
    if (introOver) {
      pUniforms.uOpacity.value -= 0.02;
      if (pUniforms.uOpacity.value <= 0) particles.visible = false;
    } else {
      particles.visible = true;
      pUniforms.uOpacity.value = 1.0;
    }

    if (introOver) {
      blob.scale.lerp(new THREE.Vector3(1, 1, 1), 0.04);
      satellites.forEach((sat) => {
        sat.visible = true;
        sat.scale.lerp(new THREE.Vector3(0, 0, 0), 0.12);
        if (sat.scale.length() < 0.02) {
          sat.scale.set(0, 0, 0);
          sat.visible = false;
          sat.mesh.material.uniforms.uAlpha.value = 0.0;
        }
      });
    } else {
      blob.scale.lerp(new THREE.Vector3(0, 0, 0), 0.1);
      forceShowSatellites();
      satellites.forEach((sat) => {
        sat.scale.lerp(new THREE.Vector3(1, 1, 1), 0.1);
      });
    }

    renderer.render(scene, camera);
    rafId = requestAnimationFrame(animate);
  }

  animate();
}

document.addEventListener('DOMContentLoaded', initThreeScene);
