<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

const message = ref('');

function submit() {
    axios.post('create-schedule')
    .then(response => {
        message.value = response.data.success
    })
}
</script>

<template>
    <Head title="Create Schedule"></Head>
    <AuthLayout>
        <div class="text-center">
            <button type="button" class="mt-3 px-2.5 py-1.5 rounded-md bg-blue-500 border border-blue-500 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600" @click.prevent="submit">Get Schedule From Here</button>
        </div>
        <p v-if="message">{{ message }}</p>
    </AuthLayout>
</template>