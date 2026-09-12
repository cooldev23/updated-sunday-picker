<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onBeforeMount } from 'vue';

let props = defineProps({
    leagues: Array,
    user: Object
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthLayout>
        <div class="py-12">
            <div class="p-3 max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white shadow-sm">
                <h2 class="mb-3 text-3xl border-b border-blue-200">My Leagues</h2>
                <div v-if="leagues.length">
                    <div class="mb-3" v-for="league in leagues" :key="league.id">
                        <div class="pb-3 flex justify-evenly">
                            <Card class="me-3 min-h-fit self-start w-96 shrink-0">
                                <template #header>
                                    <div class="bg-gray-200">
                                        <h3 class="text-2xl text-center"><Link
                                            :href="route('league.show', league)"
                                            class="font-semibold text-blue-600 hover:text-gray-900 focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                            >{{ league.name }}
                                            </Link>
                                        </h3>
                                        <p class="text-center">{{ league.motto }}</p>
                                    </div>
                                </template>
                                <template #body>
                                    <p>Week {{ $page.props.currentWeek }} Picks</p>
                                    <div class="mb-3 flex justify-around flex-wrap">
                                        <span v-for="pick in league.picks" :key="pick.game_id" class="mx-2 mb-1 shadow-sm inline-flex justify-center items-center rounded-full bg-gray-50 px-2 py-1 text-lg w-16 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ pick.winner }}</span>
                                    </div>
                                   <div>
                                        <Link :href="route('edit.picks', {user: user, league: league})" class="ms-4 rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">{{ league.picks.length ? 'Edit' : 'Make' }} Picks</Link>
                                   </div>
                                </template>
                            </Card>
                            <div class="flex flex-col items-center">
                                <div class="mb-3 flex justify-evenly">
                                    <Card class="mx-2 h-20 w-52">
                                        <template #body>
                                        <Link :href="route('league.weeklyPicks', {league: league})" class="ms-4 rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">View Weekly Picks</Link>
                                        </template>
                                    </Card>
                                    <Card class="mx-2 h-20 w-52">
                                        <template #body>
                                            Season Leader: figure this out
                                        </template>
                                    </Card>
                                </div>
                                <div class="flex justify-center">
                                    <Card class="mx-2 h-20 w-52">
                                        <template #body>
                                            Last Week's Winner figure this out
                                        </template>
                                    </Card>
                                    <Card class="mx-2 h-20 w-52">
                                        <template #body>
                                            Season Standings (link)
                                        </template>
                                    </Card>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <p>Looks like you don't belong to any leagues yet!  Let's fix that!</p>
                    <Link :href="route('league.create')" class="ms-4 rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Create League</Link>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
