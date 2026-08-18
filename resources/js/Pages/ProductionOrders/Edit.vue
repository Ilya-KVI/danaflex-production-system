<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    lines: {
        type: Array,
        default: () => [],
    },
})

const original = {
    customer_name: props.order.customer_name ?? '',
    material: props.order.material ?? '',
    quantity: Number(props.order.quantity ?? 0),
    production_line_id: props.order.production_line_id ?? '',
}

const customer_name = ref(original.customer_name)
const material = ref(original.material)
const quantity = ref(original.quantity)
const production_line_id = ref(original.production_line_id)

const processing = ref(false)
const saved = ref(false)

const hasChanges = computed(() => {
    return (
        customer_name.value !== original.customer_name ||
        material.value !== original.material ||
        Number(quantity.value) !== original.quantity ||
        String(production_line_id.value ?? '') !== String(original.production_line_id ?? '')
    )
})

const quantityDelta = computed(() => {
    return Number(quantity.value || 0) - original.quantity
})

const selectedLine = computed(() => {
    return props.lines.find(
        line => String(line.id) === String(production_line_id.value)
    )
})

function updateOrder() {
    if (processing.value || !hasChanges.value) {
        return
    }

    saved.value = false
    processing.value = true

    router.put(
        `/production-orders/${props.order.id}`,
        {
            customer_name: customer_name.value,
            material: material.value,
            quantity: Number(quantity.value),
            production_line_id:
                production_line_id.value === ''
                    ? null
                    : Number(production_line_id.value),
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                saved.value = true
            },
            onFinish: () => {
                processing.value = false
            },
        }
    )
}

function cancel() {
    router.visit(`/production-orders/${props.order.id}`)
}

function goToOrders() {
    router.visit('/production-orders')
}

function goToDashboard() {
    router.visit('/dashboard')
}

function formatNumber(value) {
    return new Intl.NumberFormat('ru-RU').format(Number(value || 0))
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

function statusClasses(status) {
    if (status === 'В производстве') {
        return 'bg-blue-50 text-blue-700 ring-blue-600/20'
    }

    if (status === 'Готово') {
        return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
    }

    return 'bg-slate-100 text-slate-700 ring-slate-600/10'
}

function lineStatusClasses(status) {
    if (status === 'Работает') {
        return 'text-emerald-700 bg-emerald-50 border-emerald-200'
    }

    return 'text-slate-600 bg-slate-100 border-slate-200'
}
</script>

<template>
    <!-- DANAFLEX ORDER EDIT OPERATOR V2 -->
    <div class="min-h-screen bg-gradient-to-b from-blue-100 via-sky-50 to-indigo-100 text-slate-900">

        <!-- TOP NAVIGATION -->

        <header class="border-b border-slate-200/80 bg-white/95 shadow-sm backdrop-blur">
            <div
                class="
                    mx-auto flex max-w-[1500px]
                    items-center justify-between
                    gap-4 px-6 py-4 lg:px-10
                "
            >
                <button
                    @click="goToDashboard"
                    class="flex items-center gap-3 text-left"
                >
                    <div
                        class="
                            flex h-11 w-11 items-center justify-center
                            rounded-xl bg-slate-950 text-cyan-300 shadow-sm
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
                                    hidden rounded-md bg-blue-50 px-2 py-1
                                    text-[10px] font-bold uppercase
                                    tracking-[0.16em] text-blue-600 sm:inline-flex
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

                <div class="flex items-center gap-2">
                    <button
                        @click="goToOrders"
                        class="
                            hidden rounded-lg border border-slate-200
                            bg-white px-4 py-2.5 text-sm font-semibold
                            text-slate-600 transition hover:bg-slate-50 sm:inline-flex
                        "
                    >
                        Все заказы
                    </button>

                    <button
                        @click="cancel"
                        class="
                            rounded-lg border border-slate-200
                            bg-white px-4 py-2.5 text-sm font-semibold
                            text-slate-700 shadow-sm transition hover:bg-slate-50
                        "
                    >
                        ← К заказу
                    </button>
                </div>
            </div>
        </header>


        <main class="mx-auto max-w-[1350px] px-6 py-8 lg:px-10">

            <!-- PAGE HEADING -->

            <section class="mb-7">
                <p
                    class="
                        text-xs font-bold uppercase
                        tracking-[0.2em] text-blue-600
                    "
                >
                    Производственный заказ
                </p>

                <div
                    class="
                        mt-3 flex flex-col justify-between
                        gap-4 lg:flex-row lg:items-end
                    "
                >
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1
                                class="
                                    text-3xl font-bold tracking-tight
                                    text-slate-950 sm:text-4xl
                                "
                            >
                                Редактирование заказа #{{ order.id }}
                            </h1>

                            <span
                                class="
                                    inline-flex rounded-full px-3 py-1.5
                                    text-xs font-semibold ring-1 ring-inset
                                "
                                :class="statusClasses(order.status)"
                            >
                                {{ order.status }}
                            </span>
                        </div>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                            Изменяйте параметры только при необходимости:
                            эти данные используются в производственном плане и рабочем журнале.
                        </p>
                    </div>

                    <div
                        class="
                            inline-flex self-start items-center gap-2
                            rounded-xl border border-slate-200
                            bg-white px-4 py-3 text-xs text-slate-500
                            shadow-sm lg:self-auto
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            class="h-4 w-4 text-slate-400"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9" />
                            <path
                                stroke-linecap="round"
                                d="M12 7v5l3 2"
                            />
                        </svg>

                        Создан {{ formatDate(order.created_at) }}
                    </div>
                </div>
            </section>


            <div
                class="
                    grid grid-cols-1 gap-6
                    xl:grid-cols-[minmax(0,1fr)_360px]
                "
            >

                <!-- FORM -->

                <section
                    class="
                        overflow-hidden rounded-3xl border border-blue-200
                        bg-white shadow-xl shadow-blue-900/[0.06]
                    "
                >
                    <div
                        class="
                            flex items-center justify-between
                            border-b border-blue-100
                            bg-gradient-to-r from-blue-50 via-white to-cyan-50
                            px-6 py-5
                        "
                    >
                        <div>
                            <p class="font-bold text-slate-950">
                                Параметры заказа
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Основные данные для запуска и учёта производства
                            </p>
                        </div>

                        <div
                            v-if="hasChanges"
                            class="
                                inline-flex items-center gap-2
                                rounded-lg border border-amber-200
                                bg-amber-50 px-3 py-2
                                text-xs font-semibold text-amber-700
                            "
                        >
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            Есть изменения
                        </div>

                        <div
                            v-else
                            class="
                                hidden items-center gap-2 rounded-lg
                                border border-emerald-200 bg-emerald-50
                                px-3 py-2 text-xs font-semibold
                                text-emerald-700 sm:inline-flex
                            "
                        >
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Данные актуальны
                        </div>
                    </div>


                    <div class="space-y-7 p-6 sm:p-7">

                        <!-- CUSTOMER -->

                        <div>
                            <div class="mb-2 flex items-end justify-between gap-3">
                                <div>
                                    <label
                                        for="customer_name"
                                        class="block text-sm font-bold text-slate-800"
                                    >
                                        Заказчик
                                    </label>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Компания или подразделение-заказчик
                                    </p>
                                </div>

                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                    Обязательно
                                </span>
                            </div>

                            <input
                                id="customer_name"
                                v-model="customer_name"
                                type="text"
                                autocomplete="off"
                                class="
                                    w-full rounded-xl border border-slate-200
                                    bg-slate-50 px-4 py-3.5
                                    text-sm font-medium text-slate-900
                                    outline-none transition
                                    placeholder:text-slate-400
                                    focus:border-blue-400 focus:bg-white
                                    focus:ring-4 focus:ring-blue-100
                                "
                                placeholder="Например, ООО Альфа"
                            >
                        </div>


                        <!-- MATERIAL -->

                        <div>
                            <div class="mb-2 flex items-end justify-between gap-3">
                                <div>
                                    <label
                                        for="material"
                                        class="block text-sm font-bold text-slate-800"
                                    >
                                        Материал
                                    </label>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Материал или тип выпускаемой продукции
                                    </p>
                                </div>

                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                    Обязательно
                                </span>
                            </div>

                            <input
                                id="material"
                                v-model="material"
                                type="text"
                                autocomplete="off"
                                class="
                                    w-full rounded-xl border border-slate-200
                                    bg-slate-50 px-4 py-3.5
                                    text-sm font-medium text-slate-900
                                    outline-none transition
                                    placeholder:text-slate-400
                                    focus:border-blue-400 focus:bg-white
                                    focus:ring-4 focus:ring-blue-100
                                "
                                placeholder="Например, BOPP плёнка"
                            >
                        </div>


                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                            <!-- QUANTITY -->

                            <div>
                                <label
                                    for="quantity"
                                    class="block text-sm font-bold text-slate-800"
                                >
                                    Объём заказа
                                </label>

                                <p class="mt-1 text-xs text-slate-400">
                                    Количество производимых единиц
                                </p>

                                <div class="relative mt-2">
                                    <input
                                        id="quantity"
                                        v-model="quantity"
                                        type="number"
                                        min="1"
                                        class="
                                            w-full rounded-xl border border-slate-200
                                            bg-slate-50 px-4 py-3.5 pr-16
                                            text-sm font-bold text-slate-900
                                            outline-none transition
                                            focus:border-blue-400 focus:bg-white
                                            focus:ring-4 focus:ring-blue-100
                                        "
                                    >

                                    <span
                                        class="
                                            absolute right-4 top-1/2
                                            -translate-y-1/2
                                            text-xs font-semibold text-slate-400
                                        "
                                    >
                                        ед.
                                    </span>
                                </div>

                                <div
                                    v-if="quantityDelta !== 0"
                                    class="mt-2 text-xs font-medium"
                                    :class="
                                        quantityDelta > 0
                                            ? 'text-blue-600'
                                            : 'text-amber-600'
                                    "
                                >
                                    {{
                                        quantityDelta > 0
                                            ? `+${formatNumber(quantityDelta)} ед. к исходному объёму`
                                            : `${formatNumber(quantityDelta)} ед. от исходного объёма`
                                    }}
                                </div>
                            </div>


                            <!-- LINE -->

                            <div>
                                <label
                                    for="production_line_id"
                                    class="block text-sm font-bold text-slate-800"
                                >
                                    Производственная линия
                                </label>

                                <p class="mt-1 text-xs text-slate-400">
                                    Назначение заказа на оборудование
                                </p>

                                <select
                                    id="production_line_id"
                                    v-model="production_line_id"
                                    class="
                                        mt-2 w-full rounded-xl
                                        border border-slate-200
                                        bg-slate-50 px-4 py-3.5
                                        text-sm font-semibold text-slate-800
                                        outline-none transition
                                        focus:border-blue-400 focus:bg-white
                                        focus:ring-4 focus:ring-blue-100
                                    "
                                >
                                    <option value="">
                                        Не назначена
                                    </option>

                                    <option
                                        v-for="line in lines"
                                        :key="line.id"
                                        :value="line.id"
                                    >
                                        {{ line.name }} · {{ line.status }}
                                    </option>
                                </select>
                            </div>
                        </div>


                        <!-- SELECTED LINE INFO -->

                        <div
                            v-if="selectedLine"
                            class="
                                rounded-2xl border border-cyan-200
                                bg-gradient-to-r from-cyan-50 to-blue-50
                                p-5
                            "
                        >
                            <div
                                class="
                                    flex flex-col justify-between gap-4
                                    sm:flex-row sm:items-center
                                "
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="
                                            flex h-11 w-11 items-center
                                            justify-center rounded-xl
                                            bg-slate-950 text-cyan-300
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
                                        <p class="font-bold text-slate-900">
                                            {{ selectedLine.name }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ selectedLine.material }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="
                                            rounded-lg border px-2.5 py-1.5
                                            text-xs font-semibold
                                        "
                                        :class="lineStatusClasses(selectedLine.status)"
                                    >
                                        {{ selectedLine.status }}
                                    </span>

                                    <span
                                        class="
                                            rounded-lg border border-slate-200
                                            bg-white px-2.5 py-1.5
                                            text-xs font-semibold text-slate-600
                                        "
                                    >
                                        {{ selectedLine.load_percent }}% загрузка
                                    </span>

                                    <span
                                        class="
                                            rounded-lg border border-slate-200
                                            bg-white px-2.5 py-1.5
                                            text-xs font-semibold text-slate-600
                                        "
                                    >
                                        {{ selectedLine.temperature }}°C
                                    </span>
                                </div>
                            </div>
                        </div>


                        <!-- ACTIONS -->

                        <div
                            class="
                                flex flex-col-reverse gap-3
                                border-t border-slate-100 pt-6
                                sm:flex-row sm:items-center sm:justify-between
                            "
                        >
                            <button
                                type="button"
                                @click="cancel"
                                class="
                                    rounded-xl border border-slate-200
                                    bg-white px-5 py-3
                                    text-sm font-semibold text-slate-600
                                    transition hover:bg-slate-50
                                "
                            >
                                Отмена
                            </button>

                            <div class="flex flex-col gap-2 sm:items-end">
                                <button
                                    type="button"
                                    @click="updateOrder"
                                    :disabled="processing || !hasChanges"
                                    class="
                                        inline-flex min-w-[220px]
                                        items-center justify-center gap-2
                                        rounded-xl bg-blue-600
                                        px-5 py-3 text-sm font-bold
                                        text-white shadow-lg shadow-blue-600/20
                                        transition
                                        hover:bg-blue-700
                                        disabled:cursor-not-allowed
                                        disabled:bg-slate-300
                                        disabled:shadow-none
                                    "
                                >
                                    <svg
                                        v-if="!processing"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        class="h-4 w-4"
                                        stroke="currentColor"
                                        stroke-width="1.9"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 4h12l2 2v14H5V4Zm3 0v5h8V4M8 20v-7h8v7"
                                        />
                                    </svg>

                                    <svg
                                        v-else
                                        class="h-4 w-4 animate-spin"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="9"
                                            stroke="currentColor"
                                            stroke-width="3"
                                            class="opacity-25"
                                        />
                                        <path
                                            fill="currentColor"
                                            class="opacity-80"
                                            d="M21 12a9 9 0 0 0-9-9v3a6 6 0 0 1 6 6h3Z"
                                        />
                                    </svg>

                                    {{
                                        processing
                                            ? 'Сохраняем...'
                                            : 'Сохранить изменения'
                                    }}
                                </button>

                                <p
                                    v-if="saved"
                                    class="text-xs font-medium text-emerald-600"
                                >
                                    Изменения сохранены
                                </p>
                            </div>
                        </div>
                    </div>
                </section>


                <!-- OPERATOR SIDEBAR -->

                <aside class="space-y-5">

                    <!-- ORDER SNAPSHOT -->

                    <section
                        class="
                            overflow-hidden rounded-3xl
                            border border-slate-800
                            bg-gradient-to-br from-[#0a1734] to-[#101f46]
                            text-white shadow-xl shadow-slate-900/15
                        "
                    >
                        <div class="border-b border-white/10 p-5">
                            <p
                                class="
                                    text-[11px] font-bold uppercase
                                    tracking-[0.18em] text-cyan-300
                                "
                            >
                                Order snapshot
                            </p>

                            <div class="mt-3 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-3xl font-bold">
                                        #{{ order.id }}
                                    </p>

                                    <p class="mt-1 text-xs text-blue-200/70">
                                        {{ order.customer_name }}
                                    </p>
                                </div>

                                <span
                                    class="
                                        rounded-lg bg-white/10 px-2.5 py-1.5
                                        text-xs font-semibold text-blue-100
                                    "
                                >
                                    {{ order.status }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-px bg-white/10">
                            <div class="bg-[#0c1835] p-4">
                                <p
                                    class="
                                        text-[10px] font-bold uppercase
                                        tracking-wider text-blue-300/60
                                    "
                                >
                                    Было
                                </p>

                                <p class="mt-2 text-xl font-bold text-white">
                                    {{ formatNumber(original.quantity) }}
                                </p>

                                <p class="mt-0.5 text-xs text-blue-200/60">
                                    единиц
                                </p>
                            </div>

                            <div class="bg-[#0c1835] p-4">
                                <p
                                    class="
                                        text-[10px] font-bold uppercase
                                        tracking-wider text-blue-300/60
                                    "
                                >
                                    Сейчас
                                </p>

                                <p
                                    class="mt-2 text-xl font-bold"
                                    :class="
                                        quantityDelta === 0
                                            ? 'text-white'
                                            : 'text-cyan-300'
                                    "
                                >
                                    {{ formatNumber(quantity) }}
                                </p>

                                <p class="mt-0.5 text-xs text-blue-200/60">
                                    единиц
                                </p>
                            </div>
                        </div>

                        <div class="p-5">
                            <p class="text-xs leading-5 text-blue-100/70">
                                Проверяйте объём и назначенную линию перед сохранением:
                                изменения сразу влияют на рабочий план.
                            </p>
                        </div>
                    </section>


                    <!-- CHANGE CONTROL -->

                    <section
                        class="
                            rounded-3xl border border-blue-200
                            bg-white p-5 shadow-lg shadow-blue-900/[0.05]
                        "
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="
                                    flex h-10 w-10 items-center justify-center
                                    rounded-xl bg-blue-100 text-blue-700
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
                                        d="M12 3v18M3 12h18"
                                    />
                                </svg>
                            </div>

                            <div>
                                <p class="font-bold text-slate-900">
                                    Контроль изменений
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Сводка перед сохранением
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3">
                            <div
                                class="
                                    flex items-center justify-between gap-3
                                    rounded-xl bg-slate-50 px-4 py-3
                                "
                            >
                                <span class="text-xs font-medium text-slate-500">
                                    Заказчик
                                </span>

                                <span
                                    class="text-right text-xs font-bold"
                                    :class="
                                        customer_name !== original.customer_name
                                            ? 'text-blue-600'
                                            : 'text-slate-700'
                                    "
                                >
                                    {{
                                        customer_name !== original.customer_name
                                            ? 'Изменён'
                                            : 'Без изменений'
                                    }}
                                </span>
                            </div>

                            <div
                                class="
                                    flex items-center justify-between gap-3
                                    rounded-xl bg-slate-50 px-4 py-3
                                "
                            >
                                <span class="text-xs font-medium text-slate-500">
                                    Материал
                                </span>

                                <span
                                    class="text-right text-xs font-bold"
                                    :class="
                                        material !== original.material
                                            ? 'text-blue-600'
                                            : 'text-slate-700'
                                    "
                                >
                                    {{
                                        material !== original.material
                                            ? 'Изменён'
                                            : 'Без изменений'
                                    }}
                                </span>
                            </div>

                            <div
                                class="
                                    flex items-center justify-between gap-3
                                    rounded-xl bg-slate-50 px-4 py-3
                                "
                            >
                                <span class="text-xs font-medium text-slate-500">
                                    Объём
                                </span>

                                <span
                                    class="text-right text-xs font-bold"
                                    :class="
                                        quantityDelta !== 0
                                            ? 'text-blue-600'
                                            : 'text-slate-700'
                                    "
                                >
                                    {{
                                        quantityDelta !== 0
                                            ? formatNumber(quantityDelta)
                                            : 'Без изменений'
                                    }}
                                </span>
                            </div>

                            <div
                                class="
                                    flex items-center justify-between gap-3
                                    rounded-xl bg-slate-50 px-4 py-3
                                "
                            >
                                <span class="text-xs font-medium text-slate-500">
                                    Линия
                                </span>

                                <span
                                    class="text-right text-xs font-bold"
                                    :class="
                                        String(production_line_id ?? '') !==
                                        String(original.production_line_id ?? '')
                                            ? 'text-blue-600'
                                            : 'text-slate-700'
                                    "
                                >
                                    {{
                                        String(production_line_id ?? '') !==
                                        String(original.production_line_id ?? '')
                                            ? 'Изменена'
                                            : 'Без изменений'
                                    }}
                                </span>
                            </div>
                        </div>
                    </section>

                </aside>
            </div>
        </main>
    </div>
</template>
