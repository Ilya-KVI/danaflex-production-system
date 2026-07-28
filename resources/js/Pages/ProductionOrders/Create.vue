<script setup>

import { router } from '@inertiajs/vue3'

import { ref } from 'vue'


const customer_name = ref('')
const material = ref('')
const quantity = ref('')



function submit(){

    router.post(
        '/production-orders',
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


    <div class="bg-white rounded-2xl shadow-md p-8 max-w-xl">


        <h1 class="text-3xl font-bold mb-6">
            📦 Новый производственный заказ
        </h1>



        <div class="space-y-4">


            <input
                v-model="customer_name"
                placeholder="Название клиента"
                class="
                w-full
                border
                rounded-lg
                p-3
                "
            >



            <input
                v-model="material"
                placeholder="Материал"
                class="
                w-full
                border
                rounded-lg
                p-3
                "
            >



            <input
                v-model="quantity"
                type="number"
                placeholder="Количество"
                class="
                w-full
                border
                rounded-lg
                p-3
                "
            >



            <button
                @click="submit"
                class="
                bg-blue-600
                text-white
                px-6
                py-3
                rounded-lg
                hover:bg-blue-700
                "
            >
                Создать заказ
            </button>


        </div>


    </div>


</div>


</template>
