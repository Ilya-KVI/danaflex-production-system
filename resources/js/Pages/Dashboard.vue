```vue
<script setup>

import { router } from '@inertiajs/vue3'


const props = defineProps({
    statistics: Object,
    orders: Array,
    line: Object
})


function changeStatus(id, status){

    router.post(
        `/production-orders/${id}/status`,
        {
            status: status
        },
        {
            preserveScroll: true
        }
    )

}

</script>


<template>

<div class="min-h-screen bg-slate-100 p-8">

    <!-- HEADER -->

    <div class="flex justify-between items-center mb-10">


        <div>

            <h1 class="text-4xl font-bold text-slate-800">
                🏭 Danaflex Production System
            </h1>


            <p class="text-slate-500 mt-2">
                Производственный мониторинг и управление заказами
            </p>

        </div>



        <div
            class="
            bg-green-100
            text-green-700
            px-5
            py-3
            rounded-xl
            font-semibold
            shadow
            "
        >

            ● Система активна

        </div>


    </div>




    <!-- LINE STATUS -->


    <div
        class="
        bg-white
        rounded-2xl
        shadow-md
        p-6
        mb-10
        flex
        justify-between
        items-center
        "
    >


        <div>

            <h2 class="text-xl font-bold">
            ⚙ {{ line.name }}
            </h2>


            <p class="text-gray-500 mt-2">
            Температура оборудования:
            {{ line.temperature }}°C
            </p>


            <div class="text-green-600 font-bold">
            ● {{ line.status }}
            </div>


    </div>

    </div>






    <!-- CARDS -->


    <div class="grid grid-cols-3 gap-6 mb-10">



        <div class="bg-white rounded-2xl shadow-md p-6">


            <h2 class="text-xl font-bold">
                🏭 Линия №1
            </h2>


            <p class="text-gray-500 mt-3">
                Производство пленочных материалов
            </p>


            <div class="mt-6">


                <div class="flex justify-between mb-2">

                    <span>
                        Загрузка
                    </span>


                    <b>
                        75%
                    </b>

                </div>


                <div class="w-full bg-gray-200 h-3 rounded-full">


                    <div
                        class="
                        bg-gradient-to-r
                        from-blue-500
                        to-cyan-400
                        h-3
                        rounded-full
                        "
                        style="width:75%"
                    ></div>


                </div>


            </div>


        </div>






        <div class="bg-white rounded-2xl shadow-md p-6">


            <h2 class="text-xl font-bold">
                📦 Материал
            </h2>


            <p class="text-3xl font-bold mt-5">
                PE-500
            </p>


            <p class="text-gray-500 mt-2">
                Полиэтиленовая пленка
            </p>


        </div>






        <div class="bg-white rounded-2xl shadow-md p-6">


            <h2 class="text-xl font-bold">
                ⚙ Состояние
            </h2>


            <p class="text-3xl font-bold text-green-600 mt-5">
                Норма
            </p>


            <p class="text-gray-500 mt-2">
                Оборудование работает стабильно
            </p>


        </div>



    </div>






    <!-- STATISTICS -->


    <div class="grid grid-cols-3 gap-6 mb-10">


        <div class="
            bg-white
            rounded-2xl
            shadow-md
            p-6
            border-l-4
            border-blue-500
        ">


            <p class="text-gray-500">
                Всего заказов
            </p>


            <p class="text-5xl font-bold text-blue-600 mt-3">
                {{ statistics.total }}
            </p>


        </div>





        <div class="
            bg-white
            rounded-2xl
            shadow-md
            p-6
            border-l-4
            border-yellow-500
        ">


            <p class="text-gray-500">
                В производстве
            </p>


            <p class="text-5xl font-bold text-yellow-600 mt-3">
                {{ statistics.inProduction }}
            </p>


        </div>





        <div class="
            bg-white
            rounded-2xl
            shadow-md
            p-6
            border-l-4
            border-green-500
        ">


            <p class="text-gray-500">
                Завершено
            </p>


            <p class="text-5xl font-bold text-green-600 mt-3">
                {{ statistics.completed }}
            </p>


        </div>


    </div>






    <!-- ORDERS -->


    <div class="bg-white rounded-2xl shadow-md overflow-hidden">


        <div class="p-6 border-b">


            <h2 class="text-2xl font-bold">
                📦 Последние производственные заказы
            </h2>


        </div>



        <table class="w-full">


            <thead class="bg-slate-50">


            <tr>


                <th class="p-4 text-left">
                    Клиент
                </th>


                <th class="p-4 text-left">
                    Материал
                </th>


                <th class="p-4 text-left">
                    Количество
                </th>


                <th class="p-4 text-left">
                    Статус
                </th>


            </tr>


            </thead>




            <tbody>


            <tr
                v-for="order in orders"
                :key="order.id"
                class="
                border-b
                hover:bg-slate-50
                transition
                "
            >


                <td class="p-4 font-semibold">
                    {{ order.customer_name }}
                </td>



                <td class="p-4">
                    {{ order.material }}
                </td>



                <td class="p-4">
                    {{ order.quantity }}
                </td>




                <td class="p-4">


                    <span
                        class="
                        px-4
                        py-2
                        rounded-full
                        text-white
                        text-sm
                        font-semibold
                        "
                        :class="{

                        'bg-gray-500':
                        order.status === 'Новый',

                        'bg-blue-500':
                        order.status === 'В производстве',

                        'bg-green-500':
                        order.status === 'Готово'

                        }"
                    >

                        {{ order.status }}

                    </span>



                    <button
                        v-if="order.status === 'Новый'"
                        @click="changeStatus(order.id,'В производстве')"
                        class="
                        ml-3
                        px-4
                        py-2
                        bg-blue-600
                        text-white
                        rounded-lg
                        "
                    >

                        ▶ Запустить

                    </button>



                    <button
                        v-if="order.status === 'В производстве'"
                        @click="changeStatus(order.id,'Готово')"
                        class="
                        ml-3
                        px-4
                        py-2
                        bg-green-600
                        text-white
                        rounded-lg
                        "
                    >

                        ✓ Завершить

                    </button>



                </td>


            </tr>


            </tbody>


        </table>


    </div>


</div>


</template>
```
