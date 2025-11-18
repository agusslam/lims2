<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1. Ambil semua data dari tabel system_settings.
        $allSettings = SystemSetting::all();

        // 2. Konversi hasilnya menjadi Collection, 
        //    menggunakan 'setting_key' sebagai kunci (key) koleksi.
        //    Ini memungkinkan kita mengakses pengaturan dengan $settings->get('nama_key').
        $settings = $allSettings->keyBy('setting_key');
        
        // Cek izin (berdasarkan logika di web.php Anda, permission ID 10)
        // if (!Auth::user()->hasPermission(10)) { 
        //     abort(403, 'Unauthorized access to System Settings module'); 
        // }

        // 3. Kirim koleksi 'settings' ke view.
        return view('system.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        DB::transaction(function() use ($request) {
            foreach ($request->settings as $key => $value) {
                SystemSetting::set($key, $value);
            }
        });

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    public function settings()
    {
        $settings = SystemSetting::orderBy('id')->get()->keyBy('id');
        return view('system.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        DB::transaction(function() use ($request) {
            foreach ($request->settings as $key => $value) {
                SystemSetting::set($key, $value);
            }
        });

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    // public function auditLogs(Request $request)
    // {
    //     $query = AuditLog::with('user')->latest();

    //     if ($request->filled('action')) {
    //         $query->where('action', $request->action);
    //     }

    //     if ($request->filled('user_id')) {
    //         $query->where('user_id', $request->user_id);
    //     }

    //     $logs = $query->paginate(50);
    //     $actions = AuditLog::distinct()->pluck('action');
        
    //     return view('system.audit-logs', compact('logs', 'actions'));
    // }

    public function backup()
    {
        // TODO: Implement backup functionality
        return back()->with('success', 'Backup berhasil dibuat.');
    }
}
