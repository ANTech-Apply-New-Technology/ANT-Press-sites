/**
 * Makiro STL Viewer — Three.js based 3D model viewer
 */
(function() {
  'use strict';

  let scene, camera, renderer, controls, mesh, animationId;
  let autoRotate = false;
  let wireframe = false;
  let currentColor = 0xc8ff00;

  const container = document.getElementById('stlViewerContainer');
  const canvas = document.getElementById('stlCanvas');
  const overlay = document.getElementById('viewerOverlay');
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('stlFileInput');
  const viewerControls = document.getElementById('viewerControls');
  const modelInfo = document.getElementById('modelInfo');
  const viewerCta = document.getElementById('viewerCta');

  if (!container || !canvas) return;

  // Initialize Three.js scene
  function initScene() {
    scene = new THREE.Scene();

    var w = container.clientWidth;
    var h = container.clientHeight;

    camera = new THREE.PerspectiveCamera(45, w / h, 0.1, 2000);
    camera.position.set(0, 0, 5);

    renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x0a0a0a, 1);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.2;

    // Lights
    var ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
    scene.add(ambientLight);

    var dirLight1 = new THREE.DirectionalLight(0xffffff, 1.0);
    dirLight1.position.set(5, 8, 5);
    scene.add(dirLight1);

    var dirLight2 = new THREE.DirectionalLight(0xc8ff00, 0.3);
    dirLight2.position.set(-5, -3, -5);
    scene.add(dirLight2);

    var dirLight3 = new THREE.DirectionalLight(0xffffff, 0.5);
    dirLight3.position.set(0, -5, 5);
    scene.add(dirLight3);

    // Grid helper
    var gridHelper = new THREE.GridHelper(10, 20, 0x222222, 0x1a1a1a);
    gridHelper.position.y = -2;
    scene.add(gridHelper);

    // Orbit controls
    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.08;
    controls.enablePan = true;
    controls.minDistance = 1;
    controls.maxDistance = 50;

    animate();

    window.addEventListener('resize', onResize);
  }

  function onResize() {
    if (!renderer) return;
    var w = container.clientWidth;
    var h = container.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  }

  function animate() {
    animationId = requestAnimationFrame(animate);
    if (autoRotate && mesh) {
      mesh.rotation.y += 0.005;
    }
    if (controls) controls.update();
    if (renderer && scene && camera) renderer.render(scene, camera);
  }

  function loadSTL(buffer, filename) {
    // Remove old mesh
    if (mesh) {
      scene.remove(mesh);
      mesh.geometry.dispose();
      mesh.material.dispose();
    }

    var loader = new THREE.STLLoader();
    var geometry = loader.parse(buffer);

    geometry.computeVertexNormals();
    geometry.center();

    var material = new THREE.MeshPhysicalMaterial({
      color: currentColor,
      metalness: 0.1,
      roughness: 0.4,
      clearcoat: 0.3,
      clearcoatRoughness: 0.2,
      wireframe: wireframe,
      flatShading: false
    });

    mesh = new THREE.Mesh(geometry, material);

    // Scale to fit
    var box = new THREE.Box3().setFromObject(mesh);
    var size = box.getSize(new THREE.Vector3());
    var maxDim = Math.max(size.x, size.y, size.z);
    var scale = 3 / maxDim;
    mesh.scale.set(scale, scale, scale);

    scene.add(mesh);

    // Reset camera
    camera.position.set(0, 2, 5);
    controls.target.set(0, 0, 0);
    controls.update();

    // Show UI
    overlay.classList.add('hidden');
    viewerControls.style.display = 'flex';
    modelInfo.style.display = 'flex';
    viewerCta.style.display = 'flex';

    // Model info
    document.getElementById('modelName').textContent = filename;
    var fileSizeMB = (buffer.byteLength / (1024 * 1024)).toFixed(1);
    document.getElementById('modelSize').textContent = fileSizeMB + ' MB';

    // Estimate price (mock: based on volume)
    var volume = size.x * size.y * size.z * scale * scale * scale;
    var price = Math.max(99, Math.round(volume * 50 + 99));
    document.getElementById('estimatedPrice').textContent = price + ' kr';
  }

  function handleFile(file) {
    if (!file) return;
    var validTypes = ['.stl', '.obj'];
    var ext = '.' + file.name.split('.').pop().toLowerCase();
    if (validTypes.indexOf(ext) === -1) {
      alert('Filformat stöds ej. Använd .stl eller .obj');
      return;
    }
    if (file.size > 50 * 1024 * 1024) {
      alert('Filen är för stor. Max 50 MB.');
      return;
    }

    if (!scene) initScene();

    var reader = new FileReader();
    reader.onload = function(e) {
      loadSTL(e.target.result, file.name);
    };
    reader.readAsArrayBuffer(file);
  }

  // File input
  fileInput.addEventListener('change', function(e) {
    handleFile(e.target.files[0]);
  });

  // Drag & drop
  ['dragenter', 'dragover'].forEach(function(evt) {
    dropZone.addEventListener(evt, function(e) {
      e.preventDefault();
      e.stopPropagation();
      dropZone.classList.add('dragover');
    });
  });

  ['dragleave', 'drop'].forEach(function(evt) {
    dropZone.addEventListener(evt, function(e) {
      e.preventDefault();
      e.stopPropagation();
      dropZone.classList.remove('dragover');
    });
  });

  dropZone.addEventListener('drop', function(e) {
    var files = e.dataTransfer.files;
    if (files.length > 0) handleFile(files[0]);
  });

  // Prevent default drag on the whole container
  container.addEventListener('dragover', function(e) { e.preventDefault(); });
  container.addEventListener('drop', function(e) {
    e.preventDefault();
    var files = e.dataTransfer.files;
    if (files.length > 0) handleFile(files[0]);
  });

  // Controls
  document.getElementById('btnResetView').addEventListener('click', function() {
    camera.position.set(0, 2, 5);
    controls.target.set(0, 0, 0);
    controls.update();
    if (mesh) {
      mesh.rotation.set(0, 0, 0);
    }
  });

  document.getElementById('btnWireframe').addEventListener('click', function() {
    wireframe = !wireframe;
    this.classList.toggle('active', wireframe);
    if (mesh) {
      mesh.material.wireframe = wireframe;
    }
  });

  document.getElementById('btnAutoRotate').addEventListener('click', function() {
    autoRotate = !autoRotate;
    this.classList.toggle('active', autoRotate);
  });

  // Color picker
  document.querySelectorAll('.color-dot').forEach(function(dot) {
    dot.addEventListener('click', function() {
      document.querySelectorAll('.color-dot').forEach(function(d) { d.classList.remove('active'); });
      this.classList.add('active');
      currentColor = parseInt(this.dataset.color.replace('#', '0x'));
      if (mesh) {
        mesh.material.color.setHex(currentColor);
      }
    });
  });

  // Load demo model on click (generate a simple parametric shape)
  // This creates a cool demo without needing an external STL file
  function loadDemoModel() {
    if (!scene) initScene();

    if (mesh) {
      scene.remove(mesh);
      mesh.geometry.dispose();
      mesh.material.dispose();
    }

    // Create a cool geometric vase shape
    var points = [];
    for (var i = 0; i < 30; i++) {
      var t = i / 29;
      var r = 0.5 + 0.4 * Math.sin(t * Math.PI) + 0.15 * Math.sin(t * Math.PI * 4);
      points.push(new THREE.Vector2(r, t * 3 - 1.5));
    }
    var geometry = new THREE.LatheGeometry(points, 64);
    geometry.computeVertexNormals();

    var material = new THREE.MeshPhysicalMaterial({
      color: currentColor,
      metalness: 0.1,
      roughness: 0.4,
      clearcoat: 0.3,
      clearcoatRoughness: 0.2,
      wireframe: wireframe,
      flatShading: false
    });

    mesh = new THREE.Mesh(geometry, material);
    scene.add(mesh);

    camera.position.set(0, 1, 4);
    controls.target.set(0, 0, 0);
    controls.update();
    autoRotate = true;
    var btn = document.getElementById('btnAutoRotate');
    if (btn) btn.classList.add('active');

    overlay.classList.add('hidden');
    viewerControls.style.display = 'flex';
    modelInfo.style.display = 'flex';
    viewerCta.style.display = 'flex';

    document.getElementById('modelName').textContent = 'Geometric Vase (demo)';
    document.getElementById('modelSize').textContent = 'Parametric';
    document.getElementById('estimatedPrice').textContent = '349 kr';
  }

  // Add demo button to drop zone
  var demoLink = document.createElement('button');
  demoLink.className = 'btn btn-ghost btn-sm';
  demoLink.textContent = 'Eller testa med demomodell';
  demoLink.style.marginTop = '8px';
  demoLink.addEventListener('click', function(e) {
    e.preventDefault();
    loadDemoModel();
  });
  dropZone.appendChild(demoLink);

})();
