<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Addresses\AddressController;
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
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('itens', [ItemController::class, 'index'])->name('items.index');
        Route::get('itens/criar', [ItemController::class, 'create'])->name('items.create');
        Route::post('itens', [ItemController::class, 'store'])->name('items.store');
        Route::get('itens/{id}/editar', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('itens/{id}', [ItemController::class, 'update'])->name('items.update');
        Route::put('/itens/lote', [ItemController::class, 'bulkUpdate'])->name('items.bulkUpdate');
        Route::delete('/itens/{id}', [ItemController::class, 'destroy'])->name('items.destroy');


        // ...suas outras rotas (edit, update, destroy)
    
        Route::get('locacoes', [RentalController::class, 'index'])->name('rentals.index');
        Route::get('locacoes/criar', [RentalController::class, 'create'])->name('rentals.create');
        Route::post('locacoes', [RentalController::class, 'store'])->name('rentals.store');
        Route::get('locacoes/{id}', [RentalController::class, 'show'])->name('rentals.show');

        Route::get('pagamentos', [PaymentController::class, 'index'])->name('payment.index');
        Route::get('pagamentos/criar', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('pagamentos', [PaymentController::class, 'store'])->name('payment.store');

        Route::get('clientes', [ClientController::class, 'index'])->name('clients.index');
        Route::get('clientes/criar', [ClientController::class, 'create'])->name('clients.create');
        Route::get('clientes/{client}/editar', [ClientController::class, 'edit'])->name('clients.edit');
        Route::post('clientes', [ClientController::class, 'store'])->name('clients.store');
        Route::put('clientes/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('clientes/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        Route::get('enderecos', [AddressController::class, 'index'])->name('addresses.index');
        Route::get('enderecos/criar', [AddressController::class, 'create'])->name('addresses.create');
        Route::post('enderecos', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('enderecos/{address}', [AddressController::class, 'show'])->name('addresses.show');
        Route::get('enderecos/{address}/editar', [AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('enderecos/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('enderecos/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__ . '/settings.php';
