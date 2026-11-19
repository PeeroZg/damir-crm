import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
const locale = () => document.documentElement.lang || 'en';

export function formatMoney(value, currency = 'EUR') {
    return new Intl.NumberFormat(locale() === 'hr' ? 'hr-HR' : 'en-IE', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(Number(value) || 0);
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

/* ------------------------------------------------------------------ toasts */

Alpine.store('toasts', {
    items: [],
    push(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => this.dismiss(id), 4500);
    },
    dismiss(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
});

window.toast = (message, type) => Alpine.store('toasts').push(message, type);

/* ------------------------------------------------------------------ kanban */

/**
 * Drag & drop board. Works with mouse and touch (SortableJS); the PATCH request
 * is sent as JSON and the card is moved back if the server refuses the change.
 */
Alpine.data('kanbanBoard', ({ url, field, reasonFor = null, currency = 'EUR' }) => ({
    pending: null,
    reason: '',

    init() {
        this.$el.querySelectorAll('[data-kanban-list]').forEach((list) => {
            Sortable.create(list, {
                group: 'kanban',
                draggable: '[data-id]',
                animation: 160,
                delay: 120,
                delayOnTouchOnly: true,
                ghostClass: 'kanban-ghost',
                chosenClass: 'kanban-chosen',
                filter: '[data-locked]',
                preventOnFilter: false,
                onEnd: (evt) => this.dropped(evt),
            });
        });
        this.refreshTotals();
    },

    dropped(evt) {
        if (evt.from === evt.to) return;

        const move = {
            item: evt.item,
            from: evt.from,
            oldIndex: evt.oldIndex,
            id: evt.item.dataset.id,
            status: evt.to.dataset.status,
        };

        if (reasonFor && move.status === reasonFor) {
            this.pending = move;
            this.reason = '';
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
        this.revert(this.pending);
        this.pending = null;
    },

    async send(move, extra = {}) {
        move.item.classList.add('opacity-60');
        try {
            const data = await jsonRequest(url.replace('__ID__', move.id), 'PATCH', { [field]: move.status, ...extra });
            window.toast(data.message);
        } catch (error) {
            this.revert(move);
            window.toast(error.status === 403 ? error.message : error.message || 'Error', 'error');
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
            const count = column.querySelector('[data-count]');
            const total = column.querySelector('[data-total]');
            const empty = column.querySelector('[data-empty]');

            if (count) count.textContent = cards.length;
            if (total) total.textContent = formatMoney(cards.reduce((sum, c) => sum + Number(c.dataset.value || 0), 0), currency);
            if (empty) empty.hidden = cards.length > 0;
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
        if (this.query.trim().length < 2) {
            return actions;
        }
        return this.results;
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
        this.$nextTick(() => this.$refs.list?.querySelector('[data-active="true"]')?.scrollIntoView({ block: 'nearest' }));
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
        if (!this.items.length) this.add();
    },

    add() {
        this.items.push({ key: Math.random(), description: '', quantity: 1, unit: 'pcs', unit_price: 0, discount_percent: 0 });
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
        return Math.round(this.subtotal * this.vatRate) / 100;
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

/** Toggle a task without leaving the page. */
Alpine.data('taskToggle', ({ url, completed }) => ({
    completed,
    async toggle() {
        try {
            const data = await jsonRequest(url, 'PATCH');
            this.completed = data.completed;
            window.toast(data.message);
        } catch (error) {
            window.toast(error.message, 'error');
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();
