<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Text to 3D — Chained Preview→Refine</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family: sans-serif; margin:20px; }
    #viewer { width:100%; height:600px; border:1px solid #ccc; margin-top:20px; }
    #timer  { margin-top:5px; font-style:italic; }
  </style>
</head>
<body>
  <h1>Generate 3D Model from Text Prompt</h1>

  <form id="promptForm">
    <input type="text" id="promptInput" placeholder="e.g. ghost mask" required>
    <button type="submit">Generate</button>
  </form>

  <div id="status" style="margin-top:10px;"></div>
  <div id="timer"></div>
  <a id="modelLink" href="#" target="_blank"
     style="display:none; margin-top:5px; display:block;"></a>
  <div id="viewer"></div>

  <script type="module">
    import * as THREE        from 'https://esm.sh/three@0.160.0';
    import { OrbitControls } from 'https://esm.sh/three@0.160.0/examples/jsm/controls/OrbitControls.js';
    import { GLTFLoader }    from 'https://esm.sh/three@0.160.0/examples/jsm/loaders/GLTFLoader.js';

    const form      = document.getElementById('promptForm');
    const statusEl  = document.getElementById('status');
    const timerEl   = document.getElementById('timer');
    const modelLink = document.getElementById('modelLink');
    const viewer    = document.getElementById('viewer');

    let scene, camera, renderer, controls;
    let startTime, timerInt;

    form.addEventListener('submit', async e => {
      e.preventDefault();
      // reset
      statusEl.textContent    = '⏳ Submitting preview…';
      timerEl.textContent     = '';
      modelLink.style.display = 'none';
      viewer.innerHTML        = '';
      clearInterval(timerInt);

      // start timer
      startTime = Date.now();
      timerInt  = setInterval(()=>{
        const s = Math.floor((Date.now()-startTime)/1000);
        timerEl.textContent = `⏱️ Elapsed: ${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
      },1000);

      const prompt = document.getElementById('promptInput').value;

      // 1) Preview
      const prevRes = await fetch('{{ route('meshy.preview3d') }}', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':'{{ csrf_token() }}',
        },
        body: JSON.stringify({prompt})
      });
      const { preview_task_id, error:prevErr } = await prevRes.json();
      if (!prevRes.ok || !preview_task_id) {
        clearInterval(timerInt);
        statusEl.textContent = `❌ Preview error: ${prevErr||'failed'}`;
        return;
      }
      statusEl.textContent = `📤 Preview queued (${preview_task_id}). Polling…`;

      // 2) Wait preview → SUCCEEDED
      await waitForStatus(preview_task_id);

      // 3) Refine
      statusEl.textContent = '⏳ Submitting refine…';
      const refRes = await fetch('{{ route('meshy.refine3d') }}', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':'{{ csrf_token() }}',
        },
        body: JSON.stringify({preview_task_id})
      });
      const { refine_task_id, error:refErr } = await refRes.json();
      if (!refRes.ok || !refine_task_id) {
        clearInterval(timerInt);
        statusEl.textContent = `❌ Refine error: ${refErr||'failed'}`;
        return;
      }
      statusEl.textContent = `📤 Refine queued (${refine_task_id}). Polling…`;

      // 4) Wait refine → SUCCEEDED & render
      await waitForStatus(refine_task_id, true);
    });

    // Poll until status SUCCEEDED; if final, load model
    async function waitForStatus(taskId, isFinal=false) {
      return new Promise(resolve => {
        const iv = setInterval(async () => {
          const r = await fetch(`/meshy/text-to-3d/status/${taskId}`);
          const d = await r.json();
          statusEl.textContent = `⏳ Status: ${d.status}`;
          if (d.status==='SUCCEEDED') {
            clearInterval(iv);
            if (isFinal) {
              clearInterval(timerInt);
              statusEl.innerHTML = '✅ Model Ready!';
              modelLink.href = d.model_url;
              modelLink.textContent = d.model_url;
              modelLink.style.display = 'block';
              loadModel(`/meshy/text-to-3d/proxy/${taskId}`);
            }
            resolve();
          }
          else if (d.status==='FAILED') {
            clearInterval(iv);
            clearInterval(timerInt);
            statusEl.textContent = '❌ Generation failed.';
            resolve();
          }
        },3000);
      });
    }

    function initViewer() {
      renderer = new THREE.WebGLRenderer({antialias:true});
      renderer.setSize(viewer.clientWidth,viewer.clientHeight);
      viewer.innerHTML = '';
      viewer.appendChild(renderer.domElement);

      scene = new THREE.Scene();
      scene.background = new THREE.Color(0xf0f0f0);

      camera = new THREE.PerspectiveCamera(
        45, viewer.clientWidth/viewer.clientHeight, 0.1, 1000
      );
      camera.position.set(0,1.5,3);

      scene.add(new THREE.AmbientLight(0xffffff,0.6));
      const d1 = new THREE.DirectionalLight(0xffffff,0.8);
      d1.position.set(5,10,7.5); scene.add(d1);
      const d2 = new THREE.DirectionalLight(0xffffff,0.3);
      d2.position.set(-5,-5,-5); scene.add(d2);

      controls = new OrbitControls(camera, renderer.domElement);
      controls.target.set(0,1,0);
      controls.update();

      animate();
    }

    function loadModel(url) {
      if (!renderer) initViewer();
      new GLTFLoader().load(url, gltf=>{
        while(scene.children.length>3) scene.remove(scene.children[3]);
        scene.add(gltf.scene);
      },undefined,err=>{
        console.error('GLB load error',err);
        statusEl.textContent += ' ❌ Failed to load model.';
      });
    }

    function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene,camera);
    }
  </script>
</body>
</html>
