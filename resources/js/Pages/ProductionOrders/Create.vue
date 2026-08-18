<script setup>
import { computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps({
    lines: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    customer_name: '',
    material: '',
    quantity: '',
    production_line_id: '',
})

const selectedLine = computed(() => {
    return props.lines.find(
        line => String(line.id) === String(form.production_line_id)
    )
})

const lineNeedsAttention = computed(() => {
    if (!selectedLine.value) {
        return false
    }

    return (
        Number(selectedLine.value.temperature) > 45 ||
        Number(selectedLine.value.load_percent) >= 90
    )
})

const uniqueMaterials = computed(() => {
    return [
        ...new Set(
            props.lines
                .map(line => line.material)
                .filter(Boolean)
        ),
    ]
})

function submit() {
    form
        .transform(data => ({
            ...data,
            quantity: Number(data.quantity),
            production_line_id:
                data.production_line_id === ''
                    ? null
                    : Number(data.production_line_id),
        }))
        .post('/production-orders')
}

function cancel() {
    router.visit('/production-orders')
}

function goDashboard() {
    router.visit('/dashboard')
}

function formatNumber(value) {
    return new Intl.NumberFormat('ru-RU').format(Number(value || 0))
}

function statusClasses(status) {
    if (status === 'Работает') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700'
    }

    return 'border-slate-200 bg-slate-100 text-slate-600'
}

function loadClasses(load) {
    const value = Number(load || 0)

    if (value >= 90) {
        return 'bg-rose-500'
    }

    if (value >= 70) {
        return 'bg-blue-600'
    }

    return 'bg-emerald-500'
}
</script>

<template>
    <!-- DANAFLEX ORDER CREATE OPERATOR V2 -->
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
                    @click="cancel"
                    class="
                        rounded-lg
                        border
                        border-slate-200
                        bg-white
                        px-4
                        py-2.5
                        text-sm
                        font-semibold
                        text-slate-700
                        shadow-sm
                        transition
                        hover:bg-slate-50
                    "
                >
                    ← К журналу
                </button>
            </div>
        </header>


        <main class="mx-auto max-w-[1350px] px-6 py-8 lg:px-10">

            <!-- TITLE -->

            <section class="mb-7">
                <p
                    class="
                        text-xs
                        font-bold
                        uppercase
                        tracking-[0.2em]
                        text-blue-600
                    "
                >
                    Производственное задание
                </p>

                <div class="mt-3">
                    <h1
                        class="
                            text-3xl
                            font-bold
                            tracking-tight
                            text-slate-950
                            sm:text-4xl
                        "
                    >
                        Новый производственный заказ
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
                        Создайте заказ, задайте объём и при необходимости
                        сразу назначьте производственную линию.
                    </p>
                </div>
            </section>


            <div
                class="
                    grid
                    grid-cols-1
                    gap-6
                    xl:grid-cols-[minmax(0,1fr)_380px]
                "
            >

                <!-- FORM -->

                <section
                    class="
                        overflow-hidden
                        rounded-3xl
                        border
                        border-blue-200
                        bg-white
                        shadow-xl
                        shadow-blue-900/[0.06]
                    "
                >
                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            border-b
                            border-blue-100
                            bg-gradient-to-r
                            from-blue-50
                            via-white
                            to-cyan-50
                            px-6
                            py-5
                        "
                    >
                        <div>
                            <p class="font-bold text-slate-950">
                                Параметры запуска
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Данные нового производственного заказа
                            </p>
                        </div>

                        <span
                            class="
                                inline-flex
                                items-center
                                gap-2
                                rounded-lg
                                border
                                border-slate-200
                                bg-white
                                px-3
                                py-2
                                text-xs
                                font-semibold
                                text-slate-600
                            "
                        >
                            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                            Статус: Новый
                        </span>
                    </div>


                    <form
                        class="space-y-7 p-6 sm:p-7"
                        @submit.prevent="submit"
                    >

                        <!-- CUSTOMER -->

                        <div>
                            <div
                                class="
                                    mb-2
                                    flex
                                    items-end
                                    justify-between
                                    gap-3
                                "
                            >
                                <div>
                                    <label
                                        for="customer_name"
                                        class="
                                            block
                                            text-sm
                                            font-bold
                                            text-slate-800
                                        "
                                    >
                                        Заказчик
                                    </label>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Компания или подразделение-заказчик
                                    </p>
                                </div>

                                <span
                                    class="
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Обязательно
                                </span>
                            </div>

                            <input
                                id="customer_name"
                                v-model="form.customer_name"
                                type="text"
                                autocomplete="off"
                                placeholder="Например, ООО Альфа"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    bg-slate-50
                                    px-4
                                    py-3.5
                                    text-sm
                                    font-medium
                                    text-slate-900
                                    outline-none
                                    transition
                                    placeholder:text-slate-400
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-blue-100
                                "
                                :class="
                                    form.errors.customer_name
                                        ? 'border-rose-300 focus:border-rose-400'
                                        : 'border-slate-200 focus:border-blue-400'
                                "
                            >

                            <p
                                v-if="form.errors.customer_name"
                                class="mt-2 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.customer_name }}
                            </p>
                        </div>


                        <!-- MATERIAL -->

                        <div>
                            <div
                                class="
                                    mb-2
                                    flex
                                    items-end
                                    justify-between
                                    gap-3
                                "
                            >
                                <div>
                                    <label
                                        for="material"
                                        class="
                                            block
                                            text-sm
                                            font-bold
                                            text-slate-800
                                        "
                                    >
                                        Материал / продукция
                                    </label>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Тип материала или выпускаемой продукции
                                    </p>
                                </div>

                                <span
                                    class="
                                        text-[11px]
                                        font-semibold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    "
                                >
                                    Обязательно
                                </span>
                            </div>

                            <input
                                id="material"
                                v-model="form.material"
                                type="text"
                                list="danaflex-materials"
                                autocomplete="off"
                                placeholder="Например, BOPP плёнка"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    bg-slate-50
                                    px-4
                                    py-3.5
                                    text-sm
                                    font-medium
                                    text-slate-900
                                    outline-none
                                    transition
                                    placeholder:text-slate-400
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-blue-100
                                "
                                :class="
                                    form.errors.material
                                        ? 'border-rose-300 focus:border-rose-400'
                                        : 'border-slate-200 focus:border-blue-400'
                                "
                            >

                            <datalist id="danaflex-materials">
                                <option
                                    v-for="materialName in uniqueMaterials"
                                    :key="materialName"
                                    :value="materialName"
                                />
                            </datalist>

                            <p
                                v-if="form.errors.material"
                                class="mt-2 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.material }}
                            </p>
                        </div>


                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                            <!-- QUANTITY -->

                            <div>
                                <label
                                    for="quantity"
                                    class="
                                        block
                                        text-sm
                                        font-bold
                                        text-slate-800
                                    "
                                >
                                    Объём заказа
                                </label>

                                <p class="mt-1 text-xs text-slate-400">
                                    Количество единиц для производства
                                </p>

                                <div class="relative mt-2">
                                    <input
                                        id="quantity"
                                        v-model="form.quantity"
                                        type="number"
                                        min="1"
                                        placeholder="5000"
                                        class="
                                            w-full
                                            rounded-xl
                                            border
                                            bg-slate-50
                                            px-4
                                            py-3.5
                                            pr-16
                                            text-sm
                                            font-bold
                                            text-slate-900
                                            outline-none
                                            transition
                                            placeholder:text-slate-300
                                            focus:bg-white
                                            focus:ring-4
                                            focus:ring-blue-100
                                        "
                                        :class="
                                            form.errors.quantity
                                                ? 'border-rose-300 focus:border-rose-400'
                                                : 'border-slate-200 focus:border-blue-400'
                                        "
                                    >

                                    <span
                                        class="
                                            absolute
                                            right-4
                                            top-1/2
                                            -translate-y-1/2
                                            text-xs
                                            font-semibold
                                            text-slate-400
                                        "
                                    >
                                        ед.
                                    </span>
                                </div>

                                <p
                                    v-if="form.errors.quantity"
                                    class="mt-2 text-xs font-medium text-rose-600"
                                >
                                    {{ form.errors.quantity }}
                                </p>
                            </div>


                            <!-- LINE -->

                            <div>
                                <label
                                    for="production_line_id"
                                    class="
                                        block
                                        text-sm
                                        font-bold
                                        text-slate-800
                                    "
                                >
                                    Производственная линия
                                </label>

                                <p class="mt-1 text-xs text-slate-400">
                                    Можно назначить сразу или оставить в очереди
                                </p>

                                <select
                                    id="production_line_id"
                                    v-model="form.production_line_id"
                                    class="
                                        mt-2
                                        w-full
                                        rounded-xl
                                        border
                                        bg-slate-50
                                        px-4
                                        py-3.5
                                        text-sm
                                        font-semibold
                                        text-slate-800
                                        outline-none
                                        transition
                                        focus:bg-white
                                        focus:ring-4
                                        focus:ring-blue-100
                                    "
                                    :class="
                                        form.errors.production_line_id
                                            ? 'border-rose-300 focus:border-rose-400'
                                            : 'border-slate-200 focus:border-blue-400'
                                    "
                                >
                                    <option value="">
                                        Оставить без назначения
                                    </option>

                                    <option
                                        v-for="line in lines"
                                        :key="line.id"
                                        :value="line.id"
                                    >
                                        {{ line.name }} · {{ line.status }} · {{ line.load_percent }}%
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.production_line_id"
                                    class="mt-2 text-xs font-medium text-rose-600"
                                >
                                    {{ form.errors.production_line_id }}
                                </p>
                            </div>
                        </div>


                        <!-- SELECTED LINE -->

                        <div
                            v-if="selectedLine"
                            class="
                                overflow-hidden
                                rounded-2xl
                                border
                                border-cyan-200
                                bg-gradient-to-r
                                from-cyan-50
                                via-blue-50
                                to-indigo-50
                            "
                        >
                            <div
                                class="
                                    flex
                                    flex-col
                                    justify-between
                                    gap-4
                                    px-5
                                    py-4
                                    sm:flex-row
                                    sm:items-center
                                "
                            >
                                <div class="flex items-center gap-3">
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
                                            {{ selectedLine.material || 'Материал не задан' }}
                                        </p>
                                    </div>
                                </div>

                                <span
                                    class="
                                        inline-flex
                                        self-start
                                        rounded-lg
                                        border
                                        px-2.5
                                        py-1.5
                                        text-xs
                                        font-semibold
                                        sm:self-auto
                                    "
                                    :class="statusClasses(selectedLine.status)"
                                >
                                    {{ selectedLine.status }}
                                </span>
                            </div>

                            <div
                                class="
                                    grid
                                    grid-cols-2
                                    gap-px
                                    border-t
                                    border-cyan-100
                                    bg-cyan-100
                                "
                            >
                                <div class="bg-white/80 px-5 py-4">
                                    <p
                                        class="
                                            text-[10px]
                                            font-bold
                                            uppercase
                                            tracking-wider
                                            text-slate-400
                                        "
                                    >
                                        Температура
                                    </p>

                                    <p
                                        class="mt-1 text-xl font-bold"
                                        :class="
                                            Number(selectedLine.temperature) > 45
                                                ? 'text-rose-600'
                                                : 'text-slate-900'
                                        "
                                    >
                                        {{ selectedLine.temperature }}°C
                                    </p>
                                </div>

                                <div class="bg-white/80 px-5 py-4">
                                    <div
                                        class="
                                            flex
                                            items-center
                                            justify-between
                                            gap-3
                                        "
                                    >
                                        <div>
                                            <p
                                                class="
                                                    text-[10px]
                                                    font-bold
                                                    uppercase
                                                    tracking-wider
                                                    text-slate-400
                                                "
                                            >
                                                Загрузка
                                            </p>

                                            <p class="mt-1 text-xl font-bold text-slate-900">
                                                {{ selectedLine.load_percent }}%
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white/70 px-5 py-4">
                                <div
                                    class="
                                        h-2
                                        overflow-hidden
                                        rounded-full
                                        bg-slate-200
                                    "
                                >
                                    <div
                                        class="
                                            h-full
                                            rounded-full
                                            transition-all
                                            duration-300
                                        "
                                        :class="loadClasses(selectedLine.load_percent)"
                                        :style="{
                                            width: Math.min(
                                                Number(selectedLine.load_percent) || 0,
                                                100
                                            ) + '%',
                                        }"
                                    ></div>
                                </div>

                                <div
                                    v-if="lineNeedsAttention"
                                    class="
                                        mt-3
                                        flex
                                        items-start
                                        gap-2
                                        rounded-xl
                                        border
                                        border-rose-200
                                        bg-rose-50
                                        px-3
                                        py-2.5
                                        text-xs
                                        font-medium
                                        leading-5
                                        text-rose-700
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        class="mt-0.5 h-4 w-4 shrink-0"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 3 2.5 20h19L12 3Zm0 6v5m0 3h.01"
                                        />
                                    </svg>

                                    У линии есть показатель, требующий внимания оператора.
                                    Перед назначением проверьте её состояние.
                                </div>
                            </div>
                        </div>


                        <!-- ACTIONS -->

                        <div
                            class="
                                flex
                                flex-col-reverse
                                gap-3
                                border-t
                                border-slate-100
                                pt-6
                                sm:flex-row
                                sm:items-center
                                sm:justify-between
                            "
                        >
                            <button
                                type="button"
                                @click="cancel"
                                class="
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-white
                                    px-5
                                    py-3
                                    text-sm
                                    font-semibold
                                    text-slate-600
                                    transition
                                    hover:bg-slate-50
                                "
                            >
                                Отмена
                            </button>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="
                                    inline-flex
                                    min-w-[220px]
                                    items-center
                                    justify-center
                                    gap-2
                                    rounded-xl
                                    bg-blue-600
                                    px-5
                                    py-3
                                    text-sm
                                    font-bold
                                    text-white
                                    shadow-lg
                                    shadow-blue-600/20
                                    transition
                                    hover:bg-blue-700
                                    disabled:cursor-not-allowed
                                    disabled:bg-slate-300
                                    disabled:shadow-none
                                "
                            >
                                <svg
                                    v-if="!form.processing"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    class="h-4 w-4"
                                    stroke="currentColor"
                                    stroke-width="1.9"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 5v14M5 12h14"
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
                                    form.processing
                                        ? 'Создаём...'
                                        : 'Создать заказ'
                                }}
                            </button>
                        </div>
                    </form>
                </section>


                <!-- RIGHT PANEL -->

                <aside class="space-y-5">

                    <!-- PLAN -->

                    <section
                        class="
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
                        <div class="border-b border-white/10 p-5">
                            <p
                                class="
                                    text-[11px]
                                    font-bold
                                    uppercase
                                    tracking-[0.18em]
                                    text-cyan-300
                                "
                            >
                                Launch preview
                            </p>

                            <h2 class="mt-2 text-xl font-bold">
                                План нового заказа
                            </h2>

                            <p class="mt-1 text-xs leading-5 text-blue-100/60">
                                Краткая сводка перед добавлением в производственный журнал.
                            </p>
                        </div>

                        <div class="space-y-3 p-5">
                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-white/10
                                    bg-white/[0.05]
                                    px-4
                                    py-3.5
                                "
                            >
                                <p
                                    class="
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-wider
                                        text-blue-300/60
                                    "
                                >
                                    Заказчик
                                </p>

                                <p
                                    class="
                                        mt-1.5
                                        truncate
                                        text-sm
                                        font-bold
                                        text-white
                                    "
                                >
                                    {{ form.customer_name || 'Не указан' }}
                                </p>
                            </div>

                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-white/10
                                    bg-white/[0.05]
                                    px-4
                                    py-3.5
                                "
                            >
                                <p
                                    class="
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-wider
                                        text-blue-300/60
                                    "
                                >
                                    Материал
                                </p>

                                <p class="mt-1.5 text-sm font-bold text-white">
                                    {{ form.material || 'Не указан' }}
                                </p>
                            </div>

                            <div
                                class="
                                    grid
                                    grid-cols-2
                                    gap-3
                                "
                            >
                                <div
                                    class="
                                        rounded-xl
                                        border
                                        border-white/10
                                        bg-white/[0.05]
                                        px-4
                                        py-3.5
                                    "
                                >
                                    <p
                                        class="
                                            text-[10px]
                                            font-bold
                                            uppercase
                                            tracking-wider
                                            text-blue-300/60
                                        "
                                    >
                                        Объём
                                    </p>

                                    <p class="mt-1.5 text-xl font-bold text-cyan-300">
                                        {{ formatNumber(form.quantity) }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-blue-100/50">
                                        единиц
                                    </p>
                                </div>

                                <div
                                    class="
                                        rounded-xl
                                        border
                                        border-white/10
                                        bg-white/[0.05]
                                        px-4
                                        py-3.5
                                    "
                                >
                                    <p
                                        class="
                                            text-[10px]
                                            font-bold
                                            uppercase
                                            tracking-wider
                                            text-blue-300/60
                                        "
                                    >
                                        Статус
                                    </p>

                                    <p class="mt-1.5 text-sm font-bold text-amber-300">
                                        Новый
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-blue-100/50">
                                        ожидает запуска
                                    </p>
                                </div>
                            </div>

                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-white/10
                                    bg-white/[0.05]
                                    px-4
                                    py-3.5
                                "
                            >
                                <p
                                    class="
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-wider
                                        text-blue-300/60
                                    "
                                >
                                    Линия
                                </p>

                                <p class="mt-1.5 text-sm font-bold text-white">
                                    {{
                                        selectedLine
                                            ? selectedLine.name
                                            : 'Не назначена'
                                    }}
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[11px]
                                        text-blue-100/50
                                    "
                                >
                                    {{
                                        selectedLine
                                            ? `${selectedLine.load_percent}% текущая загрузка`
                                            : 'заказ попадёт в очередь'
                                    }}
                                </p>
                            </div>
                        </div>
                    </section>


                    <!-- WORKFLOW -->

                    <section
                        class="
                            rounded-3xl
                            border
                            border-blue-200
                            bg-white
                            p-5
                            shadow-lg
                            shadow-blue-900/[0.05]
                        "
                    >
                        <p class="font-bold text-slate-900">
                            Что произойдёт дальше
                        </p>

                        <div class="mt-5 space-y-4">
                            <div class="flex gap-3">
                                <div
                                    class="
                                        flex
                                        h-7
                                        w-7
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-blue-100
                                        text-xs
                                        font-bold
                                        text-blue-700
                                    "
                                >
                                    1
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-800">
                                        Заказ попадёт в журнал
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Новый заказ появится в общей очереди производства.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div
                                    class="
                                        flex
                                        h-7
                                        w-7
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-cyan-100
                                        text-xs
                                        font-bold
                                        text-cyan-700
                                    "
                                >
                                    2
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-800">
                                        Оператор увидит его на Dashboard
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Заказ отобразится во вкладке «Ожидают».
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div
                                    class="
                                        flex
                                        h-7
                                        w-7
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-emerald-100
                                        text-xs
                                        font-bold
                                        text-emerald-700
                                    "
                                >
                                    3
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-800">
                                        После запуска сменится статус
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Заказ перейдёт в рабочую производственную очередь.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                </aside>
            </div>
        </main>
    </div>
</template>
