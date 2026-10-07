<script setup>
import { Head, router, usePage, Link } from '@inertiajs/vue3';

const props = defineProps({
  userNotifications: Object,
  clanMemberInvites: Object
})

function markRead(notification) {
  router.patch(route('profile.notifications.markRead'), {
    notificationId: notification.id
  });
}

function addMemberToClan(clan) {
  router.post(route('clans.addMember', [clan.id, usePage().props.auth.user.id]));
}
</script>

<template>
  <Head title="Member Notifications" />

  <div class="py-12">
    <div class="px-3 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <h3 class="mb-3 text-2xl border-b-2 border-gray-200">Notifications</h3>
        <div v-if="userNotifications.length" class="mx-auto mb-3 max-w-4xl sm:px-6 lg:px-8 space-y-6">
          <div v-for="notification in userNotifications" :key="notification.id" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="ml-3 pt-0.5">
              <p class="text-sm font-medium text-gray-900">{{ notification.data.clan.name }}</p>
              <p class="mt-1 text-sm text-gray-500">{{ notification.data.message ? notification.data.message : notification.data.description }}</p>
              <div class="flex justify-start mt-4">
                <!-- <SmallPrimaryOutlineButton class="me-2"><Link :href="route('clans.show', notification.data.clan)">View Clan</Link></SmallPrimaryOutlineButton> -->
                <button type="button"
                  class="rounded-md bg-white text-sm font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  @click="markRead(notification)">Mark as Read</button>
              </div>
            </div>
            <div class="ml-4 flex flex-shrink-0">
              <button type="button" @click="show = false"
                class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="sr-only">Close</span>
              </button>
            </div>
          </div>
        </div>
        <div v-else class="mx-auto mb-3 sm:px-6 lg:px-8 space-y-6 max-w-4xl">
          <p class="p-4 sm:p-8 bg-white rounded shadow">No unread notifications found.</p>
        </div>
        <h3 class="mb-3 text-2xl border-b-2 border-gray-200">Clan Invites</h3>
        <div v-if="clanMemberInvites.length" class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
          <div v-for="invite in clanMemberInvites" :key="invite.id" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="ml-3 pt-0.5">
              <p class="mb-3 text-md text-gray-600">You have been invited to join the <span class="font-medium text-gray-900">{{ invite.clan.name }} Poker Clan</span>!
              </p>
              <h6 class="font-medium">Current Clan Members</h6>
              <ul class="border-t-2 border-gray-200">
                <li v-for="member in invite.clan.members" :key="member.id">
                  <p class="text-sm">{{ member.name }}</p>
                </li>
              </ul>
              <div class="mt-4">
                <button type="button"
                  class="rounded-md bg-white text-sm font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  @click="addMemberToClan(invite.clan)">Join Clan!</button>
              </div>
            </div>
            <div class="ml-4 flex flex-shrink-0">
              <button type="button" @click="show = false"
                class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="sr-only">Close</span>
              </button>
            </div>
          </div>
        </div>
        <div v-else class="sm:px-6 lg:px-8 space-y-6 max-w-4xl mx-auto">
          <p class="p-4 sm:p-8 bg-white rounded shadow">No clan invites found.</p>
        </div>
    </div>
  </div>
</template>