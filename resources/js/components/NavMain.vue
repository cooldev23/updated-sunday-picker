<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
  items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();

const page = usePage()

// Access the globally shared inertia props
// const unreadCount = computed(() => page.props.unread_notifications_count ?? 0)
const hasAdminRole = computed(() => page.props.auth.roles.hasOwnProperty('super_admin'))
// console.log(page.props.auth.user.roles);

const unreadCount = 3;
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in items" :key="item.title">
        <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
          <Link v-if="item.title === 'Notifications'" href="/notifications">
            <component :is="item.icon" />
            <span>Notifications</span>

            <!-- Only show badge if there are actual notifications -->
            <span v-if="unreadCount > 0"
              class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">
              {{ unreadCount }}
            </span>
          </Link>
          <p v-if="item.title === 'Admin' && hasAdminRole">Show if Super Admin</p>
          <Link v-else-if="item.title === 'Admin' && hasAdminRole" :href="item.href">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </Link>
          <Link v-else :href="item.href">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
