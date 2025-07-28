<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Canvas Debug</title>
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
      background: #111;
      color: #0f0;
    }
    #overlay {
      position: absolute;
      top: 0;
      left: 0;
      padding: 10px;
      background: rgba(0,0,0,0.8);
      font-size: 14px;
      z-index: 1000;
    }
    canvas {
      background: #333;
      display: block;
      width: 100vw;
      height: 100vh;
    }
    button {
      margin-top: 10px;
      padding: 6px 12px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div id="overlay">
    <pre id="info">Loading...</pre>
    <button onclick="forceResize()">Force Resize</button>
  </div>
  <canvas id="testCanvas"></canvas>

  <script>
    const canvas = document.getElementById('testCanvas');
    const info = document.getElementById('info');

    function updateInfo() {
      const ctx = canvas.getContext('2d');
      canvas.width = window.innerWidth * window.devicePixelRatio;
      canvas.height = window.innerHeight * window.devicePixelRatio;

      ctx.fillStyle = "#fff";
      ctx.font = "32px sans-serif";
      ctx.fillText("Canvas Rendered", 20, 50);

      info.textContent =
        `window.innerWidth: ${window.innerWidth}\n` +
        `window.innerHeight: ${window.innerHeight}\n` +
        `screen.width: ${screen.width}\n` +
        `screen.height: ${screen.height}\n` +
        `devicePixelRatio: ${window.devicePixelRatio}\n` +
        `canvas.width: ${canvas.width}\n` +
        `canvas.height: ${canvas.height}\n` +
        `orientation: ${screen.orientation?.type || "N/A"} (${screen.orientation?.angle || 0}°)`;
    }

    function forceResize() {
      updateInfo();
    }

    window.addEventListener('resize', updateInfo);
    window.addEventListener('orientationchange', updateInfo);

    // Initial call
    updateInfo();
  </script>
</body>
</html>
