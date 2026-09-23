<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { 
  Alert,
  AlertTitle,
  AlertDescription 
} from '@/components/ui/alert';
import type { BreadcrumbItem } from '@/types';
import { usePage } from "@inertiajs/vue3";
import { AlertCircleIcon, CheckCircle2Icon } from '@lucide/vue';

const page = usePage();

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" class="bg-background">
        <div v-if="page.flash?.error">
            <Alert variant="destructive">
              <AlertTitle>Error!</AlertTitle>
              <AlertDescription>{{ page.flash.error }}</AlertDescription>
            </Alert>
        </div>
        <div v-if="page.flash?.success">
            <Alert variant="success">
              <AlertTitle>Success!</AlertTitle>
              <AlertDescription>{{ page.flash.success }}</AlertDescription>
            </Alert>
        </div>
        <div v-if="page.flash?.message">
            <Alert variant="default">
                {{ page.flash.message }}
            </Alert>
        </div>
        <slot />
    </AppLayout>
</template>
