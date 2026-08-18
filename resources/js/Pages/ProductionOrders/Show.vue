<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
})

function back() {
    router.visit('/production-orders')
}

function goDashboard() {
    router.visit('/dashboard')
}

function edit() {
    router.visit(`/production-orders/${props.order.id}/edit`)
}

function deleteOrder() {
    if (!confirm('Удалить этот заказ?')) {
        return
    }

    router.delete(`/production-orders/${props.order.id}`)
}

function formatDate(date) {
    if (!date) {
        return '—'
    }

    return new Date(date).toLocaleString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

function formatQuantity(quantity) {
    return new Intl.NumberFormat('ru-RU').format(quantity ?? 0)
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
                    max-w-[1400px]
                    items-center
                    justify-between
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
                    @click="back"
                    class="
                        rounded-lg
                        border
                        border-slate-200
                        bg-white
                        px-4
                        py-2.5
                        text-sm
                        font-semibold
                        text-slate-600
                        transition
                        hover:bg-slate-50
                    "
                >
                    ← Все заказы
                </button>
            </div>
        </header>


        <main
            class="
                mx-auto
                max-w-[1100px]
                px-6
                py-10
                lg:px-10
            "
        >
            <!-- TITLE -->

            <div class="mb-7">
                <p
                    class="
                        text-xs
                        font-semibold
                        uppercase
                        tracking-[0.18em]
                        text-blue-600
                    "
                >
                    Карточка производственного заказа
                </p>

                <div
                    class="
                        mt-3
                        flex
                        flex-col
                        justify-between
                        gap-4
                        sm:flex-row
                        sm:items-center
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
                            Заказ #{{ order.id }}
                        </h1>

                        <p
                            class="
                                mt-2
                                text-sm
                                text-slate-500
                            "
                        >
                            Создан {{ formatDate(order.created_at) }}
                        </p>
                    </div>

                    <span
                        class="
                            inline-flex
                            self-start
                            rounded-full
                            px-3
                            py-1.5
                            text-sm
                            font-semibold
                            ring-1
                            ring-inset
                            sm:self-auto
                        "
                        :class="getStatusClasses(order.status)"
                    >
                        {{ order.status }}
                    </span>
                </div>
            </div>


            <!-- CONTENT -->

            <div
                class="
                    grid
                    grid-cols-1
                    gap-6
                    lg:grid-cols-[1fr_320px]
                "
            >
                <!-- ORDER INFO -->

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
                            border-b
                            border-slate-100
                            px-6
                            py-5
                        "
                    >
                        <h2
                            class="
                                text-lg
                                font-bold
                                text-slate-900
                            "
                        >
                            Информация о заказе
                        </h2>

                        <p
                            class="
                                mt-1
                                text-sm
                                text-slate-500
                            "
                        >
                            Основные производственные параметры
                        </p>
                    </div>


                    <div class="p-6">
                        <div
                            class="
                                grid
                                grid-cols-1
                                gap-5
                                sm:grid-cols-2
                            "
                        >
                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-slate-100
                                    bg-slate-50
                                    p-4
                                "
                            >
                                <p
                                    class="
                                        text-xs
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Клиент
                                </p>

                                <p
                                    class="
                                        mt-2
                                        text-lg
                                        font-bold
                                        text-slate-900
                                    "
                                >
                                    {{ order.customer_name }}
                                </p>
                            </div>


                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-slate-100
                                    bg-slate-50
                                    p-4
                                "
                            >
                                <p
                                    class="
                                        text-xs
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Материал
                                </p>

                                <p
                                    class="
                                        mt-2
                                        text-lg
                                        font-bold
                                        text-slate-900
                                    "
                                >
                                    {{ order.material }}
                                </p>
                            </div>


                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-slate-100
                                    bg-slate-50
                                    p-4
                                "
                            >
                                <p
                                    class="
                                        text-xs
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Количество
                                </p>

                                <p
                                    class="
                                        mt-2
                                        text-2xl
                                        font-bold
                                        text-slate-950
                                    "
                                >
                                    {{ formatQuantity(order.quantity) }}
                                </p>
                            </div>


                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-slate-100
                                    bg-slate-50
                                    p-4
                                "
                            >
                                <p
                                    class="
                                        text-xs
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Номер заказа
                                </p>

                                <p
                                    class="
                                        mt-2
                                        text-2xl
                                        font-bold
                                        text-slate-950
                                    "
                                >
                                    #{{ order.id }}
                                </p>
                            </div>
                        </div>


                        <div
                            v-if="order.production_line"
                            class="
                                mt-5
                                rounded-xl
                                border
                                border-blue-100
                                bg-blue-50/60
                                p-4
                            "
                        >
                            <div
                                class="
                                    flex
                                    items-center
                                    gap-3
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-10
                                        w-10
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-white
                                        text-blue-600
                                        shadow-sm
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
                                            d="M8 8h8v8H8zM4 12H2m20 0h-2M12 4V2m0 20v-2"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <p
                                        class="
                                            text-xs
                                            font-semibold
                                            uppercase
                                            tracking-wider
                                            text-blue-500
                                        "
                                    >
                                        Производственная линия
                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            font-bold
                                            text-slate-900
                                        "
                                    >
                                        {{ order.production_line.name }}
                                    </p>
                                </div>
                            </div>
                        </div>


                        <div
                            class="
                                mt-6
                                border-t
                                border-slate-100
                                pt-5
                            "
                        >
                            <p
                                class="
                                    text-xs
                                    font-semibold
                                    uppercase
                                    tracking-wider
                                    text-slate-400
                                "
                            >
                                Дата создания
                            </p>

                            <p
                                class="
                                    mt-2
                                    text-sm
                                    font-medium
                                    text-slate-700
                                "
                            >
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                    </div>
                </section>


                <!-- ACTIONS -->

                <aside class="space-y-4">
                    <section
                        class="
                            rounded-2xl
                            border
                            border-slate-200
                            bg-white
                            p-5
                            shadow-sm
                        "
                    >
                        <h2
                            class="
                                font-bold
                                text-slate-900
                            "
                        >
                            Управление
                        </h2>

                        <p
                            class="
                                mt-1
                                text-sm
                                leading-5
                                text-slate-500
                            "
                        >
                            Действия с текущим заказом
                        </p>

                        <div class="mt-5 space-y-2">
                            <button
                                @click="edit"
                                class="
                                    flex
                                    w-full
                                    items-center
                                    justify-center
                                    gap-2
                                    rounded-lg
                                    bg-slate-900
                                    px-4
                                    py-3
                                    text-sm
                                    font-semibold
                                    text-white
                                    transition
                                    hover:bg-slate-800
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

                                Редактировать
                            </button>


                            <button
                                @click="back"
                                class="
                                    flex
                                    w-full
                                    items-center
                                    justify-center
                                    gap-2
                                    rounded-lg
                                    border
                                    border-slate-200
                                    bg-white
                                    px-4
                                    py-3
                                    text-sm
                                    font-semibold
                                    text-slate-700
                                    transition
                                    hover:bg-slate-50
                                "
                            >
                                ← К списку заказов
                            </button>
                        </div>
                    </section>


                    <section
                        class="
                            rounded-2xl
                            border
                            border-rose-100
                            bg-white
                            p-5
                            shadow-sm
                        "
                    >
                        <h2
                            class="
                                font-bold
                                text-slate-900
                            "
                        >
                            Опасная зона
                        </h2>

                        <p
                            class="
                                mt-1
                                text-sm
                                leading-5
                                text-slate-500
                            "
                        >
                            Удалённый заказ восстановить нельзя.
                        </p>

                        <button
                            @click="deleteOrder"
                            class="
                                mt-4
                                flex
                                w-full
                                items-center
                                justify-center
                                gap-2
                                rounded-lg
                                border
                                border-rose-200
                                bg-rose-50
                                px-4
                                py-3
                                text-sm
                                font-semibold
                                text-rose-700
                                transition
                                hover:bg-rose-100
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

                            Удалить заказ
                        </button>
                    </section>
                </aside>
            </div>
        </main>
    </div>
</template>
