<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Items\ItemController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Rentals\RentalController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard',[DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('itens', [ItemController::class, 'index'])->name('items.index');
        Route::get('itens/criar', [ItemController::class, 'create'])->name('items.create');
        Route::post('itens', [ItemController::class, 'store'])->name('items.store');

        Route::get('locacoes', [RentalController::class, 'index'])->name('rentals.index');
        Route::get('locacoes/criar', [RentalController::class, 'create'])->name('rentals.create');
        Route::post('locacoes', [RentalController::class, 'store'])->name('rentals.store');
        Route::get('locacoes/{id}', [RentalController::class, 'show'])->name('rentals.show');

        Route::get('pagamentos', [PaymentController::class, 'index'])->name('payment.index');
        Route::get('pagamentos/criar', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('pagamentos', [PaymentController::class, 'store'])->name('payment.store');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/settings.php';
