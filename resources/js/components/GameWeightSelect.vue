<script setup>
import { useSelectedTeamsStore } from '@/stores/SelectedTeamsStore.vue';
import { ref } from 'vue';

const emit = defineEmits(['updateWeight']);

const selectedTeamsStore = useSelectedTeamsStore();

// props
const props = defineProps({
  gameId: Number,
  hasWeightError: Boolean,
  selectedWeight: Number
});

const userSelectedWeight = ref(props.selectedWeight);

function updateWeight() {
  emit('updateWeight', userSelectedWeight.value);
}
</script>

<template>
  <label class="text-sm flex"
    :class="{ 'text-red-600': hasWeightError }, { 'text-green-400': userSelectedWeight !== 0 }"
    :for="gameId">Weight</label>
  <select :id="gameId"
    class="w-20 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6"
    v-model="userSelectedWeight" @change="updateWeight()"
    :class="{ 'ring-red-600 text-red-600': hasWeightError }, { 'ring-green-400': userSelectedWeight !== 0 }">
    <option v-for="(num, index) in selectedTeamsStore.weekGamesCount" :key="index" :value="num">{{ num }}</option>
  </select>
</template>