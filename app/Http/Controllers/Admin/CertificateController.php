<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('user')->get();
        return response()->json($certificates);
    }

    public function search(Request $request)
    {
        $query = Certificate::query();

        if ($request->has('sha256_hash')) {
            $query->where('sha256_hash', 'like', '%' . $request->sha256_hash . '%');
        }

        if ($request->has('project_name')) {
            $query->where('project_name', 'like', '%' . $request->project_name . '%');
        }

        $certificates = $query->get();
        return response()->json($certificates);
    }

    public function show($certificate_id)
    {
        $certificate = Certificate::where('certificate_id', $certificate_id)->firstOrFail();
        return response()->json($certificate);
    }

    public function updateStatus(Request $request, $certificate_id)
    {
        $request->validate([
            'status' => 'required|in:approved,denied',
        ]);

        $certificate = Certificate::where('certificate_id', $certificate_id)->firstOrFail();

        $certificate->status = $request->status;
        $certificate->save();

        return response()->json(['message' => 'Certificate status updated successfully']);
    }


    public function downloadReport()
    {
        $certificates = Certificate::all();

        $filename = storage_path('app/public/audit_reports/audit_report_' . now()->format('Y_m_d_H_i_s') . '.csv');

        if (!file_exists(storage_path('app/public/audit_reports'))) {
            mkdir(storage_path('app/public/audit_reports'), 0777, true);
        }

        $handle = fopen($filename, 'w');

        fputcsv($handle, ['Certificate ID', 'User ID', 'File Name', 'SHA256 Hash', 'Timestamp', 'Blockchain TX', 'Status']);

        foreach ($certificates as $certificate) {
            fputcsv($handle, [
                $certificate->certificate_id,
                $certificate->user_id,
                $certificate->file_name,
                $certificate->sha256_hash,
                $certificate->timestamp,
                $certificate->blockchain_tx,
                $certificate->status,
            ]);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
