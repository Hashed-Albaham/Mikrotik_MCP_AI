<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Router;
use App\Models\AiProvider;
use App\Models\VoucherBatch;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalRouters' => Router::count(),
            'totalProviders' => AiProvider::count(),
            'activeProviders' => AiProvider::where('is_active', true)->count(),
            'totalVouchers' => VoucherBatch::sum('count'),
        ])->layout('layouts.app');
    }
}
