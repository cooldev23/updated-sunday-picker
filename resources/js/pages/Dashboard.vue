<script setup>
import { dashboard } from '@/routes';
import { create as createPicks, edit as editPicks } from '@/routes/picks';
import { show, create } from '@/routes/league';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
  Card,
  CardTitle,
  CardHeader,
  CardContent,
  CardFooter
} from '@/components/ui/card';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Dashboard',
        href: dashboard(),
      },
    ],
  },
});

let props = defineProps({
  leagues: Array,
  user: Object
});

const page = usePage();
</script>

<template>

  <Head title="Dashboard" />

  <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <div v-if="leagues.length">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <Card class="hover:shadow-sm" v-for="league in leagues" :key="league.id">
          <CardHeader class="py-1 border-b">
            <CardTitle class="text-xl">
              <Link :href="show(league)"
                class="font-semibold text-blue-600 hover:text-gray-900 focus:outline-2 focus:rounded-sm focus:outline-red-500">
                {{ league.name }}
              </Link>
              <p v-if="league.motto" class="text-sm">{{ league.motto }}</p>
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p>Week {{ page.props.currentWeek }} {{ league.picks.length ? 'Picks' : '' }}</p>
            <div class="mb-3 flex justify-around flex-wrap">
              <span v-for="pick in league.picks" :key="pick.game_id"
                class="mx-2 mb-1 shadow-sm inline-flex justify-center items-center rounded-full bg-gray-50 px-2 py-1 text-lg w-16 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{
                  pick.winner }}</span>
            </div>
          </CardContent>
          <CardFooter v-if="!league.picks.length">
            <Link :href="createPicks({ league: league, week: page.props.currentWeek })"
              class="rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
              Make Picks</Link>
          </CardFooter>
          <CardFooter v-else>
            <Link :href="editPicks({ league: league, week: page.props.currentWeek })"
              class="rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
              Edit Picks</Link>
          </CardFooter>
        </Card>
      </div>
    </div>
    <div v-else>
      <Card class="hover:shadow-sm">
        <CardHeader class="border-b">
          <CardTitle>No Leagues</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-sm text-muted-foreground">
            <!-- TODO: replace this with year from current_week table -->
            Looks like you don't belong to any leagues yet! Let's fix that!
          </p>
        </CardContent>
        <CardFooter>
          <Link :href="create()"
            class="rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Create League</Link>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
