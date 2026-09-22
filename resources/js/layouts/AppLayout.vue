<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import type { BreadcrumbItem } from '@/types';
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" class="bg-background">
        <div v-if="page.flash?.error">
            <Alert variant="destructive">
                {{ page.flash.error }}
            </Alert>
        </div>
        <div v-if="page.flash?.success">
            <Alert variant="default">
                {{ page.flash.success }}
            </Alert>
        </div>
        <slot />
    </AppLayout>
</template>
