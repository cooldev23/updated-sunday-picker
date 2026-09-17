<script setup>
import { Link, Head } from '@inertiajs/vue3';
import { getNflData, adminDashboard } from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Admin Dashboard',
                href: adminDashboard(),
            }
        ],
    },
});
</script>

<template>
  This is the admin page
  <Link :href="getNflData()">
    getNflData
  </Link>
</template>