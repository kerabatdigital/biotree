<?php

namespace App\Livewire\Admin;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $statusFilter = 'open';
    public string $reasonFilter = 'all';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 25;

    public ?int $selectedReportId = null;
    public bool $showActionModal = false;
    public string $actionType = '';
    public ?string $actionNotes = null;

    protected $listeners = ['refreshReports' => '$refresh'];

    public function mount(): void
    {
        $this->perPage = request()->get('per_page', 25);
    }

    public function render()
    {
        $reports = $this->getReports();

        return view('livewire.admin.reports', [
            'reports' => $reports,
            'counts' => $this->counts,
        ])->layout('layouts.admin', ['title' => 'Reports']);
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedReasonFilter(): void
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

    public function getReports()
    {
        $query = Report::query()
            ->with(['reportable', 'handler']);

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Reason filter
        if ($this->reasonFilter !== 'all') {
            $query->where('reason', $this->reasonFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function openActionModal(int $reportId, string $action): void
    {
        $this->selectedReportId = $reportId;
        $this->actionType = $action;
        $this->actionNotes = null;
        $this->showActionModal = true;
    }

    public function closeActionModal(): void
    {
        $this->showActionModal = false;
        $this->selectedReportId = null;
        $this->actionType = '';
        $this->actionNotes = null;
    }

    public function confirmAction(): void
    {
        if (!$this->selectedReportId || !$this->actionType) {
            return;
        }

        $report = Report::findOrFail($this->selectedReportId);
        $adminId = auth()->id();

        switch ($this->actionType) {
            case 'dismiss':
                $report->dismiss($adminId, $this->actionNotes);
                session()->flash('message', 'Report dismissed.');
                break;

            case 'action':
                $report->action($adminId, $this->actionNotes);
                session()->flash('message', 'Action taken on the reported content.');
                break;

            case 'review':
                $report->markAsReviewed($adminId);
                session()->flash('message', 'Report marked as reviewed.');
                break;
        }

        $this->closeActionModal();
        $this->dispatch('refreshReports');
    }

    public function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'open' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
            'reviewed' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300',
            'actioned' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
            'dismissed' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        };
    }

    public function getReasonBadgeClass(string $reason): string
    {
        return match ($reason) {
            'phishing' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
            'spam' => 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300',
            'inappropriate' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300',
            'harassment' => 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
            'copyright' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
            'fraud' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
            'other' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        };
    }

    public function getSelectedReportProperty()
    {
        if (!$this->selectedReportId) {
            return null;
        }

        return Report::with(['reportable', 'handler'])->find($this->selectedReportId);
    }

    public function clearFilters(): void
    {
        $this->statusFilter = 'open';
        $this->reasonFilter = 'all';
        $this->resetPage();
    }

    public function getCountsProperty(): array
    {
        return [
            'open' => Report::where('status', 'open')->count(),
            'reviewed' => Report::where('status', 'reviewed')->count(),
            'actioned' => Report::where('status', 'actioned')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
            'total' => Report::count(),
        ];
    }
}
