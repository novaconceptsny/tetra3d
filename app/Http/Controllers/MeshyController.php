<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MeshyController extends Controller
{
    public function textTo3d(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        try {
            $prompt = $request->input('prompt');

            $payload = [
                'mode' => 'preview',
                'prompt' => $prompt,
                'negative_prompt' => 'low quality, low resolution, low poly, ugly',
                'art_style' => 'realistic',
                'should_remesh' => true
            ];

            $response = Http::withToken(env('MESHY_API_KEY'))
                ->withOptions(['verify' => false])
                ->post('https://api.meshy.ai/openapi/v2/text-to-3d', $payload);

            if ($response->successful()) {
                $taskId = $response->json()['result'];

                return response()->json([
                    'status' => 'task-created',
                    'task_id' => $taskId
                ]);
            }

            Log::error('Meshy v2 API Error:', ['body' => $response->body()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Meshy API error',
                'details' => $response->json()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Exception in textTo3d:', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function checkStatus($taskId)
    {
        try {
            $response = Http::withToken(env('MESHY_API_KEY'))
                ->withOptions(['verify' => false])
                ->get("https://api.meshy.ai/openapi/v2/text-to-3d/{$taskId}");

            if (!$response->successful()) {
                Log::error('Meshy checkStatus error', ['body' => $response->body()]);
                return response()->json(['status'=>'error','message'=>'API error'], 500);
            }

            $data = $response->json();
            return response()->json([
                'status' => $data['status'],
                'model_url' => $data['model_urls']['glb'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in checkStatus', ['error'=>$e->getMessage()]);
            return response()->json(['status'=>'error','message'=>'Internal server error'], 500);
        }
    }
    
    public function proxyModel(string $taskId)
    {
        // 1) Get the status & URL
        $status = Http::withToken(env('MESHY_API_KEY'))
            ->withOptions(['verify' => false])
            ->get("https://api.meshy.ai/openapi/v2/text-to-3d/{$taskId}")
            ->json();

        if (($status['status'] ?? null) !== 'SUCCEEDED') {
            abort(404, 'Model not ready');
        }

        $url = $status['model_urls']['glb'] 
            ?? abort(404, 'No GLB URL');

        // 2) Fetch the binary
        $file = Http::withOptions(['verify' => false])
            ->get($url);

        if (! $file->successful()) {
            abort(404, 'Failed to fetch GLB');
        }

        // 3) Stream it back with CORS header
        return response($file->body(), 200)
            ->header('Content-Type', 'model/gltf-binary')
            ->header('Access-Control-Allow-Origin', '*');
    }


}


?>