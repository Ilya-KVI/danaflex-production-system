<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const activeStatus = ref('Все')

const statuses = [
    'Все',
    'Новый',
    'В производстве',
    'Готово',
]

const total = computed(() => props.orders.length)

const inProduction = computed(() => {
    return props.orders.filter(
        order => order.status === 'В производстве'
    ).length
})

const completed = computed(() => {
    return props.orders.filter(
        order => order.status === 'Готово'
    ).length
})

const queued = computed(() => {
    return props.orders.filter(
        order => order.status === 'Новый'
    ).length
})

const filteredOrders = computed(() => {
    const query = search.value.trim().toLowerCase()

    return props.orders.filter(order => {
        const matchesStatus =
            activeStatus.value === 'Все' ||
            order.status === activeStatus.value

        const matchesSearch =
            !query ||
            String(order.customer_name || '')
                .toLowerCase()
                .includes(query) ||
            String(order.material || '')
                .toLowerCase()
                .includes(query) ||
            String(order.id || '')
                .toLowerCase()
                .includes(query)

        return matchesStatus && matchesSearch
    })
})

function goDashboard() {
    router.visit('/dashboard')
}

function createOrder() {
    router.visit('/production-orders/create')
}

function openOrder(id) {
    router.visit(`/production-orders/${id}`)
}

function editOrder(id) {
    router.visit(`/production-orders/${id}/edit`)
}

function deleteOrder(order) {
    const ok = window.confirm(
        `Удалить заказ #${order.id} — ${order.customer_name}?`
    )

    if (!ok) {
        return
    }

    router.delete(`/production-orders/${order.id}`, {
        preserveScroll: true,
    })
}

function formatNumber(value) {
    return new Intl.NumberFormat('ru-RU').format(Number(value || 0))
}

function formatDate(value) {
    if (!value) {
        return '—'
    }

    return new Date(value).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

function statusClasses(status) {
    if (status === 'В производстве') {
        return 'border-blue-200 bg-blue-50 text-blue-700'
    }

    if (status === 'Готово') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700'
    }

    return 'border-amber-200 bg-amber-50 text-amber-700'
}

function statusDot(status) {
    if (status === 'В производстве') {
        return 'bg-blue-500'
    }

    if (status === 'Готово') {
        return 'bg-emerald-500'
    }

    return 'bg-amber-500'
}
</script>

<template>
    <!-- DANAFLEX ORDERS OPERATOR V3 -->
    <div
        class="
            min-h-screen
            bg-gradient-to-b
            from-blue-100
            via-sky-50
            to-indigo-100
            text-slate-900
        "
    >

        <!-- HEADER -->

        <header
            class="
                border-b
                border-slate-200/80
                bg-white/95
                shadow-sm
                backdrop-blur
            "
        >
            <div
                class="
                    mx-auto
                    flex
                    max-w-[1500px]
                    items-center
                    justify-between
                    gap-4
                    px-6
                    py-4
                    lg:px-10
                "
            >
                <button
                    @click="goDashboard"
                    class="flex items-center gap-3 text-left"
                >
                    <div
                        class="
                            flex
                            h-11
                            w-11
                            items-center
                            justify-center
                            rounded-xl
                            bg-slate-950
                            text-cyan-300
                            shadow-sm
                        "
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
                        <div class="flex items-center gap-2">
                            <p class="font-bold tracking-tight text-slate-950">
                                Danaflex
                            </p>

                            <span
                                class="
                                    hidden
                                    rounded-md
                                    bg-blue-50
                                    px-2
                                    py-1
                                    text-[10px]
                                    font-bold
                                    uppercase
                                    tracking-[0.16em]
                                    text-blue-600
                                    sm:inline-flex
                                "
                            >
                                Operator
                            </span>
                        </div>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Production System
                        </p>
                    </div>
                </button>

                <button
                    @click="createOrder"
                    class="
                        inline-flex
                        items-center
                        gap-2
                        rounded-xl
                        bg-slate-950
                        px-4
                        py-2.5
                        text-sm
                        font-bold
                        text-white
                        shadow-lg
                        shadow-slate-900/10
                        transition
                        hover:bg-slate-800
                    "
                >
                    <span class="text-lg leading-none">+</span>
                    Новый заказ
                </button>
            </div>
        </header>


        <main class="mx-auto max-w-[1500px] px-6 py-8 lg:px-10">

            <!-- HEADING -->

            <section class="mb-7">
                <button
                    @click="goDashboard"
                    class="
                        mb-4
                        inline-flex
                        items-center
                        gap-2
                        text-sm
                        font-semibold
                        text-slate-500
                        transition
                        hover:text-blue-700
                    "
                >
                    ← Вернуться в центр управления
                </button>

                <p
                    class="
                        text-xs
                        font-bold
                        uppercase
                        tracking-[0.2em]
                        text-blue-600
                    "
                >
                    Производственный журнал
                </p>

                <div
                    class="
                        mt-3
                        flex
                        flex-col
                        justify-between
                        gap-4
                        lg:flex-row
                        lg:items-end
                    "
                >
                    <div>
                        <h1
                            class="
                                text-3xl
                                font-bold
                                tracking-tight
                                text-slate-950
                                sm:text-4xl
                            "
                        >
                            Производственные заказы
                        </h1>

                        <p
                            class="
                                mt-2
                                max-w-2xl
                                text-sm
                                leading-6
                                text-slate-600
                            "
                        >
                            Поиск, контроль статусов и оперативное управление
                            текущим производственным планом.
                        </p>
                    </div>

                    <div
                        class="
                            inline-flex
                            self-start
                            items-center
                            gap-2
                            rounded-xl
                            border
                            border-emerald-200
                            bg-emerald-50
                            px-4
                            py-2.5
                            text-xs
                            font-bold
                            text-emerald-700
                            shadow-sm
                            lg:self-auto
                        "
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Журнал доступен оператору
                    </div>
                </div>
            </section>


            <!-- KPI -->

            <section class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
                <div
                    class="
                        rounded-2xl
                        border
                        border-blue-300
                        bg-gradient-to-br
                        from-blue-100
                        via-blue-50
                        to-cyan-100
                        p-5
                        shadow-md
                        shadow-blue-900/5
                    "
                >
                    <p class="text-sm font-semibold text-slate-600">
                        Всего заказов
                    </p>

                    <div class="mt-3 flex items-end justify-between gap-4">
                        <p class="text-3xl font-bold text-slate-950">
                            {{ total }}
                        </p>

                        <div
                            class="
                                flex
                                h-10
                                w-10
                                items-center
                                justify-center
                                rounded-xl
                                border
                                border-blue-200
                                bg-white/70
                                text-blue-700
                            "
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
                                    d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"
                                />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-2 text-xs text-blue-700/70">
                        В производственном журнале
                    </p>
                </div>

                <div
                    class="
                        rounded-2xl
                        border
                        border-amber-300
                        bg-gradient-to-br
                        from-amber-100
                        via-amber-50
                        to-orange-100
                        p-5
                        shadow-md
                        shadow-amber-900/5
                    "
                >
                    <p class="text-sm font-semibold text-slate-600">
                        Ожидают
                    </p>

                    <p class="mt-3 text-3xl font-bold text-amber-600">
                        {{ queued }}
                    </p>

                    <p class="mt-2 text-xs text-amber-700/70">
                        Ещё не запущены
                    </p>
                </div>

                <div
                    class="
                        rounded-2xl
                        border
                        border-blue-300
                        bg-gradient-to-br
                        from-sky-100
                        via-blue-50
                        to-indigo-100
                        p-5
                        shadow-md
                        shadow-blue-900/5
                    "
                >
                    <p class="text-sm font-semibold text-slate-600">
                        В производстве
                    </p>

                    <p class="mt-3 text-3xl font-bold text-blue-600">
                        {{ inProduction }}
                    </p>

                    <p class="mt-2 text-xs text-blue-700/70">
                        Активные задания
                    </p>
                </div>

                <div
                    class="
                        rounded-2xl
                        border
                        border-emerald-300
                        bg-gradient-to-br
                        from-emerald-100
                        via-emerald-50
                        to-teal-100
                        p-5
                        shadow-md
                        shadow-emerald-900/5
                    "
                >
                    <p class="text-sm font-semibold text-slate-600">
                        Завершено
                    </p>

                    <p class="mt-3 text-3xl font-bold text-emerald-600">
                        {{ completed }}
                    </p>

                    <p class="mt-2 text-xs text-emerald-700/70">
                        Выполненные задания
                    </p>
                </div>
            </section>


            <!-- CONTROL PANEL -->

            <section
                class="
                    mb-6
                    overflow-hidden
                    rounded-3xl
                    border
                    border-slate-800
                    bg-gradient-to-br
                    from-[#0a1734]
                    to-[#101f46]
                    text-white
                    shadow-xl
                    shadow-slate-900/15
                "
            >
                <div
                    class="
                        flex
                        flex-col
                        gap-5
                        border-b
                        border-white/10
                        p-5
                        lg:flex-row
                        lg:items-center
                        lg:justify-between
                    "
                >
                    <div>
                        <p
                            class="
                                text-[11px]
                                font-bold
                                uppercase
                                tracking-[0.18em]
                                text-cyan-300
                            "
                        >
                            Order control
                        </p>

                        <h2 class="mt-1 text-xl font-bold">
                            Фильтрация производственного плана
                        </h2>

                        <p class="mt-1 text-xs text-blue-100/60">
                            Быстрый поиск по заказчику, материалу или номеру заказа.
                        </p>
                    </div>

                    <div
                        class="
                            flex
                            items-center
                            gap-2
                            rounded-xl
                            border
                            border-white/10
                            bg-white/[0.05]
                            px-3
                            py-2
                            text-xs
                            font-semibold
                            text-blue-100
                        "
                    >
                        Найдено:
                        <span class="text-base font-bold text-cyan-300">
                            {{ filteredOrders.length }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="relative">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            class="
                                absolute
                                left-4
                                top-1/2
                                h-5
                                w-5
                                -translate-y-1/2
                                text-blue-200/50
                            "
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="11" cy="11" r="7" />
                            <path
                                stroke-linecap="round"
                                d="m20 20-3.5-3.5"
                            />
                        </svg>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Поиск по клиенту, материалу или ID заказа"
                            class="
                                w-full
                                rounded-xl
                                border
                                border-white/10
                                bg-white/[0.07]
                                py-3.5
                                pl-12
                                pr-4
                                text-sm
                                font-medium
                                text-white
                                outline-none
                                transition
                                placeholder:text-blue-200/40
                                focus:border-cyan-400/40
                                focus:bg-white/[0.10]
                                focus:ring-4
                                focus:ring-cyan-400/10
                            "
                        >
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            v-for="status in statuses"
                            :key="status"
                            @click="activeStatus = status"
                            class="
                                rounded-lg
                                border
                                px-4
                                py-2.5
                                text-xs
                                font-bold
                                transition
                            "
                            :class="
                                activeStatus === status
                                    ? 'border-blue-500 bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                                    : 'border-white/10 bg-white/[0.04] text-blue-100/70 hover:bg-white/[0.08] hover:text-white'
                            "
                        >
                            {{ status }}
                        </button>
                    </div>
                </div>
            </section>


            <!-- TABLE -->

            <section
                class="
                    overflow-hidden
                    rounded-3xl
                    border
                    border-blue-200
                    bg-white
                    shadow-xl
                    shadow-blue-900/[0.05]
                "
            >
                <div
                    class="
                        flex
                        flex-col
                        justify-between
                        gap-3
                        border-b
                        border-slate-100
                        bg-gradient-to-r
                        from-blue-50
                        via-white
                        to-cyan-50
                        px-6
                        py-5
                        sm:flex-row
                        sm:items-center
                    "
                >
                    <div>
                        <p class="font-bold text-slate-950">
                            Рабочий список заказов
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Нажмите на строку или используйте действия справа.
                        </p>
                    </div>

                    <p class="text-xs font-semibold text-slate-500">
                        Показано {{ filteredOrders.length }} из {{ total }}
                    </p>
                </div>

                <div
                    v-if="filteredOrders.length"
                    class="overflow-x-auto"
                >
                    <table class="min-w-full">
                        <thead>
                            <tr
                                class="
                                    border-b
                                    border-slate-100
                                    bg-slate-50/80
                                    text-left
                                "
                            >
                                <th
                                    class="
                                        px-6
                                        py-3.5
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-[0.12em]
                                        text-slate-400
                                    "
                                >
                                    Заказ
                                </th>

                                <th
                                    class="
                                        px-4
                                        py-3.5
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-[0.12em]
                                        text-slate-400
                                    "
                                >
                                    Материал
                                </th>

                                <th
                                    class="
                                        px-4
                                        py-3.5
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-[0.12em]
                                        text-slate-400
                                    "
                                >
                                    Объём
                                </th>

                                <th
                                    class="
                                        px-4
                                        py-3.5
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-[0.12em]
                                        text-slate-400
                                    "
                                >
                                    Статус
                                </th>

                                <th
                                    class="
                                        px-4
                                        py-3.5
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-[0.12em]
                                        text-slate-400
                                    "
                                >
                                    Создан
                                </th>

                                <th
                                    class="
                                        px-6
                                        py-3.5
                                        text-right
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-[0.12em]
                                        text-slate-400
                                    "
                                >
                                    Действия
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="order in filteredOrders"
                                :key="order.id"
                                @click="openOrder(order.id)"
                                class="
                                    group
                                    cursor-pointer
                                    border-b
                                    border-slate-100
                                    transition
                                    last:border-b-0
                                    hover:bg-blue-50/70
                                "
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="
                                                flex
                                                h-10
                                                w-10
                                                shrink-0
                                                items-center
                                                justify-center
                                                rounded-xl
                                                border
                                                border-blue-100
                                                bg-blue-50
                                                text-xs
                                                font-bold
                                                text-blue-700
                                                transition
                                                group-hover:border-blue-200
                                                group-hover:bg-blue-100
                                            "
                                        >
                                            #{{ order.id }}
                                        </div>

                                        <div>
                                            <p
                                                class="
                                                    font-bold
                                                    text-slate-900
                                                "
                                            >
                                                {{ order.customer_name }}
                                            </p>

                                            <p
                                                class="
                                                    mt-0.5
                                                    text-xs
                                                    text-slate-400
                                                "
                                            >
                                                Производственный заказ
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <p
                                        class="
                                            max-w-[260px]
                                            truncate
                                            text-sm
                                            font-medium
                                            text-slate-600
                                        "
                                    >
                                        {{ order.material }}
                                    </p>
                                </td>

                                <td class="px-4 py-4">
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ formatNumber(order.quantity) }}
                                        <span class="font-medium text-slate-400">
                                            ед.
                                        </span>
                                    </p>
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2
                                            rounded-full
                                            border
                                            px-3
                                            py-1.5
                                            text-xs
                                            font-bold
                                        "
                                        :class="statusClasses(order.status)"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full"
                                            :class="statusDot(order.status)"
                                        ></span>

                                        {{ order.status }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    <p class="text-xs font-medium text-slate-500">
                                        {{ formatDate(order.created_at) }}
                                    </p>
                                </td>

                                <td
                                    class="px-6 py-4"
                                    @click.stop
                                >
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openOrder(order.id)"
                                            title="Открыть заказ"
                                            class="
                                                flex
                                                h-9
                                                w-9
                                                items-center
                                                justify-center
                                                rounded-lg
                                                border
                                                border-slate-200
                                                bg-white
                                                text-slate-500
                                                transition
                                                hover:border-blue-200
                                                hover:bg-blue-50
                                                hover:text-blue-700
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                class="h-4 w-4"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"
                                                />
                                                <circle cx="12" cy="12" r="2.5" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="editOrder(order.id)"
                                            title="Редактировать"
                                            class="
                                                flex
                                                h-9
                                                w-9
                                                items-center
                                                justify-center
                                                rounded-lg
                                                border
                                                border-slate-200
                                                bg-white
                                                text-slate-500
                                                transition
                                                hover:border-amber-200
                                                hover:bg-amber-50
                                                hover:text-amber-700
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                class="h-4 w-4"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m4 20 4.5-1 10-10-3.5-3.5-10 10L4 20Z"
                                                />
                                            </svg>
                                        </button>

                                        <button
                                            @click="deleteOrder(order)"
                                            title="Удалить"
                                            class="
                                                flex
                                                h-9
                                                w-9
                                                items-center
                                                justify-center
                                                rounded-lg
                                                border
                                                border-slate-200
                                                bg-white
                                                text-slate-400
                                                transition
                                                hover:border-rose-200
                                                hover:bg-rose-50
                                                hover:text-rose-600
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                class="h-4 w-4"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5 7h14M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else
                    class="
                        flex
                        min-h-[260px]
                        flex-col
                        items-center
                        justify-center
                        px-6
                        py-12
                        text-center
                    "
                >
                    <div
                        class="
                            flex
                            h-12
                            w-12
                            items-center
                            justify-center
                            rounded-2xl
                            bg-blue-50
                            text-blue-500
                        "
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
                                d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"
                            />
                        </svg>
                    </div>

                    <p class="mt-4 font-bold text-slate-900">
                        Заказы не найдены
                    </p>

                    <p class="mt-1 max-w-sm text-sm text-slate-500">
                        Измените поисковый запрос или выберите другой статус.
                    </p>
                </div>
            </section>
        </main>
    </div>
</template>
