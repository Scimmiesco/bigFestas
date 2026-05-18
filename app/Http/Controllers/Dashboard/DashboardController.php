<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\Dashboard\StockResumeService;
use App\Services\Payment\PaymentResumeService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
    private StockResumeService $stockResumeService,
    private PaymentResumeService $paymentResumeService)
    {
    }

    public function index(Team $current_team): Response
    {
        $paymentData = $this->paymentResumeService->getSummary($current_team);
        $stockData = $this->stockResumeService->getSummary($current_team);

        return Inertia::render('dashboard/Dashboard',
        [
        'stockResumeData' => $stockData,
        'paymentData' => $paymentData
        ]);
    }
}
