<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';

// Destructure the appearance state and the update function
const { appearance, updateAppearance } = useAppearance();
</script>

<template>
  <div class="p-6 space-y-4">
    <p>Current theme preference: {{ appearance }}</p>

    <div class="flex space-x-2">
      <button 
        @click="updateAppearance('light')"
        class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-800"
      >
        Light
      </button>
      
      <button 
        @click="updateAppearance('dark')"
        class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-800"
      >
        Dark
      </button>
      
      <button 
        @click="updateAppearance('system')"
        class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-800"
      >
        System
      </button>
    </div>
  </div>
</template>
