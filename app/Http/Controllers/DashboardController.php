<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SampleRequest;
use App\Models\Sample;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $data = [
            'stats' => $this->getStats($user),
            'recent_samples' => $this->getRecentSamples($user),
            'pending_tasks' => $this->getPendingTasks($user),
            'chart_data' => $this->getChartData($user)
        ];

        return view('dashboard.index', $data);
    }

    private function getStats($user)
    {
        $stats = [];
        
        if ($user->hasAnyRole(['SUPERVISOR', 'ADMIN', 'DEVEL'])) {
            $stats['total_requests'] = SampleRequest::count();
            $stats['pending_requests'] = SampleRequest::where('status', 'pending')->count();
            $stats['completed_requests'] = SampleRequest::where('status', 'completed')->count();
            $stats['active_customers'] = Customer::where('is_verified', true)->count();
        }
        
        if ($user->hasAnyRole(['ANALYST', 'SUPERVISOR_ANALYST'])) {
            $stats['assigned_samples'] = Sample::where('assigned_analyst_id', $user->id)
                ->whereIn('status', ['assigned', 'testing'])->count();
            $stats['completed_tests'] = Sample::where('assigned_analyst_id', $user->id)
                ->where('status', 'completed')->count();
        }
        
        if ($user->hasAnyRole(['TECH_AUDITOR', 'QUALITY_AUDITOR'])) {
            $stats['pending_reviews'] = Sample::whereIn('status', ['review_tech', 'review_quality'])->count();
        }

        return $stats;
    }

    private function getRecentSamples($user)
    {
        $query = Sample::with(['sampleRequest.customer', 'assignedAnalyst']);
        
        if ($user->hasRole('ANALYST')) {
            $query->where('assigned_analyst_id', $user->id);
        }
        
        return $query->orderBy('created_at', 'desc')->limit(10)->get();
    }

    private function getPendingTasks($user)
    {
        $tasks = [];
        
        if ($user->hasRole('ADMIN')) {
            $tasks['new_requests'] = SampleRequest::where('status', 'pending')->count();
            $tasks['needs_verification'] = Customer::where('is_verified', false)->count();
        }
        
        if ($user->hasRole('SUPERVISOR')) {
            $tasks['needs_assignment'] = Sample::where('status', 'registered')->count();
            $tasks['pending_certificates'] = Sample::where('status', 'validated')->count();
        }
        
        if ($user->hasRole('ANALYST')) {
            $tasks['assigned_tests'] = Sample::where('assigned_analyst_id', $user->id)
                ->where('status', 'assigned')->count();
        }
        
        if ($user->hasAnyRole(['TECH_AUDITOR', 'QUALITY_AUDITOR'])) {
            $tasks['pending_reviews'] = Sample::whereIn('status', ['review_tech', 'review_quality'])->count();
        }

        return $tasks;
    }

    private function getChartData($user)
    {
        $days = 7;
        $dates = collect(range(0, $days-1))->map(function($day) {
            return Carbon::now()->subDays($day)->format('Y-m-d');
        })->reverse()->values();

        $samples_data = $dates->map(function($date) {
            return Sample::whereDate('created_at', $date)->count();
        });

        return [
            'labels' => $dates->map(function($date) {
                return Carbon::parse($date)->format('d/m');
            }),
            'samples' => $samples_data
        ];
    }

    public function profile()
    {
        return view('dashboard.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        Auth::user()->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('dashboard.profile')
                        ->with('success', 'Profil berhasil diperbarui.');
    }
}
