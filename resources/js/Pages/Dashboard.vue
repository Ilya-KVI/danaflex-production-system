<script setup>

import { router } from '@inertiajs/vue3'


const props = defineProps({
    statistics: Object,
    orders: Array,
    lines: Array
})



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




function changeStatus(id,status){

    router.post(
        `/production-orders/${id}/status`,
        {
            status: status
        },
        {
            preserveScroll:true
        }
    )

}




function startLine(id){

    router.post(
        `/production-lines/${id}/start`,
        {},
        {
            preserveScroll:true
        }
    )

}





function stopLine(id){

    router.post(
        `/production-lines/${id}/stop`,
        {},
        {
            preserveScroll:true
        }
    )

}




function deleteOrder(id){

    if(confirm('Удалить этот заказ?')){

        router.delete(
            `/production-orders/${id}`,
            {
                preserveScroll:true
            }
        )

    }

}




function createOrder(){

    router.visit('/production-orders/create')

}


</script>


<template>


<div class="min-h-screen bg-slate-100 p-8">


<!-- HEADER -->


<div class="flex gap-4 items-center mb-10">


<div>

<h1 class="text-4xl font-bold text-slate-800">
🏭 Danaflex Production System
</h1>


<p class="text-slate-500 mt-2">
Производственный мониторинг и управление заказами
</p>


</div>



<button
@click="createOrder"
class="
bg-blue-600
text-white
px-5
py-3
rounded-xl
font-semibold
shadow
transition
hover:bg-blue-700
hover:scale-105
"
>

+ Новый заказ

</button>




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





<!-- LINES -->


<div class="grid grid-cols-3 gap-6 mb-10">


<div
v-for="line in lines"
:key="line.id"
class="
bg-white
rounded-2xl
shadow-md
p-6
"
>


<h2 class="text-xl font-bold">
⚙ {{ line.name }}
</h2>


<p class="mt-3 font-semibold">

Температура:

<span
:class="{

'text-green-600': line.temperature <=40,

'text-yellow-500':
line.temperature >40 &&
line.temperature <=45,

'text-red-600':
line.temperature >45

}"
>

{{ line.temperature }}°C

</span>

</p>



<p class="text-gray-500">

Материал:
{{ line.material }}

</p>




<div class="mt-4">


<span
class="
px-4
py-2
rounded-full
font-semibold
"
:class="{

'bg-green-100 text-green-700':
line.status === 'Работает',

'bg-red-100 text-red-700':
line.status === 'Остановлена'

}"
>

● {{ line.status }}

</span>




<div class="mt-5 flex justify-center">


<button
v-if="line.status !== 'Работает'"
@click="startLine(line.id)"
class="
w-36
px-4
py-2
bg-green-600
text-white
rounded-lg
transition
hover:bg-green-700
hover:scale-105
shadow
"
>

▶ Запустить

</button>



<button
v-if="line.status === 'Работает'"
@click="stopLine(line.id)"
class="
w-36
px-4
py-2
bg-red-600
text-white
rounded-lg
transition
hover:bg-red-700
hover:scale-105
shadow
"
>

■ Остановить

</button>


</div>


</div>




<div class="mt-5">


<div class="flex justify-between">

<span>
Загрузка
</span>


<b>
{{ line.load_percent }}%
</b>


</div>



<div class="w-full bg-gray-200 rounded-full h-3 mt-2">


<div
class="
bg-blue-500
h-3
rounded-full
"
:style="{
width: line.load_percent + '%'
}"
></div>


</div>


</div>


</div>


</div>







<!-- STATISTICS -->


<div class="grid grid-cols-3 gap-6 mb-10">


<div
class="
bg-white
rounded-2xl
shadow-md
p-6
border-l-4
border-blue-500
"
>

<p class="text-gray-500">
Всего заказов
</p>


<p class="text-5xl font-bold text-blue-600 mt-3">

{{ statistics.total }}

</p>

</div>




<div
class="
bg-white
rounded-2xl
shadow-md
p-6
border-l-4
border-yellow-500
"
>

<p class="text-gray-500">
В производстве
</p>


<p class="text-5xl font-bold text-yellow-600 mt-3">

{{ statistics.inProduction }}

</p>

</div>




<div
class="
bg-white
rounded-2xl
shadow-md
p-6
border-l-4
border-green-500
"
>

<p class="text-gray-500">
Завершено
</p>


<p class="text-5xl font-bold text-green-600 mt-3">

{{ statistics.completed }}

</p>

</div>


</div>







<!-- ORDERS -->


<div
class="
bg-white
rounded-2xl
shadow-md
overflow-hidden
"
>


<div class="p-6 border-b">

<h2 class="text-2xl font-bold">
📦 Последние производственные заказы
</h2>

</div>



<table
v-if="orders.length"
class="w-full"
>


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
Дата
</th>


<th class="p-4 text-left">
Действия
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
font-semibold
"
:class="{

'bg-gray-500':
order.status==='Новый',

'bg-blue-500':
order.status==='В производстве',

'bg-green-500':
order.status==='Готово'

}"
>

{{ order.status }}

</span>


</td>




<td class="p-4 text-gray-500">

{{ formatDate(order.created_at) }}

</td>




<td class="p-4">


<div class="flex flex-col gap-2 items-center">


<button
v-if="order.status==='Новый'"
@click="changeStatus(order.id,'В производстве')"
class="
w-36
px-4
py-2
bg-blue-600
text-white
rounded-lg
transition
hover:bg-blue-700
hover:scale-105
shadow
"
>

▶ Запустить

</button>



<button
v-if="order.status==='В производстве'"
@click="changeStatus(order.id,'Готово')"
class="
w-36
px-4
py-2
bg-green-600
text-white
rounded-lg
transition
hover:bg-green-700
hover:scale-105
shadow
"
>

✓ Завершить

</button>



<button
@click="deleteOrder(order.id)"
class="
w-36
px-4
py-2
bg-red-600
text-white
rounded-lg
transition
hover:bg-red-700
hover:scale-105
shadow
"
>

🗑 Удалить

</button>


</div>


</td>



</tr>


</tbody>


</table>




<div
v-else
class="
p-10
text-center
text-gray-500
"
>

📦 Заказов пока нет

</div>



</div>



</div>


</template>
