<?php

namespace App\Livewire\App;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Subscription')]
class SubscriptionDashboard extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public function render()
    {
        $user = auth()->user();

        return view('livewire.app.subscription-dashboard', [
            'subscription' => $user->subscription,
            'payments' => $user->payments()->latest()->paginate(10),
        ]);
    }
}
