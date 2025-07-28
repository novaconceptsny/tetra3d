<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image to 3D</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md bg-white p-6 rounded-2xl shadow">
    @if(request()->has('taskId'))
        <h1 class="text-xl font-bold mb-4">3D Model Status</h1>

        <p id="statusText" class="mb-2">Checking status...</p>
        <p>Elapsed time: <span id="timer">0s</span></p>

        <div id="resultArea" class="mt-4 hidden">
            <p>3D model is ready!</p>
            <a id="downloadLink" href="#" target="_blank" class="text-blue-600 underline">Download GLB</a>
        </div>

        <script>
            const taskId = "{{ request()->get('taskId') }}";
            const statusText = document.getElementById('statusText');
            const resultArea = document.getElementById('resultArea');
            const downloadLink = document.getElementById('downloadLink');
            const timerDisplay = document.getElementById('timer');

            let secondsElapsed = 0;
            const timer = setInterval(() => {
                secondsElapsed++;
                timerDisplay.textContent = `${secondsElapsed}s`;
            }, 1000);

            const poll = async () => {
                try {
                    const res = await fetch(`{{ route('image-to-3d.status') }}?taskId=${taskId}`);
                    const data = await res.json();

                    if (data.status === 'SUCCEEDED') {
                        statusText.textContent = '✅ Model generation succeeded!';
                        clearInterval(timer);
                        resultArea.classList.remove('hidden');
                        downloadLink.href = data.model_urls.glb;
                    } else if (data.status === 'FAILED') {
                        statusText.textContent = '❌ Model generation failed.';
                        clearInterval(timer);
                    } else {
                        statusText.textContent = `⏳ Status: ${data.status}...`;
                        setTimeout(poll, 3000);
                    }
                } catch (err) {
                    statusText.textContent = '⚠️ Error fetching status.';
                    clearInterval(timer);
                }
            };

            poll();
        </script>

    @else
        <h1 class="text-xl font-bold mb-4">Upload Image for 3D Model</h1>

        @if(session('error'))
            <div class="text-red-500 mb-2">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('image-to-3d.upload') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="image_file" required accept="image/jpeg,image/png" class="mb-4 w-full">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Upload</button>
        </form>
    @endif
</div>
</body>
</html>