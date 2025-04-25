<?php
namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CertificateController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $validated = $request->validate([
                'file'         => 'required|file|max:10240',
                'project_name' => 'required|string',
                'description'  => 'required|string',
                'language'     => 'required|string|in:en,es,fr,de,it,ar,he',
                'blockchain'   => 'required|boolean',
                'store_file'   => 'required|boolean',
            ]);
        } catch (ValidationException $e) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422));
        }

        $user = $request->user();

        $certificate = (new CertificateService())->processUpload($request, $user);

        return response()->json([
            'success' => true,
            'message' => 'Certificate created successfully.',
            'data'    => $certificate,
        ], 201);
    }

    public function verifyById($certificate_id)
    {
        $certificate = Certificate::where('certificate_id', $certificate_id)->first();

        if (! $certificate) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json([
            'valid'         => true,
            'file_name'     => $certificate->file_name,
            'timestamp'     => $certificate->timestamp,
            'blockchain_tx' => $certificate->blockchain_tx,
            'hash'          => $certificate->sha256_hash,
            'preview_url'   => $certificate->preview_url,
        ]);
    }

    public function verifyByFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240',
            ]);
        } catch (ValidationException $e) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422));
        }

        $uploadedHash = hash_file('sha256', $request->file('file')->getRealPath());

        $match = Certificate::where('sha256_hash', $uploadedHash)->first();

        if ($match) {
            return response()->json([
                'match'          => true,
                'certificate_id' => $match->certificate_id,
                'timestamp'      => $match->timestamp,
                'blockchain_tx'  => $match->blockchain_tx,
            ]);
        }

        return response()->json(['match' => false]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json(
            Certificate::where('user_id', $user->id)->get()
        );
    }
}
