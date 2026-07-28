<script setup>

defineProps({
    orders: Array
})

</script>


<template>

<div>

    <h1>
        Производственные заказы Danaflex
    </h1>


    <div v-for="order in orders" :key="order.id">

        <p>
            Клиент: {{ order.customer_name }}
        </p>

        <p>
            Материал: {{ order.material }}
        </p>

        <p>
            Количество: {{ order.quantity }} кг
        </p>

        <p>
            Статус: {{ order.status }}
        </p>

        <hr>

    </div>

</div>

</template>
