<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\Dashboard\StockResumeService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private StockResumeService $stockResumeService)
    {
    }

    public function index(Team $current_team): Response
    {
        $stockData = $this->stockResumeService->getSummary($current_team);

        return Inertia::render('Dashboard',
        [
        'stockResumeData' => $stockData,
        'fkasasp' => 'kflsfasl'

        ]);
    }
}
