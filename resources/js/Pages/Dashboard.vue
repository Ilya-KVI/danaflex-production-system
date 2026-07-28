<script setup>

defineProps({
    statistics: Object,
    orders: Array
})

import { router } from '@inertiajs/vue3'


function changeStatus(id, status){

    router.post(
        `/production-orders/${id}/status`,
        {
            status: status
        }
    )

}

</script>


<template>

<div class="p-8">

    <h1 class="text-3xl font-bold mb-8">
        Danaflex Production System
    </h1>


    <div class="grid grid-cols-3 gap-6">


        <div class="bg-white shadow rounded p-6">

            <h2 class="text-gray-500">
                Всего заказов
            </h2>

            <p class="text-4xl font-bold">
                {{ statistics.total }}
            </p>

        </div>



        <div class="bg-white shadow rounded p-6">

            <h2 class="text-gray-500">
                В производстве
            </h2>

            <p class="text-4xl font-bold">
                {{ statistics.inProduction }}
            </p>

        </div>



        <div class="bg-white shadow rounded p-6">

            <h2 class="text-gray-500">
                Завершено
            </h2>

            <p class="text-4xl font-bold">
                {{ statistics.completed }}
            </p>

        </div>


    </div>



    <h2 class="text-2xl font-bold mt-10 mb-5">
        Последние заказы
    </h2>



    <table class="w-full bg-white shadow rounded">

        <thead>

        <tr class="border-b">

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
            class="border-b"
        >

            <td class="p-4">
                {{ order.customer_name }}
            </td>


            <td class="p-4">
                {{ order.material }}
            </td>


            <td class="p-4">
                {{ order.quantity }}
            </td>


            <td class="p-4">

                <p>
                    {{ order.status }}
                </p>


                <button
                    v-if="order.status === 'Новый'"
                    @click="changeStatus(order.id, 'В производстве')"
                    class="mt-2 px-3 py-1 bg-blue-500 text-white rounded"
                >
                    Запустить
                </button>


                <button
                    v-if="order.status === 'В производстве'"
                    @click="changeStatus(order.id, 'Готово')"
                    class="mt-2 px-3 py-1 bg-green-500 text-white rounded"
                >
                    Завершить
                </button>

            </td>


        </tr>


        </tbody>


    </table>


</div>

</template>
