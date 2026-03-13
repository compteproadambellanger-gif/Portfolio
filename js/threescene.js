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
  const blob = new Blob(4, 0.3, 0.6, 4.5, 0.5);
  blob.scale.set(0, 0, 0);
  scene.add(blob);

  blob.scale.set(1, 1, 1);
  blob.mesh.material.uniforms.uAlpha.value = 1.0;

  const mouse = { x: 0, y: 0 };
  const targetCameraPos = { x: 0, y: 0 };

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

    renderer.render(scene, camera);
    rafId = requestAnimationFrame(animate);
  }

  animate();
}

document.addEventListener('DOMContentLoaded', initThreeScene);
