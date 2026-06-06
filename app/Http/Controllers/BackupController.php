<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index()
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Only administrators can access backups.');
        }

        $disk = Storage::disk('local');
        $files = $disk->files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'name' => basename($file),
                    'size' => $this->formatSizeUnits($disk->size($file)),
                    'date' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                ];
            }
        }

        // Sort by date descending
        usort($backups, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Only administrators can access backups.');
        }

        try {
            Artisan::call('backup:database');
            return back()->with('success', 'Backup created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    public function download($fileNameEncoded)
    {
        $fileName = base64_decode($fileNameEncoded);
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Only administrators can access backups.');
        }

        $filePath = 'backups/' . $fileName;

        if (Storage::disk('local')->exists($filePath)) {
            return response()->download(storage_path('app/' . $filePath));
        }

        return back()->with('error', 'Backup file not found.');
    }

    public function destroy($fileNameEncoded)
    {
        $fileName = base64_decode($fileNameEncoded);
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Only administrators can access backups.');
        }

        $filePath = 'backups/' . $fileName;

        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
            return back()->with('success', 'Backup deleted successfully.');
        }

        return back()->with('error', 'Backup file not found.');
    }

    public function restore($fileNameEncoded)
    {
        $fileName = base64_decode($fileNameEncoded);
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Only administrators can access backups.');
        }

        $filePath = storage_path('app/backups/' . $fileName);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Backup file not found.');
        }

        try {
            $sql = file_get_contents($filePath);
            \Illuminate\Support\Facades\DB::unprepared($sql);
            return back()->with('success', 'Database restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore database. Error: ' . $e->getMessage());
        }
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
