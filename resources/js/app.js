import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Sortable from 'sortablejs';

const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
const locale = () => (document.documentElement.lang === 'hr' ? 'hr-HR' : 'en-IE');

export function formatMoney(value, currency = 'EUR') {
    return new Intl.NumberFormat(locale(), { style: 'currency', currency, minimumFractionDigits: 2 }).format(Number(value) || 0);
}

async function jsonRequest(url, method, body) {
    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: body ? JSON.stringify(body) : undefined,
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const error = new Error(data.message || response.statusText);
        error.status = response.status;
        throw error;
    }

    return data;
}

/* ------------------------------------------------------------------ toasts
   $store.toasts.push({ type: 'success'|'won'|'error', title, body }) */

Alpine.store('toasts', {
    items: [],
    push(toast) {
        const id = Date.now() + Math.random();
        this.items.push({ id, type: 'success', body: '', ...toast });
        setTimeout(() => this.dismiss(id), toast.timeout ?? 4500);
    },
    dismiss(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
});

/** window.toast('Saved') or window.toast({ type: 'won', title, body }) */
window.toast = (toast, type = 'success') =>
    Alpine.store('toasts').push(typeof toast === 'string' ? { title: toast, type } : toast);

/* ---------------------------------------------------------------- confetti */

window.dcrmConfetti = (x, y) => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const colors = ['#D4FA4E', '#1A1A18', '#1F9A55', '#5B61F0', '#C27A00'];
    for (let i = 0; i < 28; i++) {
        const p = document.createElement('span');
        const a = (Math.PI * 2 * i) / 28 + Math.random() * 0.4;
        const d = 60 + Math.random() * 90;
        p.style.cssText = `position:fixed;left:${x}px;top:${y}px;width:7px;height:${Math.random() > 0.5 ? 7 : 12}px;border-radius:2px;pointer-events:none;z-index:70;background:${colors[i % colors.length]};--x:${Math.cos(a) * d}px;--y:${Math.sin(a) * d + 40}px`;
        p.className = 'animate-confetti';
        document.body.appendChild(p);
        setTimeout(() => p.remove(), 1000);
    }
};

/* --------------------------------------------------------------- app shell */

Alpine.data('app', () => ({
    nav: false,
}));

/* ------------------------------------------------------------------ kanban */

/**
 * Drag & drop board (SortableJS: mouse + touch). The PATCH request is sent as JSON;
 * the card goes back to where it was if the server refuses the move.
 */
Alpine.data('kanbanBoard', ({ url, field, reasonFor = null, celebrate = null, currency = 'EUR' }) => ({
    pending: null,
    reason: '',
    pendingTitle: '',

    init() {
        this.$el.querySelectorAll('[data-kanban-list]').forEach((list) => {
            Sortable.create(list, {
                group: 'kanban',
                draggable: '[data-id]',
                animation: 160,
                delay: 120,
                delayOnTouchOnly: true,
                ghostClass: 'kb-ghost',
                chosenClass: 'kb-chosen',
                filter: '[data-locked], select',
                preventOnFilter: false,
                onEnd: (evt) => this.dropped(evt),
            });
        });
        this.refreshTotals();
    },

    dropped(evt) {
        if (evt.from === evt.to) return;

        const rect = evt.item.getBoundingClientRect();
        const move = {
            item: evt.item,
            from: evt.from,
            oldIndex: evt.oldIndex,
            id: evt.item.dataset.id,
            status: evt.to.dataset.status,
            x: rect.left + rect.width / 2,
            y: rect.top + 24,
        };

        if (reasonFor && move.status === reasonFor) {
            this.pending = move;
            this.reason = '';
            this.pendingTitle = evt.item.dataset.title || '';
            this.refreshTotals();
            this.$nextTick(() => this.$refs.reason?.focus());
            return;
        }

        this.send(move);
    },

    confirmReason() {
        const move = this.pending;
        this.pending = null;
        this.send(move, { lost_reason: this.reason || null });
    },

    cancelReason() {
        if (!this.pending) return;
        this.revert(this.pending);
        this.pending = null;
    },

    async send(move, extra = {}) {
        move.item.classList.add('opacity-60');
        this.refreshTotals();
        try {
            const data = await jsonRequest(url.replace('__ID__', move.id), 'PATCH', { [field]: move.status, ...extra });
            if (celebrate && move.status === celebrate) window.dcrmConfetti(move.x, move.y);
            window.toast(data.toast ?? data.message);
        } catch (error) {
            this.revert(move);
            window.toast({ type: 'error', title: error.message || 'Error' });
        } finally {
            move.item.classList.remove('opacity-60');
            this.refreshTotals();
        }
    },

    revert(move) {
        const reference = move.from.children[move.oldIndex] ?? null;
        move.from.insertBefore(move.item, reference);
        this.refreshTotals();
    },

    /** Mobile fallback: the <select> on each card. */
    moveWithSelect(select) {
        const card = select.closest('[data-id]');
        const target = this.$el.querySelector(`[data-kanban-list][data-status="${select.value}"]`);
        if (!target || target === card.parentElement) return;
        const from = card.parentElement;
        const oldIndex = [...from.children].indexOf(card);
        target.prepend(card);
        this.dropped({ from, to: target, item: card, oldIndex });
    },

    refreshTotals() {
        this.$el.querySelectorAll('[data-kanban-list]').forEach((list) => {
            const cards = [...list.querySelectorAll(':scope > [data-id]')];
            const column = list.closest('[data-kanban-column]');
            const hiddenCount = Number(column.dataset.hiddenCount || 0);
            const hiddenValue = Number(column.dataset.hiddenValue || 0);
            const count = column.querySelector('[data-count]');
            const total = column.querySelector('[data-total]');
            const empty = column.querySelector('[data-empty]');

            if (count) count.textContent = cards.length + hiddenCount;
            if (total) total.textContent = formatMoney(cards.reduce((sum, c) => sum + Number(c.dataset.value || 0), 0) + hiddenValue, currency);
            if (empty) empty.hidden = cards.length + hiddenCount > 0;
            cards.forEach((c) => {
                const select = c.querySelector('select[data-move]');
                if (select) select.value = list.dataset.status;
            });
        });
    },
}));

/* ---------------------------------------------------------- command palette */

Alpine.data('commandPalette', ({ url, actions }) => ({
    open: false,
    query: '',
    results: [],
    active: 0,
    loading: false,
    controller: null,

    get items() {
        return this.query.trim().length < 2 ? actions : this.results;
    },

    /** Show a group heading above the first item of each group. */
    heading(index) {
        const item = this.items[index];
        return index === 0 || this.items[index - 1].group !== item.group ? item.group : null;
    },

    show() {
        this.open = true;
        this.$nextTick(() => this.$refs.input.focus());
    },

    hide() {
        this.open = false;
        this.query = '';
        this.results = [];
        this.active = 0;
    },

    async search() {
        this.active = 0;
        if (this.query.trim().length < 2) {
            this.results = [];
            return;
        }

        this.controller?.abort();
        this.controller = new AbortController();
        this.loading = true;

        try {
            const response = await fetch(`${url}?q=${encodeURIComponent(this.query)}`, {
                headers: { Accept: 'application/json' },
                signal: this.controller.signal,
            });
            this.results = (await response.json()).results;
        } catch (e) {
            if (e.name !== 'AbortError') this.results = [];
        } finally {
            this.loading = false;
        }
    },

    move(step) {
        const count = this.items.length;
        if (!count) return;
        this.active = (this.active + step + count) % count;
        this.$nextTick(() => this.$refs.list?.querySelector('[aria-selected="true"]')?.scrollIntoView({ block: 'nearest' }));
    },

    go(item = this.items[this.active]) {
        if (item) window.location.href = item.url;
    },
}));

/* --------------------------------------------------------------- offer form */

Alpine.data('offerForm', ({ items, vatRate, currency = 'EUR' }) => ({
    items: items.length ? items.map((i) => ({ ...i, key: Math.random() })) : [],
    vatRate: Number(vatRate),

    init() {
        if (!this.items.length) this.add(false);
    },

    add(focus = true) {
        this.items.push({ key: Math.random(), description: '', quantity: 1, unit: 'pcs', unit_price: 0, discount_percent: 0 });
        if (!focus) return;
        this.$nextTick(() => {
            const inputs = this.$el.querySelectorAll('[data-description]');
            inputs[inputs.length - 1]?.focus();
        });
    },

    remove(index) {
        this.items.splice(index, 1);
        if (!this.items.length) this.add();
    },

    line(item) {
        const total = Number(item.quantity || 0) * Number(item.unit_price || 0) * (1 - Number(item.discount_percent || 0) / 100);
        return Math.round(total * 100) / 100;
    },

    get subtotal() {
        return this.items.reduce((sum, item) => sum + this.line(item), 0);
    },

    get vat() {
        return Math.round(this.subtotal * (Number(this.vatRate) || 0)) / 100;
    },

    get total() {
        return this.subtotal + this.vat;
    },

    money(value) {
        return formatMoney(value, currency);
    },
}));

/* ------------------------------------------------------------------- misc */

Alpine.data('clipboard', (text) => ({
    copied: false,
    async copy() {
        await navigator.clipboard.writeText(text);
        this.copied = true;
        setTimeout(() => (this.copied = false), 2000);
    },
}));

/** Tick a task off without leaving the page. */
Alpine.data('taskToggle', ({ url, completed }) => ({
    completed,
    async toggle() {
        try {
            const data = await jsonRequest(url, 'PATCH');
            this.completed = data.completed;
            window.toast(data.message);
        } catch (error) {
            window.toast({ type: 'error', title: error.message });
        }
    },
}));

window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();
