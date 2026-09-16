<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import Card from '@/components/ui/card/Card.vue';
import { UserGroupIcon, CalendarDaysIcon } from '@heroicons/vue/24/solid';

const message = ref('');
const currentYear = new Date().getFullYear();

function submit() {
  axios.post('create-schedule')
    .then(response => {
      message.value = response.data.success
    })
}
</script>

<template>

  <Head title="Get Data"></Head>

  <div class="p-3">
    <h1 class="mb-2 pb-1 text-xl border-b">Get NFL Data</h1>
    <div class="grid grid-cols-4 gap-x-4">
      <Link :href="route('admin.nflSchedule.store')">
        <Card class="p-3 flex flex-col items-center hover:shadow-sm">
          <CardHeader>
            <CardTitle>Get Schedule</CardTitle>
          </CardHeader>
          <CalendarDaysIcon class="w-12 h-12"/>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              Get {{ currentYear }} NFL games
            </p>
          </CardContent>
        </Card>
      </Link>
      <Link :href="route('admin.nflTeams.store')">
        <Card class="p-3 flex flex-col items-center hover:shadow-sm">
          <CardHeader>
            <CardTitle>Get Teams</CardTitle>
          </CardHeader>
          <UserGroupIcon class="w-12 h-12"/>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              Get NFL teams
            </p>
          </CardContent>
        </Card>
      </Link>
      <!-- TODO: Set Weeks in current weeks table -->
      <!-- Anything else? -->
    </div>
  </div>
</template>