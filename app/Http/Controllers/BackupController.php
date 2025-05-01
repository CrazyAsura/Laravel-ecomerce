<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect(Storage::files('backups'))
            ->filter(function ($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'zip';
            })
            ->map(function ($file) {
                return (object) [
                    'id' => basename($file),
                    'created_at' => Carbon::createFromTimestamp(Storage::lastModified($file)),
                    'size' => $this->formatSize(Storage::size($file))
                ];
            })
            ->sortByDesc('created_at');

        return view('admin.backup.index', compact('backups'));
    }

    public function create(Request $request)
    {
        $backup_database = $request->has('backup_database');
        $backup_files = $request->has('backup_files');

        if (!$backup_database && !$backup_files) {
            return redirect()->back()->with('error', 'Selecione pelo menos uma opção de backup.');
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.zip';
        $path = 'backups/' . $filename;

        if ($backup_database) {
            $this->backupDatabase($path);
        }

        if ($backup_files) {
            $this->backupFiles($path);
        }

        return redirect()->back()->with('success', 'Backup criado com sucesso!');
    }

    public function download($id)
    {
        $path = 'backups/' . $id;

        if (!Storage::exists($path)) {
            return redirect()->back()->with('error', 'Arquivo de backup não encontrado.');
        }

        return Storage::download($path);
    }

    public function delete($id)
    {
        $path = 'backups/' . $id;

        if (!Storage::exists($path)) {
            return redirect()->back()->with('error', 'Arquivo de backup não encontrado.');
        }

        Storage::delete($path);
        return redirect()->back()->with('success', 'Backup excluído com sucesso!');
    }

    private function backupDatabase($path)
    {
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current', $tables);

        $sql = '';
        foreach ($tables as $table) {
            $create = DB::select("SHOW CREATE TABLE `$table`");
            $sql .= "\n\n" . $create[0]->{'Create Table'} . ";\n\n";

            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, (array) $row);

                $sql .= "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");\n";
            }
        }

        Storage::put($path, $sql);
    }

    private function backupFiles($path)
    {
        $files = collect(Storage::allFiles())
            ->reject(function ($file) {
                return str_starts_with($file, 'backups/');
            });

        $zip = new \ZipArchive();
        $zip->open(storage_path('app/' . $path), \ZipArchive::CREATE);

        foreach ($files as $file) {
            $zip->addFile(storage_path('app/' . $file), $file);
        }

        $zip->close();
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
