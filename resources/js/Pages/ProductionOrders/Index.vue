<script setup>

import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'


const props = defineProps({
    orders: Array
})


const search = ref('')

const selectedStatus = ref('Все')


const statuses = [
    'Все',
    'Новый',
    'В производстве',
    'Готово'
]



const filteredOrders = computed(() => {

    return props.orders.filter(order => {


        const searchText = search.value.toLowerCase()


        const matchesSearch =
            order.customer_name
                .toLowerCase()
                .includes(searchText)
            ||
            order.material
                .toLowerCase()
                .includes(searchText)



        const matchesStatus =
            selectedStatus.value === 'Все'
            ||
            order.status === selectedStatus.value



        return matchesSearch && matchesStatus


    })

})



function deleteOrder(id){

    if(confirm('Удалить этот заказ?')){

        router.delete(
            `/production-orders/${id}`,
            {
                preserveScroll: true
            }
        )

    }

}



function editOrder(id){

    router.visit(
        `/production-orders/${id}/edit`
    )

}


</script>



<template>

<div class="min-h-screen bg-slate-100 p-8">


    <h1 class="text-4xl font-bold mb-8">
        📦 Все производственные заказы
    </h1>



    <!-- SEARCH -->

    <div class="bg-white rounded-2xl shadow p-6 mb-8">


        <input
            v-model="search"
            placeholder="🔎 Поиск клиента или материала..."
            class="
            border
            rounded-xl
            px-4
            py-3
            w-full
            "
        />


    </div>




    <!-- FILTERS -->


    <div class="flex gap-4 mb-8">


        <button
            v-for="status in statuses"
            :key="status"
            @click="selectedStatus = status"
            class="
            px-5
            py-3
            rounded-xl
            font-semibold
            transition
            "
            :class="{

                'bg-blue-600 text-white shadow':
                selectedStatus === status,


                'bg-white text-gray-700 hover:bg-gray-100':
                selectedStatus !== status

            }"
        >

            {{ status }}

        </button>


    </div>





    <!-- TABLE -->


    <div class="bg-white rounded-2xl shadow overflow-hidden">


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


                <th class="p-4 text-left">
                    Создан
                </th>


                <th class="p-4 text-left">
                    Действия
                </th>


            </tr>


            </thead>



            <tbody>


            <tr
                v-for="order in filteredOrders"
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


                </td>




                <td class="p-4 text-gray-500">

                    {{ order.created_at }}

                </td>




                <td class="p-4">


                    <div class="flex gap-2">


                    <button
                    @click="router.visit(`/production-orders/${order.id}`)"
                    class="
                    px-4
                    py-2
                    bg-blue-600
                    text-white
                    rounded-lg
                    hover:bg-blue-700
                    transition
                    "
                    >

                    👁 Просмотр

                    </button>

                        <button
                            @click="editOrder(order.id)"
                            class="
                            px-4
                            py-2
                            bg-yellow-500
                            text-white
                            rounded-lg
                            hover:bg-yellow-600
                            transition
                            "
                        >

                            ✏️ Изменить

                        </button>



                        <button
                            @click="deleteOrder(order.id)"
                            class="
                            px-4
                            py-2
                            bg-red-600
                            text-white
                            rounded-lg
                            hover:bg-red-700
                            transition
                            "
                        >

                            🗑 Удалить

                        </button>


                    </div>


                </td>



            </tr>


            </tbody>


        </table>


    </div>



</div>


</template>
