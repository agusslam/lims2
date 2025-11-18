<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\SampleRequest;
use App\Models\Sample;
use App\Models\Parameter;
use App\Models\SampleType;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Basic stats for all users
        $stats = [
            'total_sample_requests' => 0,
            'pending_samples' => 0,
            'testing_samples' => 0,
            'completed_samples' => 0,
            'my_assignments' => 0,
            'recent_activities' => collect()
        ];

        // Get stats based on user permissions
        if ($user->hasPermission(1)) { // List New Sample permission
            $stats['total_sample_requests'] = SampleRequest::count();
            $stats['pending_samples'] = SampleRequest::where('status', 'pending')->count();
        }

        if ($user->hasPermission(4)) { // Testing permission
            // Check if assigned_to column exists before querying
            if (Schema::hasColumn('samples', 'assigned_to')) {
                $stats['my_assignments'] = Sample::where('assigned_to', $user->id)
                    ->whereIn('status', ['assigned', 'testing'])
                    ->count();
            }
        }

        if ($user->hasAnyRole(['SUPERVISOR', 'ADMIN', 'DEVEL'])) {
            $stats['testing_samples'] = Sample::where('status', 'testing')->count();
            $stats['completed_samples'] = Sample::where('status', 'completed')->count();
        }

        // Recent activities based on role
        $stats['recent_activities'] = $this->getRecentActivities($user);

        // Quick stats for dashboard cards
        $quickStats = [
            'parameters' => Parameter::where('is_active', true)->count(),
            'sample_types' => SampleType::where('is_active', true)->count(),
            'active_users' => User::where('is_active', true)->count(),
            'today_requests' => SampleRequest::whereDate('created_at', today())->count()
        ];

        return view('dashboard', compact('stats', 'quickStats'));
    }

    /**
     * Get recent activities based on user role
     */
    private function getRecentActivities($user)
    {
        $activities = collect();

        // Get recent sample requests if user has permission
        if ($user->hasPermission(1)) {
            $recentRequests = SampleRequest::with(['sampleType'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($request) {
                    return [
                        'type' => 'sample_request',
                        'title' => 'Permohonan Sampel Baru',
                        'description' => "Kode: {$request->tracking_code} - {$request->contact_person}",
                        'timestamp' => $request->created_at,
                        'status' => $request->status,
                        'url' => route('sample-requests.show', $request->id)
                    ];
                });
            $activities = $activities->merge($recentRequests);
        }

        // Get assigned samples if user is analyst and column exists
        if ($user->hasPermission(4) && Schema::hasColumn('samples', 'assigned_to')) {
            $assignedSamples = Sample::where('assigned_to', $user->id)
                ->with(['sampleType'])
                ->orderBy('assigned_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($sample) {
                    return [
                        'type' => 'assignment',
                        'title' => 'Sampel Ditugaskan',
                        'description' => "Kode: {$sample->sample_code} - {$sample->customer_name}",
                        'timestamp' => $sample->assigned_at ?? $sample->created_at,
                        'status' => $sample->status,
                        'url' => '#' // Update with actual testing route when available
                    ];
                });
            $activities = $activities->merge($assignedSamples);
        }

        return $activities->sortByDesc('timestamp')->take(10);
    }
}
