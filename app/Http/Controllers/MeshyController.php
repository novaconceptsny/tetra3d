<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeshyController extends Controller
{
    // 1) Preview pass: generate bare mesh
    public function preview3d(Request $request)
    {
        $request->validate(['prompt'=>'required|string|max:255']);
        $base = [
            'prompt'          => $request->prompt,
            'negative_prompt' => 'low quality, low resolution, low poly, ugly',
            'art_style'       => 'realistic',
            'should_remesh'   => true,
        ];
        $resp = Http::withToken(env('MESHY_API_KEY'))
            ->withOptions(['verify'=>false])
            ->post('https://api.meshy.ai/openapi/v2/text-to-3d',
                   array_merge($base, ['mode'=>'preview']));
        if (! $resp->successful()) {
            Log::error('Preview failed', ['body'=>$resp->body()]);
            return response()->json(['error'=>'preview_failed'],500);
        }
        return response()->json([
            'status'           => 'preview-created',
            'preview_task_id'  => $resp->json()['result'],
        ]);
    }

    // 2) Refine pass: texture the mesh
    public function refine3d(Request $request)
    {
        $request->validate(['preview_task_id'=>'required|string']);
        $resp = Http::withToken(env('MESHY_API_KEY'))
            ->withOptions(['verify'=>false])
            ->post('https://api.meshy.ai/openapi/v2/text-to-3d', [
                'mode'            => 'refine',
                'preview_task_id' => $request->preview_task_id,
                'texture_size'    => 2048,
                'unwrap_uv'       => true,
                'should_reproject'=> true,
            ]);
        if (! $resp->successful()) {
            Log::error('Refine failed', ['body'=>$resp->body()]);
            return response()->json(['error'=>'refine_failed'],500);
        }
        return response()->json([
            'status'          => 'refine-created',
            'refine_task_id'  => $resp->json()['result'],
        ]);
    }

    // 3) Poll status for either preview or refine
    public function checkStatus(string $taskId)
    {
        $resp = Http::withToken(env('MESHY_API_KEY'))
            ->withOptions(['verify'=>false])
            ->get("https://api.meshy.ai/openapi/v2/text-to-3d/{$taskId}");
        if (! $resp->successful()) {
            Log::error('Status check failed', ['body'=>$resp->body()]);
            return response()->json(['status'=>'error'],500);
        }
        $d = $resp->json();
        return response()->json([
            'status'    => $d['status'],
            'model_url' => $d['model_urls']['glb'] 
                         ?? $d['model_urls']['gltf'] ?? null,
        ]);
    }

    // 4) Proxy the final GLB to avoid CORS
    public function proxyModel(string $taskId)
    {
        $status = Http::withToken(env('MESHY_API_KEY'))
            ->withOptions(['verify'=>false])
            ->get("https://api.meshy.ai/openapi/v2/text-to-3d/{$taskId}")
            ->json();

        if (($status['status'] ?? '') !== 'SUCCEEDED'
            || empty($status['model_urls']['glb'])) {
            abort(404,'Model not ready');
        }

        $file = Http::withOptions(['verify'=>false])
            ->get($status['model_urls']['glb']);

        if (! $file->successful()) abort(404,'Fetch failed');

        return response($file->body(),200)
            ->header('Content-Type','model/gltf-binary')
            ->header('Access-Control-Allow-Origin','*');
    }

///////////below is for image-to-3d
    public function showImageForm()
    {
        return view('image-to-3d');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image_file' => 'required|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $imageData = base64_encode(file_get_contents($request->file('image_file')));
        $mime = $request->file('image_file')->getMimeType();
        $dataUri = "data:$mime;base64,$imageData";

        $response = Http::withToken(env('MESHY_API_KEY'))
            ->withOptions(['verify' => false])
            ->post('https://api.meshy.ai/openapi/v1/image-to-3d', [
                'image_url' => $dataUri,
                'enable_pbr' => true,
                'target_polycount'=> 300000,
                'should_remesh' => true,
                'should_texture' => true,
                'ai_model'       => 'meshy-4',
            ]);

        if ($response->failed()) {
            Log::error('Meshy API error: ' . $response->body()); // Add this
            return back()->with('error', 'Failed to send image to Meshy API');
        }

        $taskId = $response->json('result');
        Log::info('Redirecting with taskId: ' . $taskId);
        return redirect()->route('image-to-3d.form', ['taskId' => $taskId]);
    }

    public function fetchImageStatus(Request $request)
    {
        $taskId = $request->query('taskId');

        $response = Http::withToken(env('MESHY_API_KEY'))
            // Uncomment below line ONLY for local SSL issues
            ->withOptions(['verify' => false])
            ->get("https://api.meshy.ai/openapi/v1/image-to-3d/{$taskId}");

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch status'], 500);
        }

        return response()->json($response->json());
    }












}
