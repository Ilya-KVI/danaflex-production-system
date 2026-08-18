<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    statistics: {
        type: Object,
        default: () => ({
            total: 0,
            inProduction: 0,
            completed: 0,
            queued: 0,
            productionVolume: 0,
        }),
    },
    orders: {
        type: Array,
        default: () => [],
    },
    lines: {
        type: Array,
        default: () => [],
    },
    workQueue: {
        type: Array,
        default: () => [],
    },
})

const queueTab = ref('production')

const activeLines = computed(() => {
    return props.lines.filter(line => line.status === 'Работает').length
})

const averageLoad = computed(() => {
    if (!props.lines.length) {
        return 0
    }

    const total = props.lines.reduce(
        (sum, line) => sum + Number(line.load_percent || 0),
        0
    )

    return Math.round(total / props.lines.length)
})

const queueSource = computed(() => {
    return props.workQueue.length ? props.workQueue : props.orders
})

const productionOrders = computed(() => {
    return queueSource.value.filter(
        order => order.status === 'В производстве'
    )
})

const waitingOrders = computed(() => {
    return queueSource.value.filter(
        order => order.status === 'Новый'
    )
})

const visibleQueue = computed(() => {
    return queueTab.value === 'production'
        ? productionOrders.value
        : waitingOrders.value
})

const waitingCount = computed(() => {
    return Number.isFinite(Number(props.statistics.queued))
        ? Number(props.statistics.queued)
        : waitingOrders.value.length
})

const productionVolume = computed(() => {
    if (
        props.statistics.productionVolume !== undefined &&
        props.statistics.productionVolume !== null
    ) {
        return Number(props.statistics.productionVolume) || 0
    }

    return productionOrders.value.reduce(
        (total, order) => total + Number(order.quantity || 0),
        0
    )
})

const warningLines = computed(() => {
    return props.lines.filter(
        line =>
            Number(line.temperature) > 45 ||
            Number(line.load_percent) >= 90
    )
})

const hasEquipmentWarning = computed(() => warningLines.value.length > 0)

function formatDate(date) {
    if (!date) {
        return '—'
    }

    return new Date(date).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

function formatQuantity(quantity) {
    return new Intl.NumberFormat('ru-RU').format(quantity ?? 0)
}

function changeStatus(id, status) {
    router.post(
        `/production-orders/${id}/status`,
        { status },
        { preserveScroll: true }
    )
}

function startLine(id) {
    router.post(
        `/production-lines/${id}/start`,
        {},
        { preserveScroll: true }
    )
}

function stopLine(id) {
    router.post(
        `/production-lines/${id}/stop`,
        {},
        { preserveScroll: true }
    )
}

function deleteOrder(id) {
    if (!confirm('Удалить этот заказ?')) {
        return
    }

    router.delete(`/production-orders/${id}`, {
        preserveScroll: true,
    })
}

function createOrder() {
    router.visit('/production-orders/create')
}

function openOrders() {
    router.visit('/production-orders')
}

function openOrder(id) {
    router.visit(`/production-orders/${id}`)
}

function getOrderStatusClasses(status) {
    if (status === 'В производстве') {
        return 'bg-blue-50 text-blue-700 ring-blue-600/20'
    }

    if (status === 'Готово') {
        return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
    }

    return 'bg-slate-100 text-slate-700 ring-slate-600/10'
}

function getLineStatusClasses(status) {
    if (status === 'Работает') {
        return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
    }

    return 'bg-rose-50 text-rose-700 ring-rose-600/20'
}

function getTemperatureClasses(temperature) {
    const value = Number(temperature)

    if (value > 45) {
        return 'text-rose-600'
    }

    if (value > 40) {
        return 'text-amber-600'
    }

    return 'text-emerald-600'
}

function getLoadBarClasses(load) {
    const value = Number(load)

    if (value >= 90) {
        return 'bg-rose-500'
    }

    if (value >= 70) {
        return 'bg-blue-600'
    }

    if (value > 0) {
        return 'bg-emerald-500'
    }

    return 'bg-slate-300'
}
</script>

<template>
    <!-- DANAFLEX DASHBOARD OPERATOR V4 -->
    <div class="min-h-screen bg-gradient-to-b from-[#eef5ff] via-[#f4f8ff] to-[#edf4fb] text-slate-900">
        <!-- TOP / HERO -->
        <header
            class="relative overflow-hidden border-b border-slate-800 bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white"
        >
            <div
                class="pointer-events-none absolute -right-28 -top-36 h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute left-1/3 top-0 h-64 w-64 rounded-full bg-cyan-400/5 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-[1600px] px-6 py-6 lg:px-10">
                <div class="flex flex-col justify-between gap-6 xl:flex-row xl:items-center">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-cyan-300 shadow-lg shadow-blue-950/30 backdrop-blur"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-6 w-6"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 21h18M5 21V9l5 3V9l5 3V5h4v16M8 17h1m3 0h1m3 0h1"
                                />
                            </svg>
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-xl font-bold tracking-tight sm:text-2xl">
                                    Danaflex
                                </h1>

                                <span
                                    class="rounded-md border border-white/10 bg-white/5 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-300"
                                >
                                    Production System
                                </span>
                            </div>

                            <p class="mt-1 text-sm text-slate-400">
                                Рабочий центр производственного контроля
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div
                            class="inline-flex items-center gap-2 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-2.5 text-sm font-semibold text-emerald-300"
                        >
                            <span class="relative flex h-2.5 w-2.5">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40"
                                ></span>
                                <span
                                    class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"
                                ></span>
                            </span>

                            Система работает
                        </div>

                        <button
                            @click="createOrder"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/20 transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-4 w-4"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                            </svg>

                            Новый заказ
                        </button>
                    </div>
                </div>

                <div class="mt-8 flex flex-col justify-between gap-5 pb-3 lg:flex-row lg:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">
                            Операционный центр
                        </p>

                        <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
                            Производство в реальном времени
                        </h2>

                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-400">
                            Состояние линий, текущая загрузка и производственная очередь —
                            на одном рабочем экране.
                        </p>
                    </div>

                    <button
                        @click="openOrders"
                        class="inline-flex self-start items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 backdrop-blur transition hover:bg-white/10 lg:self-auto"
                    >
                        Все заказы
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            class="h-4 w-4"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1600px] px-6 py-7 lg:px-10">
            <!-- KPI -->
            <section class="mb-7 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-blue-600"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Всего заказов</p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                                {{ statistics.total }}
                            </p>
                            <p class="mt-3 text-xs text-slate-400">За весь период</p>
                        </div>

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-5 w-5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 7h12M6 12h12M6 17h7M4 4h16v16H4z"
                                />
                            </svg>
                        </div>
                    </div>
                </article>

                <article
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-amber-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">В производстве</p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                                {{ statistics.inProduction }}
                            </p>
                            <p class="mt-3 text-xs text-slate-400">Активные заказы</p>
                        </div>

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-5 w-5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 12h4l2-5 4 10 2-5h4"
                                />
                            </svg>
                        </div>
                    </div>
                </article>

                <article
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-emerald-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Завершено</p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                                {{ statistics.completed }}
                            </p>
                            <p class="mt-3 text-xs text-slate-400">Выполненные заказы</p>
                        </div>

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-5 w-5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                            </svg>
                        </div>
                    </div>
                </article>

                <article
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-violet-500"></div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Средняя загрузка</p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-950">
                                {{ averageLoad }}%
                            </p>
                            <p class="mt-3 text-xs text-slate-400">
                                {{ activeLines }} из {{ lines.length }} линий работают
                            </p>
                        </div>

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 ring-1 ring-violet-100"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-5 w-5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m5 10V5m5 14v-7m5 7V3" />
                            </svg>
                        </div>
                    </div>
                </article>
            </section>

            <!-- OPERATIONS WORKSPACE -->
            <section
                class="mb-7 overflow-hidden rounded-[28px] border border-blue-200 bg-gradient-to-br from-blue-100 via-sky-50 to-indigo-100 p-5 shadow-lg shadow-blue-950/5 sm:p-6"
            >
                <div class="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">
                            Оборудование и очередь
                        </p>
                        <h3 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">
                            Оперативная производственная зона
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">
                            Состояние оборудования, текущая загрузка и очередь заказов — без переходов между экранами.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white/90 px-3.5 py-2.5 text-xs font-semibold text-emerald-700 shadow-sm"
                        >
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ activeLines }} из {{ lines.length }} линий активны
                        </span>

                        <span
                            v-if="hasEquipmentWarning"
                            class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs font-semibold text-rose-700 shadow-sm"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-4 w-4"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.6 2.7 18a1.5 1.5 0 0 0 1.3 2.2h16a1.5 1.5 0 0 0 1.3-2.2L13.7 4.6a1.5 1.5 0 0 0-3.4 0Z" />
                            </svg>
                            {{ warningLines.length }} требует внимания
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1.2fr)_minmax(430px,0.8fr)]">
                    <!-- EQUIPMENT -->
                    <div class="space-y-4">
                        <div
                            v-if="lines.length"
                            class="grid grid-cols-1 gap-4"
                        >
                            <article
                                v-for="line in lines"
                                :key="line.id"
                                class="overflow-hidden rounded-2xl border border-blue-200 bg-white/95 shadow-md shadow-blue-950/5"
                            >
                                <div class="h-1 bg-gradient-to-r from-cyan-400 via-blue-600 to-indigo-600"></div>

                                <div class="p-5 sm:p-6">
                                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                                        <div class="flex items-center gap-3.5">
                                            <div
                                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-cyan-300 shadow-sm"
                                            >
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    class="h-5 w-5"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M8 8h8v8H8zM4 12H2m20 0h-2M12 4V2m0 20v-2M5.6 5.6 4.2 4.2m15.6 15.6-1.4-1.4m0-12.8 1.4-1.4M4.2 19.8l1.4-1.4"
                                                    />
                                                </svg>
                                            </div>

                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="text-lg font-bold text-slate-950">
                                                        {{ line.name }}
                                                    </h4>
                                                    <span class="rounded-md bg-blue-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-wider text-blue-600">
                                                        ID #{{ line.id }}
                                                    </span>
                                                </div>
                                                <p class="mt-1 text-sm text-slate-500">
                                                    Материал: <span class="font-semibold text-slate-700">{{ line.material || 'не выбран' }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <span
                                            class="inline-flex self-start items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset"
                                            :class="getLineStatusClasses(line.status)"
                                        >
                                            <span
                                                class="h-1.5 w-1.5 rounded-full"
                                                :class="line.status === 'Работает' ? 'bg-emerald-500' : 'bg-rose-500'"
                                            ></span>
                                            {{ line.status }}
                                        </span>
                                    </div>

                                    <div
                                        v-if="Number(line.temperature) > 45 || Number(line.load_percent) >= 90"
                                        class="mt-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5"
                                    >
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-rose-600 shadow-sm">
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                class="h-4 w-4"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.6 2.7 18a1.5 1.5 0 0 0 1.3 2.2h16a1.5 1.5 0 0 0 1.3-2.2L13.7 4.6a1.5 1.5 0 0 0-3.4 0Z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide text-rose-700">
                                                Требует внимания
                                            </p>
                                            <p class="mt-1 text-xs leading-5 text-rose-600/90">
                                                <span v-if="Number(line.temperature) > 45">
                                                    Температура выше рабочего порога.
                                                </span>
                                                <span v-if="Number(line.temperature) > 45 && Number(line.load_percent) >= 90"> </span>
                                                <span v-if="Number(line.load_percent) >= 90">
                                                    Нагрузка оборудования достигла критического уровня.
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- OPERATOR METRICS -->
                                    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                                Температура
                                            </p>
                                            <p class="mt-1.5 text-2xl font-bold" :class="getTemperatureClasses(line.temperature)">
                                                {{ line.temperature }}°C
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                                Загрузка
                                            </p>
                                            <p class="mt-1.5 text-2xl font-bold text-slate-950">
                                                {{ line.load_percent }}%
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3.5">
                                            <p class="text-[11px] font-semibold uppercase tracking-wider text-blue-500">
                                                Заказов в работе
                                            </p>
                                            <p class="mt-1.5 text-2xl font-bold text-blue-700">
                                                {{ productionOrders.length }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5">
                                            <p class="text-[11px] font-semibold uppercase tracking-wider text-amber-600">
                                                Ожидают запуска
                                            </p>
                                            <p class="mt-1.5 text-2xl font-bold text-amber-700">
                                                {{ waitingCount }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- LOAD -->
                                    <div class="mt-5 rounded-xl border border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50 px-4 py-4">
                                        <div class="mb-2.5 flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-xs font-semibold text-slate-700">
                                                    Производственная нагрузка
                                                </p>
                                                <p class="mt-0.5 text-[11px] text-slate-500">
                                                    Текущая загрузка оборудования относительно рабочего диапазона
                                                </p>
                                            </div>
                                            <span class="rounded-lg bg-white px-2.5 py-1 text-xs font-bold text-slate-700 shadow-sm">
                                                {{ line.load_percent }}/100
                                            </span>
                                        </div>

                                        <div class="h-2.5 overflow-hidden rounded-full bg-white shadow-inner">
                                            <div
                                                class="h-full rounded-full transition-all duration-500"
                                                :class="getLoadBarClasses(line.load_percent)"
                                                :style="{
                                                    width: Math.min(Number(line.load_percent) || 0, 100) + '%',
                                                }"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- CURRENT PRODUCTION SNAPSHOT -->
                                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                                        <div class="rounded-xl border border-slate-200 bg-slate-950 px-4 py-4 text-white">
                                            <div class="flex flex-wrap items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-cyan-300">
                                                        Текущая производственная сводка
                                                    </p>
                                                    <p class="mt-2 text-sm font-semibold text-white">
                                                        {{ productionOrders.length }} заказ(а) сейчас в производстве
                                                    </p>
                                                    <p class="mt-1 text-xs text-slate-400">
                                                        Общий активный объём: {{ formatQuantity(productionVolume) }} ед.
                                                    </p>
                                                </div>

                                                <button
                                                    v-if="productionOrders.length"
                                                    @click="openOrder(productionOrders[0].id)"
                                                    class="rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/15"
                                                >
                                                    Открыть текущий заказ
                                                </button>
                                            </div>

                                            <div
                                                v-if="productionOrders.length"
                                                class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2"
                                            >
                                                <button
                                                    v-for="order in productionOrders.slice(0, 2)"
                                                    :key="`line-snapshot-${line.id}-${order.id}`"
                                                    @click="openOrder(order.id)"
                                                    class="group flex items-center justify-between gap-3 rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-left transition hover:bg-white/10"
                                                >
                                                    <div class="min-w-0">
                                                        <p class="truncate text-xs font-semibold text-white">
                                                            #{{ order.id }} · {{ order.customer_name }}
                                                        </p>
                                                        <p class="mt-0.5 truncate text-[11px] text-slate-400">
                                                            {{ order.material }} · {{ formatQuantity(order.quantity) }} ед.
                                                        </p>
                                                    </div>
                                                    <span class="text-cyan-300 transition group-hover:translate-x-0.5">→</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex min-w-[160px] flex-col justify-between rounded-xl border border-slate-200 bg-white px-4 py-4">
                                            <div>
                                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                                    Управление линией
                                                </p>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                                    Быстрая команда оператору
                                                </p>
                                            </div>

                                            <button
                                                v-if="line.status !== 'Работает'"
                                                @click="startLine(line.id)"
                                                class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-3.5 py-2.5 text-xs font-semibold text-white transition hover:bg-emerald-700"
                                            >
                                                <svg viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                                Запустить
                                            </button>

                                            <button
                                                v-else
                                                @click="stopLine(line.id)"
                                                class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
                                            >
                                                <span class="h-2.5 w-2.5 rounded-[2px] bg-current"></span>
                                                Остановить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div
                            v-else
                            class="rounded-2xl border border-dashed border-blue-300 bg-white/75 px-6 py-16 text-center"
                        >
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-500">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16V8l-5 3V7l-5 3V6L4 9v11Z" />
                                </svg>
                            </div>
                            <h4 class="mt-4 font-semibold text-slate-800">Производственные линии не добавлены</h4>
                            <p class="mt-1 text-sm text-slate-500">После добавления оборудования его состояние появится здесь.</p>
                        </div>
                    </div>

                    <!-- LIVE QUEUE -->
                    <aside
                        class="self-start overflow-hidden rounded-2xl border border-slate-800 bg-gradient-to-b from-[#13244d] via-[#111f43] to-[#080f23] text-white shadow-xl shadow-slate-950/15 xl:sticky xl:top-5"
                    >
                        <div class="border-b border-white/10 px-5 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-cyan-300">
                                        Live queue
                                    </p>
                                    <h4 class="mt-2 text-xl font-bold tracking-tight text-white">
                                        Производственная очередь
                                    </h4>
                                    <p class="mt-1 text-xs leading-5 text-blue-100/65">
                                        Текущие заказы и ближайшие задачи оператора.
                                    </p>
                                </div>

                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-cyan-300">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h7M7 17h4" />
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-2">
                                <div class="rounded-xl border border-white/10 bg-white/[0.06] px-3 py-3">
                                    <p class="text-[9px] font-semibold uppercase tracking-wider text-blue-200/45">В работе</p>
                                    <p class="mt-1.5 text-2xl font-bold text-blue-300">{{ productionOrders.length }}</p>
                                </div>

                                <div class="rounded-xl border border-white/10 bg-white/[0.06] px-3 py-3">
                                    <p class="text-[9px] font-semibold uppercase tracking-wider text-blue-200/45">Ожидают</p>
                                    <p class="mt-1.5 text-2xl font-bold text-amber-300">{{ waitingCount }}</p>
                                </div>

                                <div class="rounded-xl border border-white/10 bg-white/[0.06] px-3 py-3">
                                    <p class="text-[9px] font-semibold uppercase tracking-wider text-blue-200/45">Объём</p>
                                    <p class="mt-1.5 text-xl font-bold text-emerald-300">{{ formatQuantity(productionVolume) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="grid grid-cols-2 rounded-xl bg-black/20 p-1">
                                <button
                                    @click="queueTab = 'production'"
                                    class="rounded-lg px-3 py-2.5 text-xs font-semibold transition"
                                    :class="queueTab === 'production' ? 'bg-blue-600 text-white shadow-sm' : 'text-blue-200/70 hover:text-white'"
                                >
                                    В работе
                                </button>

                                <button
                                    @click="queueTab = 'waiting'"
                                    class="rounded-lg px-3 py-2.5 text-xs font-semibold transition"
                                    :class="queueTab === 'waiting' ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-blue-200/70 hover:text-white'"
                                >
                                    Ожидают
                                </button>
                            </div>

                            <div v-if="visibleQueue.length" class="mt-4 space-y-2.5">
                                <button
                                    v-for="(order, index) in visibleQueue"
                                    :key="`queue-${order.id}`"
                                    @click="openOrder(order.id)"
                                    class="group w-full rounded-xl border border-white/10 bg-white/[0.035] px-3.5 py-3.5 text-left transition hover:border-blue-400/30 hover:bg-white/[0.07]"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-500/15 text-xs font-bold text-blue-300">
                                            {{ index + 1 }}
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-semibold text-white">
                                                        {{ order.customer_name }}
                                                    </p>
                                                    <p class="mt-1 truncate text-xs text-blue-100/60">
                                                        {{ order.material }} · заказ #{{ order.id }}
                                                    </p>
                                                </div>

                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    class="mt-0.5 h-4 w-4 shrink-0 text-slate-500 transition group-hover:translate-x-0.5 group-hover:text-cyan-300"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                                </svg>
                                            </div>

                                            <div class="mt-3 flex items-center justify-between gap-3">
                                                <span
                                                    class="rounded-md px-2 py-1 text-[10px] font-semibold uppercase tracking-wider"
                                                    :class="order.status === 'В производстве' ? 'bg-blue-400/10 text-blue-300' : 'bg-amber-400/10 text-amber-300'"
                                                >
                                                    {{ order.status }}
                                                </span>

                                                <span class="text-sm font-bold text-white">
                                                    {{ formatQuantity(order.quantity) }} ед.
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </div>

                            <div v-else class="mt-4 rounded-xl border border-dashed border-white/10 bg-white/[0.025] px-4 py-8 text-center">
                                <p class="text-sm font-semibold text-slate-300">
                                    {{ queueTab === 'production' ? 'Активных заказов нет' : 'Очередь пуста' }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ queueTab === 'production' ? 'Активные заказы появятся здесь.' : 'Новые заказы появятся здесь.' }}
                                </p>
                            </div>

                            <button
                                @click="openOrders"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-xs font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white"
                            >
                                Открыть полный журнал
                                <span>→</span>
                            </button>
                        </div>
                    </aside>
                </div>
            </section>

            <!-- LATEST ORDERS -->
            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div class="flex flex-col justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-600">
                            Производственный журнал
                        </p>
                        <h3 class="mt-1 text-lg font-bold text-slate-950">Последние заказы</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Последние изменения производственного плана
                        </p>
                    </div>

                    <button
                        @click="openOrders"
                        class="text-sm font-semibold text-blue-600 transition hover:text-blue-700"
                    >
                        Открыть журнал →
                    </button>
                </div>

                <div v-if="orders.length" class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80">
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">Заказчик</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">Материал</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">Объём</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">Статус</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">Создан</th>
                                <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-400">Действия</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="order in orders"
                                :key="order.id"
                                class="transition hover:bg-blue-50/30"
                            >
                                <td class="whitespace-nowrap px-5 py-4">
                                    <button @click="openOrder(order.id)" class="flex items-center gap-3 text-left">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-cyan-300"
                                        >
                                            {{ order.customer_name?.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ order.customer_name }}</p>
                                            <p class="mt-0.5 text-xs text-slate-400">Заказ #{{ order.id }}</p>
                                        </div>
                                    </button>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ order.material }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-800">
                                    {{ formatQuantity(order.quantity) }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset"
                                        :class="getOrderStatusClasses(order.status)"
                                    >
                                        {{ order.status }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ formatDate(order.created_at) }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            v-if="order.status === 'Новый'"
                                            @click="changeStatus(order.id, 'В производстве')"
                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                                        >
                                            Запустить
                                        </button>

                                        <button
                                            v-if="order.status === 'В производстве'"
                                            @click="changeStatus(order.id, 'Готово')"
                                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700"
                                        >
                                            Завершить
                                        </button>

                                        <span
                                            v-if="order.status === 'Готово'"
                                            class="text-xs font-medium text-slate-400"
                                        >
                                            Выполнен
                                        </span>

                                        <button
                                            @click="openOrder(order.id)"
                                            title="Открыть заказ"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                class="h-4 w-4"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                                <circle cx="12" cy="12" r="2.5" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="deleteOrder(order.id)"
                                            title="Удалить заказ"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                class="h-4 w-4"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M9 7l1-2h4l1 2m-8 0 1 13h8l1-13" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            class="h-5 w-5"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5h14v14H5zM8 9h8M8 13h5" />
                        </svg>
                    </div>
                    <h4 class="mt-4 font-semibold text-slate-800">Заказов пока нет</h4>
                    <p class="mt-1 text-sm text-slate-500">Создайте первый производственный заказ.</p>
                    <button
                        @click="createOrder"
                        class="mt-5 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Создать заказ
                    </button>
                </div>
            </section>
        </main>
    </div>
</template>
