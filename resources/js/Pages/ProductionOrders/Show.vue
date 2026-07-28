<script setup>

import { router } from '@inertiajs/vue3'


const props = defineProps({

    order: Object

})


function back(){

    router.visit('/production-orders')

}


</script>


<template>

<div class="min-h-screen bg-slate-100 p-8">


    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow p-8">


        <h1 class="text-3xl font-bold mb-8">
            📦 Заказ №{{ order.id }}
        </h1>


        <div class="space-y-5">


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



            <div>

                <p class="text-gray-500">
                    Создан
                </p>

                <p>
                    {{ order.created_at }}
                </p>

            </div>



            <button
                @click="back"
                class="
                w-full
                mt-5
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

</template>
