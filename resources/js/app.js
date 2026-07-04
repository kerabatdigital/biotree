import Sortable from 'sortablejs';

// Drag-to-reorder for the link editor. SortableJS reorders the DOM, then we
// push the new id order to the Livewire component via $wire.reorder().
document.addEventListener('alpine:init', () => {
    window.Alpine.data('linkSortable', () => ({
        init() {
            Sortable.create(this.$el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'opacity-40',
                onEnd: () => {
                    const ids = Array.from(this.$el.querySelectorAll('[data-id]')).map(
                        (el) => el.dataset.id
                    );
                    this.$wire.reorder(ids);
                },
            });
        },
    }));
});
