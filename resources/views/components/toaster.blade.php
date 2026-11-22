<div x-data="{
        toasts: [],
        push(detail) {
            const id = String(Date.now() + Math.random());
            this.toasts.push({ id, type: detail?.type ?? 'success', message: detail?.message ?? '' });
            setTimeout(() => this.dismiss(id), 4500);
        },
        dismiss(id) { this.toasts = this.toasts.filter(t => t.id !== id); },
     }"
     x-on:notify.window="push($event.detail)"
     class="pointer-events-none fixed inset-x-0 bottom-0 z-50 flex flex-col items-center gap-2 p-4 sm:items-end sm:p-6"
     aria-live="polite">
    <template x-for="toast in toasts" :key="toast.id">
        <div role="status"
             class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-lg border bg-surface p-4 shadow-lg animate-pop motion-reduce:animate-none"
             :class="toast.type === 'error' ? 'border-danger/40' : 'border-basil/30'">
            <svg x-show="toast.type !== 'error'" class="mt-0.5 size-5 shrink-0 text-basil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>
            <svg x-show="toast.type === 'error'" class="mt-0.5 size-5 shrink-0 text-danger" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 8v5M12 16.5v.01"/><circle cx="12" cy="12" r="9"/></svg>
            <p class="flex-1 text-sm" x-text="toast.message"></p>
            <button type="button" x-on:click="dismiss(toast.id)" aria-label="Dismiss"
                    class="-m-1 rounded-md p-1 text-muted hover:text-ink focus-visible:outline-2 focus-visible:outline-basil">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </template>
</div>
