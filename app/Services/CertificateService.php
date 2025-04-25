<?php 

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Certificate;

class CertificateService
{
    public function processUpload($request, $user)
    {
        $file = $request->file('file');
        $hash = hash_file('sha256', $file->getRealPath());
        $timestamp = Carbon::now('UTC');
        $certificate_id = Str::uuid();

        $filename = $file->getClientOriginalName();
        $preview_url = null;

        if ($request->boolean('store_file')) {
            $path = $file->store("certificates/{$user->id}", 'public');
           
            $preview_url = Storage::url($path);
        }

        // Placeholder for future blockchain integration
        $blockchain_tx = $request->boolean('blockchain') ? 'pending-blockchain-tx' : null;

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'certificate_id' => $certificate_id,
            'file_name' => $filename,
            'sha256_hash' => $hash,
            'timestamp' => $timestamp,
            'blockchain_tx' => $blockchain_tx,
            'project_name' => $request->project_name,
            'description' => $request->description,
            'language' => $request->language ?? 'en',
            'preview_url' => $preview_url,
        ]);

        return [
            'certificate_id' => $certificate_id,
            'sha256_hash' => $hash,
            'timestamp' => $timestamp,
            'blockchain_tx' => $blockchain_tx,
            'download_url' => $preview_url,
        ];
    }
}
