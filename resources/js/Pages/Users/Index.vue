<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {onMounted, ref} from "vue";

const users = ref(null);

const getUsers = () => {
    axios.post(route('users.data'))
        .then(response => {
            users.value = response.data;
        })
}

onMounted(() => getUsers());

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                </div>
            </div>
        </div>
    </AppLayout>
</template>
