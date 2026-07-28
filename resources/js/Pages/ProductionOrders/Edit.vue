<script setup>

import { router } from '@inertiajs/vue3'
import { ref } from 'vue'


const props = defineProps({

    order: Object

})



const customer_name = ref(props.order.customer_name)

const material = ref(props.order.material)

const quantity = ref(props.order.quantity)



function updateOrder(){


    router.put(
        `/production-orders/${props.order.id}`,
        {
            customer_name: customer_name.value,
            material: material.value,
            quantity: quantity.value
        }
    )


}



</script>



<template>


<div class="min-h-screen bg-slate-100 p-8">


    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow p-8">


        <h1 class="text-3xl font-bold mb-8">

            ✏️ Редактирование заказа

        </h1>




        <div class="space-y-5">



            <div>

                <label class="block mb-2 font-semibold">

                    Клиент

                </label>


                <input
                    v-model="customer_name"
                    class="
                    w-full
                    border
                    rounded-xl
                    px-4
                    py-3
                    "
                />


            </div>




            <div>


                <label class="block mb-2 font-semibold">

                    Материал

                </label>


                <input
                    v-model="material"
                    class="
                    w-full
                    border
                    rounded-xl
                    px-4
                    py-3
                    "
                />


            </div>





            <div>


                <label class="block mb-2 font-semibold">

                    Количество

                </label>


                <input
                    v-model="quantity"
                    type="number"
                    class="
                    w-full
                    border
                    rounded-xl
                    px-4
                    py-3
                    "
                />


            </div>





            <button
                @click="updateOrder"
                class="
                w-full
                bg-blue-600
                text-white
                py-3
                rounded-xl
                font-semibold
                hover:bg-blue-700
                transition
                "
            >

                💾 Сохранить изменения

            </button>




        </div>


    </div>


</div>


</template>
