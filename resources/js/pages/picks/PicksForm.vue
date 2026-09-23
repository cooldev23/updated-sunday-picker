<script setup>
import GameContainer from '@/components/GameContainer.vue';
import { useSelectedTeamsStore } from '@/stores/SelectedTeamsStore.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { CircleAlert } from '@lucide/vue';
import { ref, computed } from 'vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { create, edit, store, update } from '@/routes/picks';
import { dashboard } from '@/routes';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Dashboard',
        href: dashboard(),
      },
      {
        title: 'Make/Edit Picks',
        href: null,
      },
    ],
  },
});

// stores
const selectedTeamsStore = useSelectedTeamsStore();

// props
const props = defineProps({
  thisWeek: Array,
  currentWeek: Number,
  league: Object,
  lastGameOfWeek: Object,
  byes: Array,
  isEdit: {
    type: Boolean,
    default: false,
  },
  userPicks: {
    type: Array,
    default: []
  },
  otherWeeksWithPicks: {
    type: Array,
    default: []
  }
});

// helpers
const page = usePage();

// data
const tiebreaker = ref(0),
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

const userTimezone = computed(() => {
  return page.props.auth.user.timezone;
});

// named functions
function getSelectedTeam(game) {
  let selectedTeams = props.userPicks;
  for (const selected of selectedTeams) {
    if (selected.gameId === game.global_game_id) {
      // console.log(selected);
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
  const payload = {
    data: selectedTeamsStore.userSelectedTeams,
    tiebreaker: Number(tiebreaker.value),
  };

  if (props.isEdit) {
    router.patch(update({ league: props.league, week: selectedWeek.value }), payload);
  } else {
    router.post(store({ league: props.league, week: selectedWeek.value }), payload);
  }
}

function changeWeek() {
  if (props.otherWeeksWithPicks.includes(selectedWeek.value)) {
    router.get(edit({ league: props.league, week: selectedWeek.value }));
  } else {
    router.get(create({ league: props.league, week: selectedWeek.value }));
  }
}

function getTiebreakerValue() {
  if (selectedTeamsStore.userSelectedTeams.length && props.league.league_type_id !== 2) {
    let lastGameOfWeek = selectedTeamsStore.userSelectedTeams.filter((obj) => obj.tiebreaker !== null)[0];
    return lastGameOfWeek ? lastGameOfWeek.tiebreaker : 0;
  }
  return 0;
}

selectedTeamsStore.hydrateSelectedTeams(props.userPicks);
tiebreaker.value = getTiebreakerValue();
selectedTeamsStore.setWeekGamesCount(props.thisWeek.length);
</script>

<template>

  <Head :title="isEdit ? 'Edit Picks' : 'Make Picks'"></Head>
  <div class="mx-auto pb-4 pt-3 sm:px-6 lg:px-8 max-w-7xl">
    <div class="pb-1 flex flex-col items-center justify-between md:flex-row md:items-end border-b border-gray-400">
      <h2 class="text-xl">{{ league.name }}</h2>
      <div id="teamsOnBye" class="d-flex justify-content-center align-items-base flex-wrap">
        <h5 class="mb-2 md:mb-0">Byes: <span class="teams-on-bye">{{ displayByes }}</span></h5>
      </div>
      <div>
        <Select items={allWeeks} id="weeks" v-model="selectedWeek" @update:model-value="changeWeek()">
          <SelectTrigger class="w-full" :tabindex="1">
            <SelectValue :placeholder="'Week ' + currentWeek" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectLabel>Weeks</SelectLabel>
              <SelectItem v-for="week in allWeeks" :key="week" :value="week">
                Week {{ week }}
              </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>
    </div>
    <form method="POST" @submit.prevent="submitPicks">
      <div class="mb-3 grid grid-cols-1 auto-rows-[minmax(100px,auto)] md:grid-cols-2 xl:grid-cols-4 gap-3 border-b border-gray-400">
        <game-container v-for="(game, index) in thisWeek" :key="game.global_game_id" :league="league"
          :timezone="userTimezone" :game="game" :selected-team="getSelectedTeam(game)"></game-container>
        <div v-if="league.league_type_id !== 2" id="tiebreak-wrap" class="col-span-full">
          <div class="flex flex-col justify-center items-center">
            <label for="tiebreaker">Please enter total points for {{ lastGameOfWeek.away_team }}/{{ lastGameOfWeek.home_team }}</label>
            <input type="number" id="tiebreaker" min="0" max="120" class="mb-2 px-2 py-1 bg-slate-100 dark:bg-white rounded dark:text-slate-800" v-model="tiebreaker">
            <p class="mb-0 text-red-500 text-center flex justify-center items-center"
              v-if="countPicks && league.league_type_id !== 2">
              <CircleAlert class="me-1 w-4 h-4" />Some games not picked for Week {{ selectedWeek }}!
            </p>
          </div>
        </div>
      </div>
      <input type="hidden" :value="league.id">
      <input type="hidden" :value="thisWeek">
      <div class="mb-5 text-center">
        <button v-if="!selectedTeamsStore.areGamesDisabled" type="submit"
          class="mt-3 px-2.5 py-1.5 rounded-md bg-blue-500 border border-blue-500 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save
          Picks</button>
        <button v-if="league.league_type_id === 2" type="button"
          class="ms-2 px-2.5 py-1.5 rounded-md bg-gray-50 border border-red-400 text-sm font-semibold text-red-400 shadow-sm hover:bg-red-500 hover:text-white hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
          @click.prevent="resetSurvivorPick">Reset Form</button>
      </div>
    </form>
  </div>
</template>
