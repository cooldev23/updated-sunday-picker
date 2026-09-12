<script setup>
import GameContainer from './GameContainer.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useSelectedTeamsStore } from '@/Stores/SelectedTeamsStore.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onBeforeMount, reactive } from 'vue';

// stores
const selectedTeamsStore = useSelectedTeamsStore();

// props
const props = defineProps({
    thisWeek: Array,
    user: Object,
    currentWeek: Number,
    league: Object,
    lastGameOfWeek: Object,
    byes: Array,
    userPicks: {
        type: Array,
        default() {
            return []
        }
    },
    timezone: String
});

// data
const allGames = ref(props.thisWeek),
    currWeek = ref(props.currentWeek),
    weekByes = ref(props.byes),
    tiebreaker = ref(0),
    errorMessage = ref('Some games not picked for '),
    selectedWeek = ref(props.currentWeek);

// computed
const displayByes = computed(() => {
    return props.byes.length ? props.byes.join(', ') : 'NONE';
});
const countPicks = computed(() => {
    if (props.league.league_type_id === 2) {
        if (selectedTeamsStore.userSelectedTeams.length === 1) {
            return false;
        }
        return true;
    }

    return selectedTeamsStore.userSelectedTeams.length < props.thisWeek.length;
});
const allWeeks = computed(() => {
    let weeks = [];
    for (let x = 1; x <= 18; x++) {
        weeks.push(x);
    }
    return weeks;
});

// named functions
function getSelectedTeam(game) {
    let selectedTeams = props.userPicks;
    for (const selected of selectedTeams) {
        if (selected.gameId === game.global_game_id) {
            return selected
        }
    }
    return {
        team: null,
        gameId: game.global_game_id,
        weight: 0
    }
}

function resetSurvivorPick(e) {
    selectedTeamsStore.resetSelectedPicks();
}

function submitPicks() {
    router.patch('/picks/' + props.user.id + '/' + props.league.id, { data: selectedTeamsStore.userSelectedTeams, user: props.user, league: props.league, selectedWeek: currWeek.value, tiebreaker: Number(tiebreaker.value) });
}

function changeWeek() {
    router.get('/user/' + props.user.id + '/league/' + props.league.id + '/' + selectedWeek.value);
}

function getTiebreakerValue() {
    if (selectedTeamsStore.userSelectedTeams.length && props.league.league_type_id !== 2) {
        let lastGameOfWeek = selectedTeamsStore.userSelectedTeams.filter((obj) => obj.tiebreaker !== null)[0];
        return lastGameOfWeek ? lastGameOfWeek.tiebreaker : 0; 
    }
    return 0;
}

// hooks
onBeforeMount(() => {
    selectedTeamsStore.hydrateSelectedTeams(props.userPicks);
    tiebreaker.value = getTiebreakerValue();
    selectedTeamsStore.setWeekGamesCount(props.thisWeek.length);
})
</script>

<template>
    <Head title="Edit Picks"></Head>
    <AuthLayout>
        <div class="mx-auto pb-4 pt-3 sm:px-6 lg:px-8 max-w-7xl">
            <div class="pb-1 flex flex-col md:flex-row justify-between items-end border-b border-gray-400">
                <h2 class="text-xl">{{ league.name }}</h2>
                <div id="teamsOnBye" class="d-flex justify-content-center align-items-base flex-wrap">
                    <h5 class="mb-2 md:mb-0">Byes: <span class="teams-on-bye">{{ displayByes }}</span></h5>
                </div>
                <div>
                    <select id="weeks" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6"
                        @change="changeWeek()" aria-label="select weeks"
                        v-model="selectedWeek"
                        >
                        <option v-for="(week, index) in allWeeks" :key="index" :value="index + 1">Week {{ week }}</option>
                    </select>
                </div>
            </div>
            <form method="POST" @submit.prevent="submitPicks">
                <p class="mb-0 text-red-500 text-center" v-if="countPicks && league.league_type_id !== 2"><i
                        class="fa fa-exclamation-circle"></i> {{ errorMessage }} Week {{ currWeek }}!
                </p>
                <div class="mb-3 py-2 grid grid-cols-4 justify-items-center items-center content-evenly border-b border-gray-400">
                    <game-container v-for="(game, index) in thisWeek" :key="game.global_game_id" :league="league" :timezone="user.timezone" :game="game" :selected-team="getSelectedTeam(game)"></game-container>
                    <div v-if="league.league_type_id !== 2" id="tiebreak-wrap" class="col-span-4 text-center flex flex-col">
                        <label for="tiebreaker">Please enter total points for {{ lastGameOfWeek.away_team }}/{{
                            lastGameOfWeek.home_team }}</label>
                        <input type="text" id="tiebreaker" class="form-control" v-model="tiebreaker">
                    </div>
                </div>
                <input type="hidden" :value="league.id">
                <input type="hidden" :value="thisWeek">
                <div class="mb-5 text-center">
                    <p class="text-red-500" v-if="countPicks"><i class="fa fa-exclamation-circle"></i> {{ errorMessage }} Week {{ currWeek }}!</p>
                    <button v-if="!selectedTeamsStore.areGamesDisabled" type="submit" class="mt-3 px-2.5 py-1.5 rounded-md bg-blue-500 border border-blue-500 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 hover:shadow-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save Picks</button>
                    <button v-if="league.league_type_id === 2" type="button" class="ms-2 px-2.5 py-1.5 rounded-md bg-gray-50 border border-red-400 text-sm font-semibold text-red-400 shadow-sm hover:bg-red-500 hover:text-white hover:shadow-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" @click.prevent="resetSurvivorPick">Reset Form</button>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>

