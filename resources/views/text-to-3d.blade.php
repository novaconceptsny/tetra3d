<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Text to 3D with Three.js (ESM.sh Modules)</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family: sans-serif; margin: 20px; }
    #viewer { width: 100%; height: 600px; border: 1px solid #ccc; margin-top: 20px; }
  </style>
</head>
<body>
  <h1>Generate 3D Model from Text Prompt</h1>

  <form id="promptForm">
    <input type="text" id="promptInput" placeholder="e.g. ghost mask" required>
    <button type="submit">Generate</button>
  </form>

  <div id="status" style="margin-top: 10px;"></div>
  <a id="modelLink" href="#" target="_blank" style="display:none; margin-top:5px; display:block;"></a>
  <div id="viewer"></div>

  <script type="module">
    // All imports come from esm.sh at exactly three@0.160.0
    import * as THREE        from 'https://esm.sh/three@0.160.0';
    import { OrbitControls } from 'https://esm.sh/three@0.160.0/examples/jsm/controls/OrbitControls.js';
    import { GLTFLoader }    from 'https://esm.sh/three@0.160.0/examples/jsm/loaders/GLTFLoader.js';

    const form      = document.getElementById('promptForm');
    const statusEl  = document.getElementById('status');
    const viewer    = document.getElementById('viewer');
    const modelLink = document.getElementById('modelLink');
    let scene, camera, renderer, controls;

    form.addEventListener('submit', async e => {
      e.preventDefault();
      statusEl.textContent = '⏳ Submitting prompt…';
      viewer.innerHTML = '';
      modelLink.style.display = 'none';

      const prompt = document.getElementById('promptInput').value;
      const res = await fetch("{{ route('meshy.textTo3d') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ prompt })
      });
      const { task_id, message } = await res.json();
      if (!res.ok || !task_id) {
        statusEl.textContent = `❌ ${message || 'Task creation failed'}`;
        return;
      }
      statusEl.textContent = `📤 Task created: ${task_id}`;
      pollStatus(task_id);
    });

    async function pollStatus(id) {
      statusEl.textContent = '🔁 Waiting for model…';
      const iv = setInterval(async () => {
        try {
          const r = await fetch(`/meshy/text-to-3d/status/${id}`);
          if (!r.ok) throw new Error(await r.text());
          const data = await r.json();

          if (data.status === 'SUCCEEDED') {
            clearInterval(iv);
            statusEl.innerHTML = '✅ Model Generated!';
            modelLink.href = data.model_url;
            modelLink.textContent = data.model_url;
            modelLink.style.display = 'block';
            loadModel(`/meshy/model-proxy/${id}`);
          }
          else if (data.status === 'FAILED') {
            clearInterval(iv);
            statusEl.textContent = '❌ Generation failed.';
          }
          else {
            statusEl.textContent = `⏳ Status: ${data.status}`;
          }
        } catch(err) {
          clearInterval(iv);
          console.error(err);
          statusEl.textContent = '❌ Error polling status.';
        }
      }, 3000);
    }

    function initViewer() {
      renderer = new THREE.WebGLRenderer({ antialias: true });
      renderer.setSize(viewer.clientWidth, viewer.clientHeight);
      viewer.innerHTML = '';
      viewer.appendChild(renderer.domElement);

      scene = new THREE.Scene();
      scene.background = new THREE.Color(0xf0f0f0);

      camera = new THREE.PerspectiveCamera(45, viewer.clientWidth / viewer.clientHeight, 0.1, 1000);
      camera.position.set(0, 1.5, 3);

      // — ADDED LIGHTS HERE —
      // 1) soft global ambient
      const ambient = new THREE.AmbientLight(0xffffff, 0.6);
      scene.add(ambient);

      // 2) directional “sun” light for nice shading
      const dir = new THREE.DirectionalLight(0xffffff, 0.8);
      dir.position.set(5, 10, 7.5);
      dir.castShadow = true;
      scene.add(dir);

      // Optional 3) a fill light from below
      const fill = new THREE.DirectionalLight(0xffffff, 0.3);
      fill.position.set(-5, -5, -5);
      scene.add(fill);
      // — END LIGHTS —

      controls = new OrbitControls(camera, renderer.domElement);
      controls.target.set(0, 1, 0);
      controls.update();

      animate();
    }

    function loadModel(url) {
      if (!renderer) initViewer();
      const loader = new GLTFLoader();
      loader.load(
        url,
        gltf => {
          while (scene.children.length > 2) scene.remove(scene.children[2]);
          scene.add(gltf.scene);
        },
        undefined,
        err => {
          console.error('GLB load error', err);
          statusEl.textContent += ' ❌ Failed to load model.';
        }
      );
    }

    function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, camera);
    }
  </script>
</body>
</html>
