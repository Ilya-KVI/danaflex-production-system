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






function formatDate(date){


    if(!date){

        return ''

    }


    return new Date(date).toLocaleString(
        'ru-RU',
        {

            day:'2-digit',

            month:'2-digit',

            year:'numeric',

            hour:'2-digit',

            minute:'2-digit'

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







function editOrder(id){


    router.visit(

        `/production-orders/${id}/edit`

    )


}






function showOrder(id){


    router.visit(

        `/production-orders/${id}`

    )


}



</script>





<template>


<div class="min-h-screen bg-slate-100 p-8">



<h1 class="text-4xl font-bold text-slate-800 mb-8">

📦 Все производственные заказы

</h1>






<!-- SEARCH -->


<div
class="
bg-white
rounded-2xl
shadow-md
p-6
mb-6
"
>


<input

v-model="search"

placeholder="🔎 Поиск клиента или материала..."

class="
w-full
border
rounded-xl
px-4
py-3
focus:outline-none
focus:ring-2
focus:ring-blue-500
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



'bg-white hover:bg-gray-100':

selectedStatus !== status



}"

>

{{ status }}


</button>


</div>








<!-- TABLE -->


<div
class="
bg-white
rounded-2xl
shadow-md
overflow-hidden
"
>



<table
v-if="filteredOrders.length"
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
Создан
</th>


<th class="p-4 text-center">
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

{{ formatDate(order.created_at) }}

</td>






<td class="p-4">


<div
class="
flex
flex-col
gap-2
items-center
"
>




<button

@click="showOrder(order.id)"

class="
w-36
px-4
py-2
bg-blue-600
text-white
rounded-lg
shadow
transition
hover:bg-blue-700
hover:scale-105
"

>

👁 Просмотр

</button>







<button

@click="editOrder(order.id)"

class="
w-36
px-4
py-2
bg-yellow-500
text-white
rounded-lg
shadow
transition
hover:bg-yellow-600
hover:scale-105
"

>

✏️ Изменить

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
shadow
transition
hover:bg-red-700
hover:scale-105
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
p-12
text-center
text-gray-500
"
>

📦 Заказов не найдено


</div>





</div>




</div>


</template>
