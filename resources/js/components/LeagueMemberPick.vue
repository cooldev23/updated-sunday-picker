<script setup>
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    game: Object,
    userPicks: Object
});
// console.log(props.userPicks);
</script>

<template>
    <p
        class="mx-1 w-11 h-16 shadow-sm flex flex-col justify-center items-center rounded-sm"
        :class="
            ({ 'border border-red-400 text-red-400': !pick.correct },
            { 'border border-green-600 text-green-600': pick.correct })
        "
    >
        <CheckIcon
            v-if="pick.correct"
            class="h-6 w-6 text-green-600"
        ></CheckIcon>
        <XMarkIcon v-else class="h-6 w-6 text-red-600"></XMarkIcon>
        {{ pick.winner }}
    </p>
</template>
