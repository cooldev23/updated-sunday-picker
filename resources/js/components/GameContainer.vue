<script setup>
// import detailsModal from './GameDetailsModal.vue';
import { useSelectedTeamsStore } from '@/stores/SelectedTeamsStore.vue';
import TeamRadioButton from '@/components/TeamRadioButton.vue';
import GameWeightSelect from '@/components/GameWeightSelect.vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/solid';
import { ref, computed, onBeforeMount } from 'vue';

// stores
const selectedTeamsStore = useSelectedTeamsStore();

// props
const props = defineProps({
    game: Object,
    selectedTeam: Object,
    league: Object,
    userTimezone: String
});

// data
const away = ref({
    team: props.game.away_team,
    isPicked: props.selectedTeam.team === props.game.away_team.team_key
});
const home = ref({
    team: props.game.home_team,
    isPicked: props.selectedTeam.team === props.game.home_team.team_key
});
const isModalVisible = ref(false),
    isWeightSelected = ref(Boolean(props.selectedTeam.weight)),
    selectedWeight = ref(props.selectedTeam.weight);

// computed properties
const gameTime = computed(() => {
    return new Intl.DateTimeFormat('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour12: true, hour: 'numeric', minute: '2-digit', timeZone: props.userTimezone }).format(new Date(props.game.game_time));
});

const disabledForSurvivor = computed(() => {
    if (props.league.league_type_id === 2) {
        if (selectedTeamsStore.userSelectedTeams.length === 1) {
            return true;
        }
        return false;
    }
    return false;
});

const hasWeightError = computed(() => {
    if (props.league.league_type_id === 3) {
        if (away.value.isPicked || home.value.isPicked) {
            if (selectedWeight === 0) {
                return true;
            }
        }
        for (const pick of selectedTeamsStore.userSelectedTeams) {
            if (pick.gameId !== props.game.global_game_id) {
                if (pick.weight === selectedWeight.value && pick.weight !== 0) {
                    return true;
                }
            }
        }
    }
    return false;
});

const teamSelectedWeightZero = computed(() => {
    if (props.league.league_type_id === 3) {
        if (away.value.isPicked || home.value.isPicked) {
            if (selectedWeight.value === 0) {
                return true;
            }
        }
    }
    return false;
})

const resetForm = computed(() => {
    if (!selectedTeamsStore.userSelectedTeams.length) {
        away.value.isPicked = false;
        home.value.isPicked = false;
    }
});

// named functions
function showModal() {
    isModalVisible.value = true;
}
function closeModal() {
    isModalVisible.value = false;
}
function setSelectedAndWeight() {
    isWeightSelected.value = selectedWeight ? true : false;
    if (props.selectedTeam.team === props.game.away_team.team_key) {
        away.value.isPicked = true;
    }
    if (props.selectedTeam.team === props.game.home_team.team_key) {
        home.value.isPicked = true;
    }
}
function handlePicked(e) {
    if (e.target.value === props.game.away_team.team_key) {
        away.value.isPicked = !away.value.isPicked;
        home.value.isPicked = false;
    }
    if (e.target.value === props.game.home_team.team_key) {
        away.value.isPicked = false;
        home.value.isPicked = !home.value.isPicked;
    }
    selectedTeamsStore.updateSelectedTeams({ team: e.target.value, weight: selectedWeight.value, game: props.game.global_game_id });
}
function handleSelectedWeight(val) {
    isWeightSelected.value = true;
    selectedWeight.value = Number(val);
    if (away.value.isPicked) {
        selectedTeamsStore.updateSelectedTeams({ team: away.value.team, weight: selectedWeight.value, game: props.game.global_game_id });
    }
    if (home.value.isPicked) {
        selectedTeamsStore.updateSelectedTeams({ team: home.value.team, weight: selectedWeight.value, game: props.game.global_game_id });
    }
}

// hooks
onBeforeMount(() => {
    setSelectedAndWeight();
})
</script>

<template>
    <div class="m-2 p-2 game w-64 text-lg rounded shadow-sm border border-gray-400 bg-slate-400"
        :class="{ 'border-red-500': hasWeightError, 'border border-gray-100 shadow-none': disabledForSurvivor }"
        :reset-form="resetForm">
        <p class="game-date text-center mb-2 text-md"><small>{{ gameTime }}</small></p>
        <div class="mb-3 border border-gray-300 rounded-sm shadow-sm"
            :class="{ 'shadow-none bg-gray-300': disabledForSurvivor }">
            <TeamRadioButton :game-id="game.global_game_id" :team="away" :disabled-for-survivor="disabledForSurvivor"
                @update-picked="handlePicked">
            </TeamRadioButton>
            <TeamRadioButton :game-id="game.global_game_id" :team="home" :disabled-for-survivor="disabledForSurvivor"
                @update-picked="handlePicked">
            </TeamRadioButton>
        </div>
        <p class="mb-0 text-center"><small v-if="hasWeightError" class="text-red-500">
                <ExclamationTriangleIcon class="h-5 w-5 text-red-600 inline"></ExclamationTriangleIcon>Weight must be
                unique
            </small></p>
        <p class="mb-0 text-center"><small v-if="teamSelectedWeightZero" class="text-red-500">
                <ExclamationTriangleIcon class="h-5 w-5 text-red-600 inline"></ExclamationTriangleIcon>Weight should not
                be zero
            </small></p>
        <div v-if="league.league_type_id === 3" class="mb-3">
            <div class="px-5 flex flex-col justify-center items-center">
                <GameWeightSelect :game-id="game.global_game_id" :has-weight-error="hasWeightError"
                    :selected-weight="selectedTeam.weight" @update-weight="handleSelectedWeight">
                </GameWeightSelect>
            </div>
        </div>
        <div class="grid gap-2">
            <button type="button"
                class="px-2.5 py-1.5 rounded-md bg-gray-50 border border-blue-400 text-sm font-semibold text-blue-400 shadow-sm hover:bg-blue-500 hover:text-white hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                @click="showModal">Game Details</button>
        </div>
        <!-- <detailsModal :game="game" v-show="isModalVisible" @close="closeModal"></detailsModal> -->
    </div>
</template>
