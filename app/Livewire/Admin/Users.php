<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public string $statusFilter = 'all';
    public string $planFilter = 'all';
    public string $roleFilter = 'all';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 25;

    public ?int $selectedUserId = null;
    public bool $showActionsModal = false;
    public string $actionType = '';
    public ?string $actionNotes = null;

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function mount(): void
    {
        $this->perPage = request()->get('per_page', 25);
    }

    public function render()
    {
        $users = $this->getUsers();

        return view('livewire.admin.users', [
            'users' => $users,
        ])->layout('layouts.admin', ['title' => 'Users']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPlanFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getUsers()
    {
        $query = User::query()
            ->with('profile');

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('profile', function ($q) {
                        $q->where('username', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Plan filter
        if ($this->planFilter !== 'all') {
            $query->where('plan', $this->planFilter);
        }

        // Role filter
        if ($this->roleFilter !== 'all') {
            $query->where('role', $this->roleFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function openActionsModal(int $userId, string $action): void
    {
        $this->selectedUserId = $userId;
        $this->actionType = $action;
        $this->actionNotes = null;
        $this->showActionsModal = true;
    }

    public function closeActionsModal(): void
    {
        $this->showActionsModal = false;
        $this->selectedUserId = null;
        $this->actionType = '';
        $this->actionNotes = null;
    }

    public function confirmAction(): void
    {
        if (!$this->selectedUserId || !$this->actionType) {
            return;
        }

        $user = User::findOrFail($this->selectedUserId);
        $adminId = auth()->id();
        $oldValues = [];

        switch ($this->actionType) {
            case 'suspend':
                $oldValues = ['status' => $user->status];
                $user->update(['status' => 'suspended']);
                AuditLog::log($adminId, 'suspend_user', $user, $oldValues, ['status' => 'suspended']);
                session()->flash('message', 'User suspended successfully.');
                break;

            case 'restore':
                $oldValues = ['status' => $user->status];
                $user->update(['status' => 'active']);
                AuditLog::log($adminId, 'restore_user', $user, $oldValues, ['status' => 'active']);
                session()->flash('message', 'User restored successfully.');
                break;

            case 'make_admin':
                $oldValues = ['role' => $user->role];
                $user->update(['role' => 'admin']);
                AuditLog::log($adminId, 'make_admin', $user, $oldValues, ['role' => 'admin']);
                session()->flash('message', 'User promoted to admin.');
                break;

            case 'remove_admin':
                $oldValues = ['role' => $user->role];
                $user->update(['role' => 'user']);
                AuditLog::log($adminId, 'remove_admin', $user, $oldValues, ['role' => 'user']);
                session()->flash('message', 'Admin role removed.');
                break;
        }

        $this->closeActionsModal();
        $this->dispatch('refreshUsers');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->planFilter = 'all';
        $this->roleFilter = 'all';
        $this->resetPage();
    }

    public function getSelectedUserProperty()
    {
        if (!$this->selectedUserId) {
            return null;
        }

        return User::with('profile')->find($this->selectedUserId);
    }

    public function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'active' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
            'suspended' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        };
    }

    public function getPlanBadgeClass(string $plan): string
    {
        return match ($plan) {
            'free' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
            'pro' => 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        };
    }

    public function getRoleBadgeClass(string $role): string
    {
        return match ($role) {
            'admin' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
            'user' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        };
    }
}
