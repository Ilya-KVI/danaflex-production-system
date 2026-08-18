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
const selectedStatus = ref('Все')

const statuses = [
    'Все',
    'Новый',
    'В производстве',
    'Готово',
]

const filteredOrders = computed(() => {
    const searchText = search.value.trim().toLowerCase()

    return props.orders.filter(order => {
        const matchesSearch =
            !searchText ||
            order.customer_name?.toLowerCase().includes(searchText) ||
            order.material?.toLowerCase().includes(searchText)

        const matchesStatus =
            selectedStatus.value === 'Все' ||
            order.status === selectedStatus.value

        return matchesSearch && matchesStatus
    })
})

const completedCount = computed(() =>
    props.orders.filter(order => order.status === 'Готово').length
)

const productionCount = computed(() =>
    props.orders.filter(order => order.status === 'В производстве').length
)

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

function deleteOrder(id) {
    if (!confirm('Удалить этот заказ?')) {
        return
    }

    router.delete(`/production-orders/${id}`, {
        preserveScroll: true,
    })
}

function editOrder(id) {
    router.visit(`/production-orders/${id}/edit`)
}

function showOrder(id) {
    router.visit(`/production-orders/${id}`)
}

function backToDashboard() {
    router.visit('/dashboard')
}

function createOrder() {
    router.visit('/production-orders/create')
}

function getStatusClasses(status) {
    if (status === 'В производстве') {
        return 'bg-blue-50 text-blue-700 ring-blue-600/20'
    }

    if (status === 'Готово') {
        return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
    }

    return 'bg-slate-100 text-slate-700 ring-slate-600/10'
}
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">

        <!-- HEADER -->

        <header class="border-b border-slate-200 bg-white">
            <div
                class="
                    mx-auto
                    flex
                    max-w-[1600px]
                    items-center
                    justify-between
                    gap-4
                    px-6
                    py-4
                    lg:px-10
                "
            >
                <button
                    @click="backToDashboard"
                    class="flex items-center gap-3 text-left"
                >
                    <div
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center
                            rounded-xl
                            bg-slate-900
                            text-white
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
                                d="M3 21h18M5 21V9l5 3V9l5 3V5h4v16"
                            />
                        </svg>
                    </div>

                    <div>
                        <p class="font-bold text-slate-900">
                            Danaflex
                        </p>

                        <p class="text-xs text-slate-500">
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
                        rounded-lg
                        bg-slate-900
                        px-4
                        py-2.5
                        text-sm
                        font-semibold
                        text-white
                        shadow-sm
                        transition
                        hover:bg-slate-800
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        class="h-4 w-4"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            d="M12 5v14M5 12h14"
                        />
                    </svg>

                    Новый заказ
                </button>
            </div>
        </header>


        <main class="mx-auto max-w-[1600px] px-6 py-8 lg:px-10">

            <!-- TITLE -->

            <div
                class="
                    mb-8
                    flex
                    flex-col
                    justify-between
                    gap-5
                    lg:flex-row
                    lg:items-end
                "
            >
                <div>
                    <button
                        @click="backToDashboard"
                        class="
                            mb-4
                            inline-flex
                            items-center
                            gap-2
                            text-sm
                            font-medium
                            text-slate-500
                            transition
                            hover:text-slate-900
                        "
                    >
                        ← Вернуться в центр управления
                    </button>

                    <p
                        class="
                            mb-2
                            text-xs
                            font-semibold
                            uppercase
                            tracking-[0.18em]
                            text-blue-600
                        "
                    >
                        Производственный журнал
                    </p>

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
                            text-slate-500
                        "
                    >
                        Поиск, фильтрация и управление текущими
                        производственными заказами.
                    </p>
                </div>
            </div>


            <!-- MINI KPI -->

            <section
                class="
                    mb-6
                    grid
                    grid-cols-1
                    gap-4
                    sm:grid-cols-3
                "
            >
                <div
                    class="
                        rounded-2xl
                        border
                        border-slate-200
                        bg-white
                        p-5
                        shadow-sm
                    "
                >
                    <p class="text-sm font-medium text-slate-500">
                        Всего заказов
                    </p>

                    <p
                        class="
                            mt-2
                            text-3xl
                            font-bold
                            text-slate-950
                        "
                    >
                        {{ orders.length }}
                    </p>
                </div>

                <div
                    class="
                        rounded-2xl
                        border
                        border-slate-200
                        bg-white
                        p-5
                        shadow-sm
                    "
                >
                    <p class="text-sm font-medium text-slate-500">
                        В производстве
                    </p>

                    <p
                        class="
                            mt-2
                            text-3xl
                            font-bold
                            text-blue-600
                        "
                    >
                        {{ productionCount }}
                    </p>
                </div>

                <div
                    class="
                        rounded-2xl
                        border
                        border-slate-200
                        bg-white
                        p-5
                        shadow-sm
                    "
                >
                    <p class="text-sm font-medium text-slate-500">
                        Выполнено
                    </p>

                    <p
                        class="
                            mt-2
                            text-3xl
                            font-bold
                            text-emerald-600
                        "
                    >
                        {{ completedCount }}
                    </p>
                </div>
            </section>


            <!-- SEARCH + FILTERS -->

            <section
                class="
                    mb-6
                    rounded-2xl
                    border
                    border-slate-200
                    bg-white
                    p-5
                    shadow-sm
                "
            >
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
                            text-slate-400
                        "
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="11" cy="11" r="7" />
                        <path
                            stroke-linecap="round"
                            d="m20 20-4-4"
                        />
                    </svg>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Поиск по клиенту или материалу"
                        class="
                            w-full
                            rounded-xl
                            border
                            border-slate-200
                            bg-slate-50
                            py-3
                            pl-12
                            pr-4
                            text-sm
                            text-slate-800
                            outline-none
                            transition
                            placeholder:text-slate-400
                            focus:border-blue-400
                            focus:bg-white
                            focus:ring-4
                            focus:ring-blue-50
                        "
                    >
                </div>

                <div
                    class="
                        mt-4
                        flex
                        flex-wrap
                        gap-2
                    "
                >
                    <button
                        v-for="status in statuses"
                        :key="status"
                        @click="selectedStatus = status"
                        class="
                            rounded-lg
                            px-4
                            py-2
                            text-sm
                            font-semibold
                            transition
                        "
                        :class="
                            selectedStatus === status
                                ? 'bg-slate-900 text-white shadow-sm'
                                : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                        "
                    >
                        {{ status }}
                    </button>
                </div>
            </section>


            <!-- TABLE -->

            <section
                class="
                    overflow-hidden
                    rounded-2xl
                    border
                    border-slate-200
                    bg-white
                    shadow-sm
                "
            >
                <div
                    class="
                        flex
                        items-center
                        justify-between
                        border-b
                        border-slate-100
                        px-5
                        py-4
                    "
                >
                    <div>
                        <h2 class="font-bold text-slate-900">
                            Список заказов
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Найдено: {{ filteredOrders.length }}
                        </p>
                    </div>
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
                                "
                            >
                                <th
                                    class="
                                        px-5
                                        py-3
                                        text-left
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Клиент
                                </th>

                                <th
                                    class="
                                        px-5
                                        py-3
                                        text-left
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Материал
                                </th>

                                <th
                                    class="
                                        px-5
                                        py-3
                                        text-left
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Количество
                                </th>

                                <th
                                    class="
                                        px-5
                                        py-3
                                        text-left
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Статус
                                </th>

                                <th
                                    class="
                                        px-5
                                        py-3
                                        text-left
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Создан
                                </th>

                                <th
                                    class="
                                        px-5
                                        py-3
                                        text-right
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Действия
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="order in filteredOrders"
                                :key="order.id"
                                class="
                                    transition
                                    hover:bg-slate-50/70
                                "
                            >
                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5
                                        py-4
                                    "
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="
                                                flex
                                                h-9
                                                w-9
                                                items-center
                                                justify-center
                                                rounded-lg
                                                bg-slate-100
                                                text-xs
                                                font-bold
                                                text-slate-600
                                            "
                                        >
                                            {{
                                                order.customer_name
                                                    ?.charAt(0)
                                            }}
                                        </div>

                                        <div>
                                            <p
                                                class="
                                                    text-sm
                                                    font-semibold
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
                                                ID #{{ order.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td
                                    class="
                                        px-5
                                        py-4
                                        text-sm
                                        text-slate-600
                                    "
                                >
                                    {{ order.material }}
                                </td>

                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5
                                        py-4
                                        text-sm
                                        font-semibold
                                        text-slate-800
                                    "
                                >
                                    {{ formatQuantity(order.quantity) }}
                                </td>

                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5
                                        py-4
                                    "
                                >
                                    <span
                                        class="
                                            inline-flex
                                            rounded-full
                                            px-2.5
                                            py-1
                                            text-xs
                                            font-semibold
                                            ring-1
                                            ring-inset
                                        "
                                        :class="
                                            getStatusClasses(order.status)
                                        "
                                    >
                                        {{ order.status }}
                                    </span>
                                </td>

                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5
                                        py-4
                                        text-sm
                                        text-slate-500
                                    "
                                >
                                    {{ formatDate(order.created_at) }}
                                </td>

                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5
                                        py-4
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            items-center
                                            justify-end
                                            gap-1
                                        "
                                    >
                                        <button
                                            @click="showOrder(order.id)"
                                            title="Открыть заказ"
                                            class="
                                                flex
                                                h-9
                                                w-9
                                                items-center
                                                justify-center
                                                rounded-lg
                                                text-slate-500
                                                transition
                                                hover:bg-blue-50
                                                hover:text-blue-600
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
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.5"
                                                />
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
                                                text-slate-500
                                                transition
                                                hover:bg-amber-50
                                                hover:text-amber-600
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
                                                    d="m4 16-.8 4 4-.8L18 8.4 15.6 6 4 16Z"
                                                />
                                            </svg>
                                        </button>

                                        <button
                                            @click="deleteOrder(order.id)"
                                            title="Удалить"
                                            class="
                                                flex
                                                h-9
                                                w-9
                                                items-center
                                                justify-center
                                                rounded-lg
                                                text-slate-400
                                                transition
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
                                                    d="M4 7h16M10 11v6m4-6v6M9 7l1-2h4l1 2m-8 0 1 13h8l1-13"
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
                        px-6
                        py-16
                        text-center
                    "
                >
                    <div
                        class="
                            mx-auto
                            flex
                            h-12
                            w-12
                            items-center
                            justify-center
                            rounded-xl
                            bg-slate-100
                            text-slate-400
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
                                d="M5 5h14v14H5zM8 9h8M8 13h5"
                            />
                        </svg>
                    </div>

                    <h3
                        class="
                            mt-4
                            font-semibold
                            text-slate-800
                        "
                    >
                        Заказы не найдены
                    </h3>

                    <p
                        class="
                            mt-1
                            text-sm
                            text-slate-500
                        "
                    >
                        Попробуйте изменить запрос или фильтр.
                    </p>
                </div>
            </section>

        </main>
    </div>
</template>
