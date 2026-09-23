<script setup>
import { useSelectedTeamsStore } from '@/stores/SelectedTeamsStore.vue';

const emit = defineEmits(['updatePicked']);

const selectedTeamsStore = useSelectedTeamsStore();

// props
const props = defineProps({
    gameId: Number,
    team: Object,
    disabledForSurvivor: Boolean
});

function updatePicked(e) {
    emit('updatePicked', e);
}
</script>

<template>
    <div class="px-2 relative flex items-center" :class="{ 'bg-green-300': team.isPicked }">
        <div class="min-w-0 flex-1 leading-6">
            <label class="flex items-center" :for="team.team.team_key"><img class="me-2 w-8 h-8"
                    :src="team.team.logo_url" alt="">{{ team.team.team_key }}</label>
        </div>
        <div class="ml-3 flex h-6 items-center">
            <input type="radio" :id="team.team.team_key" :name="gameId" :value="team.team.team_key"
                :checked="team.isPicked" class="ms-auto form-check-input" @change="updatePicked($event)"
                :disabled="disabledForSurvivor || selectedTeamsStore.areGamesDisabled">
        </div>
    </div>
</template>