<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { login, register, dashboard } from '@/routes';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    }
});
</script>

<template>
    <Head title="Welcome" />

    <div
        class="welcome-bg"
    >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 md:relative bg-white p-3 text-right">
            <Link
                v-if="$page.props.auth.user"
                :href="dashboard()"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Dashboard</Link
            >

            <template v-else>
                <Link
                    :href="login()"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Log in</Link
                >

                <Link
                    v-if="canRegister"
                    :href="register()"
                    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
            </template>
        </div>

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="mt-8">
                <Card class="dark">
                    <CardHeader class="pb-0 border-b">
                        This is the Header!
                    </CardHeader>
                    <CardContent>
                        Glad you found us! Here, you can join or create a pick-em league! Don't know what pick-em is? A pick-em league is where you pick the winner of every NFL game each week. Blah, blah, blah.
                    </CardContent>
                    <!-- <template #header>
                        Testing
                    </template>
                    <template #body>
                        Glad you found us! Here, you can join or create a pick-em league! Don't know what pick-em is? A pick-em league is where you pick the winner of every NFL game each week. Blah, blah, blah.
                    </template> -->
                </Card>                   
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
