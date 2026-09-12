<script setup>
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import AuthLayout from '@/layouts/AuthLayout.vue';
import Card from '@/components/ui/card/Card.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/solid';

DataTable.use(DataTablesCore);

let props = defineProps({
    league: Object,
    winnerId: Number,
    totalGamesWeek: Number,
    lastGameOfWeek: Object,
    leagueCanShowGames: Boolean
});

function getMemberTiebreakValue(picks) {
    let tiebreak = picks.filter((pick) => pick.tiebreaker !== null)[0];
    return tiebreak ? tiebreak.tiebreaker : 0;
}

function getAvgWeeklyCorrect(leagueMember) {
    let totalCorrect = 0,
        avgPercentCorrectPerWeek = 0;
    if (leagueMember.stats_latest) {
        for (const el of leagueMember.stats) {
            let weekCorrect = el.total_correct_wk / el.total_games_wk * 100;
            totalCorrect += weekCorrect;
        }
        avgPercentCorrectPerWeek = totalCorrect / leagueMember.stats.length;

        return avgPercentCorrectPerWeek.toFixed(2);
    }
    return 'No stats yet';
}

function getYearPercentCorrect(leagueMember) {
    if (leagueMember.stats_latest) {
        let correctPicks = leagueMember.picks.filter((pick) => pick.correct);
        return ((correctPicks.length/leagueMember.stats_latest.total_games_yr) * 100).toFixed(2);
    }

    return 'No stats yet';
}
</script>

<template>
    <Head title="League Results" />

    <AuthLayout>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mb-3 pb-3 text-center border-b border-blue-200">
                        <h2 class="mb-1 text-3xl">{{ league.name }}</h2>
                        <h3 class="mb-1 text-xl">{{ league.motto }}</h3>
                        <a v-if="league.league_creator_id === $page.props.auth.user.id" :href="route('user.dashboard')" class="px-2.5 py-1.5 rounded-md bg-blue-600 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Manage League</a>
                    </div>
                    <Card class="mb-16 border border-gray-400 shadow-sm">
                        <template #header>
                            <div class="p-3 bg-gray-200">Weekly Pick Results</div>
                        </template>
                        <template #body>
                            <DataTable class="display">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-start">Name</th>
                                        <th scope="col" class="text-start">Week's Pick{{ league.league_type_id !== 2 ? 's' : '' }}</th>
                                        <th v-if="league.league_type_id !== 2" scope="col" class="text-center">{{ league.league_type_id === 3 ? 'Total Weight' : 'Tiebreaker' }}</th>
                                        <th v-else scope="col" class="text-center">Eliminated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="member in league.users" :key="member.id">
                                        <td>
                                            <p>{{ member.name }}</p>
                                            <div v-if="winnerId === member.id" class="mb-1 py-1 shadow-sm grow inline-flex justify-center items-center rounded-full bg-green-500  text-sm w-32 font-medium text-white ring-1 ring-inset ring-gray-500/10">
                                                <span >Week Winner</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div v-if="league.league_type_id !== 2">
                                                <div v-if="$page.props.auth.user.id === member.id || leagueCanShowGames" class="flex justify-between items-center">
                                                    <p v-for="pick in member.picks" class="w-11 h-16 shadow-sm flex flex-col justify-center items-center rounded-sm" :class="{'border border-red-400 text-red-400': !pick.correct}, {'border border-green-600 text-green-600': pick.correct}">
                                                    <CheckIcon v-if="pick.correct" class="h-6 w-6 text-green-600"></CheckIcon>
                                                    <XMarkIcon v-else class="h-6 w-6 text-red-600"></XMarkIcon>
                                                    {{ pick.winner }}</p>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div v-if="$page.props.auth.user.id === member.id || leagueCanShowGames" class="flex justify-between items-center">
                                                    <p v-for="pick in member.picks" class="w-11 h-16 shadow-sm flex flex-col justify-center items-center rounded-sm" :class="{ 'border border-red-400 text-red-400': !pick.correct }, { 'border border-green-600 text-green-600': pick.correct }">
                                                    <CheckIcon v-if="pick.correct" class="h-6 w-6 text-green-600"></CheckIcon>
                                                    <XMarkIcon v-else class="h-6 w-6 text-red-600"></XMarkIcon>
                                                    {{ $pick.winner }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ getMemberTiebreakValue(member.picks) }}</td>
                                    </tr>
                                </tbody>
                            </DataTable>
                        </template>
                    </Card>
                    <Card class="mb-8 border border-gray-400 shadow-sm">
                        <template #header>
                            <div class="p-3 bg-gray-200">Weekly Correct per Member</div>
                        </template>
                        <template #body>
                            <DataTable class="display">
                                <thead>
                                    <tr>
                                        <th class="text-start">Name</th>
                                        <th scope="col">Correct This Week</th>
                                        <th scope="col">Weekly % Correct</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="member in league.users" :key="member.id">
                                        <td>
                                            {{ member.name }}
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                {{ member.stats_latest.total_correct_wk ?? 'No stats yet' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                {{ ((member.stats_latest.total_correct_wk / member.stats_latest.total_games_wk) * 100).toFixed(2) ?? 'No stats yet' }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody> 
                            </DataTable>
                        </template>
                    </Card>
                    <Card class="mb-8 border border-gray-400 shadow-sm">
                        <template #header>
                            <div class="p-3 bg-gray-200">Yearly Correct per Member</div>
                        </template>
                        <template #body>
                            <DataTable class="display">
                                <thead>
                                    <tr>
                                        <th class="text-start">Name</th>
                                        <th>Total Correct</th>
                                        <th>Avg Weekly % Correct</th>
                                        <th>Ytd % Correct</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="member in league.users" :key="member.id">
                                        <td>
                                            {{ member.name }}
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                {{ member.stats_latest.total_correct_yr ?? 'No stats yet' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                {{ getAvgWeeklyCorrect(member) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                {{ getYearPercentCorrect(member) }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody> 
                            </DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </AuthLayout>
</template>

<style>
@import 'datatables.net-dt';

table.dataTable > thead > tr > th:not(.text-start) {
    text-align: center;
}
</style>