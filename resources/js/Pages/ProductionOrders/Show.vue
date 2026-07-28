<script setup>

import { router } from '@inertiajs/vue3'


const props = defineProps({

    order: Object

})



function back(){

    router.visit('/production-orders')

}



function edit(){

    router.visit(
        `/production-orders/${props.order.id}/edit`
    )

}



function deleteOrder(){

    if(confirm('Удалить этот заказ?')){

        router.delete(
            `/production-orders/${props.order.id}`
        )

    }

}



function formatDate(date){

    if(!date){
        return ''
    }


    return new Date(date).toLocaleString('ru-RU',{
        day:'2-digit',
        month:'2-digit',
        year:'numeric',
        hour:'2-digit',
        minute:'2-digit'
    })

}


</script>


<template>


<div class="min-h-screen bg-slate-100 p-8">


    <div
        class="
        max-w-xl
        mx-auto
        bg-white
        rounded-2xl
        shadow-md
        p-8
        "
    >


        <h1 class="text-3xl font-bold mb-8">

            📦 Заказ №{{ order.id }}

        </h1>




        <div class="space-y-6">



            <div>

                <p class="text-gray-500">
                    Клиент
                </p>

                <p class="text-xl font-semibold">
                    {{ order.customer_name }}
                </p>

            </div>





            <div>

                <p class="text-gray-500">
                    Материал
                </p>

                <p class="text-xl font-semibold">
                    {{ order.material }}
                </p>

            </div>





            <div>

                <p class="text-gray-500">
                    Количество
                </p>

                <p class="text-xl font-semibold">
                    {{ order.quantity }}
                </p>

            </div>





            <div>

                <p class="text-gray-500">
                    Статус
                </p>


                <span
                    class="
                    inline-block
                    mt-2
                    px-4
                    py-2
                    rounded-full
                    text-white
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


            </div>





            <div v-if="order.production_line">


                <p class="text-gray-500">
                    Производственная линия
                </p>


                <p class="text-xl font-semibold">

                    ⚙ {{ order.production_line.name }}

                </p>


            </div>





            <div>


                <p class="text-gray-500">
                    Создан
                </p>


                <p>

                    {{ formatDate(order.created_at) }}

                </p>


            </div>






            <div class="flex flex-col gap-3 mt-8">



                <button
                    @click="edit"
                    class="
                    w-full
                    bg-yellow-500
                    text-white
                    py-3
                    rounded-xl
                    hover:bg-yellow-600
                    hover:scale-105
                    transition
                    shadow
                    "
                >

                    ✏️ Изменить

                </button>





                <button
                    @click="deleteOrder"
                    class="
                    w-full
                    bg-red-600
                    text-white
                    py-3
                    rounded-xl
                    hover:bg-red-700
                    hover:scale-105
                    transition
                    shadow
                    "
                >

                    🗑 Удалить

                </button>





                <button
                    @click="back"
                    class="
                    w-full
                    bg-slate-700
                    text-white
                    py-3
                    rounded-xl
                    hover:bg-slate-800
                    transition
                    "
                >

                    ← Назад

                </button>



            </div>



        </div>


    </div>


</div>


</template>
