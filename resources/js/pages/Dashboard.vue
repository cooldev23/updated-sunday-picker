<script setup>
import { dashboard } from '@/routes';
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

  <div class="p-3">
    <!-- <h1 class="mb-2 pb-1 text-xl border-b">Get NFL Data</h1> -->
    <div class="grid grid-cols-1 gap-y-4 gap-x-4 md:grid-cols-4 md:gap-y-0">
      <div v-if="leagues.length">
        <div class="mb-3" v-for="league in leagues" :key="league.id">
          <div class="pb-3 flex justify-evenly">
            <Card class="hover:shadow-sm">
              <CardHeader>
                <CardTitle>
                  <Link :href="show(league)" class="font-semibold text-blue-600 hover:text-gray-900 focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    {{ league.name }}
                  </Link>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  <!-- TODO: replace this with year from current_week table -->
                  Get 2026 NFL games
                </p>
              </CardContent>
            </Card>
          </div>
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
              Looks like you don't belong to any leagues yet!  Let's fix that!
            </p>
          </CardContent>
          <CardFooter>
            <Link :href="create()" class="rounded-md bg-blue-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-md hover:bg-blue-500 hover:shadow-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Create League</Link>
          </CardFooter>
        </Card>
      </div>
    </div>
  </div>
</template>
