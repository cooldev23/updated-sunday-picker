<script setup>
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Between Seasons',
                href: null,
            },
        ],
    },
});
</script>

<template>
  <!-- TODO: replace year with year from current week table -->
  <h1 class="text-2xl">The 2026 NFL season has not started yet.</h1>
   <p>Season starts on: </p> <!-- TODO: how to get this -->
</template>