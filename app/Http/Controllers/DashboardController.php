<?php

namespace App\Http\Controllers;

use App\Models\NightSession;
use App\Models\Product;
use App\Models\Staff;
use App\Services\NightSessionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected NightSessionService $sessionService;

    public function __construct(NightSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function index(Request $request)
    {
        $session = $this->sessionService->resolveSession($request->get('session_id'));
        $allSessions = NightSession::orderByDesc('session_date')->get();

        $closing = null;
        if ($session) {
            $closing = $this->sessionService->recalculateClosing($session);
        }

        $totalProducts = Product::where('is_active', true)->count();
        $totalStaff = Staff::where('is_active', true)->count();

        return view('dashboard.index', compact('session', 'allSessions', 'closing', 'totalProducts', 'totalStaff'));
    }
}
