<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { CalendarDays, UsersRoundIcon } from '@lucide/vue';
import { 
  Card,
  CardTitle,
  CardHeader,
  CardContent,
} from '@/components/ui/card';
import { store } from '@/routes/admin/nflSchedule';
import { storeTeams } from '@/routes/admin/nflTeams';
import { getNflData, adminDashboard } from '@/routes/admin';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Admin Dashboard',
                href: adminDashboard(),
            },
            {
                title: 'Get NFL Data',
                href: getNflData(),
            },
        ],
    },
});

const page = usePage();
</script>

<template>

  <Head title="Get Data"></Head>

  <div class="p-3">
    <!-- <h1 class="mb-2 pb-1 text-xl border-b">Get NFL Data</h1> -->
    <div v-if="page.flash.toast" class="toast">
      {{ page.flash.toast.message }}
    </div>
    <div class="grid grid-cols-1 gap-y-4 gap-x-4 md:grid-cols-4 md:gap-y-0">
      <Link :href="store()">
        <Card class="text-center hover:shadow-sm">
          <CardHeader>
            <CardTitle>Get Schedule</CardTitle>
          </CardHeader>
          <div class="flex justify-center">
            <CalendarDays class="w-12 h-12"/>
          </div>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              <!-- TODO: replace this with year from current_week table -->
              Get 2026 NFL games
            </p>
          </CardContent>
        </Card>
      </Link>
      <Link :href="storeTeams()">
        <Card class="text-center hover:shadow-sm">
          <CardHeader>
            <CardTitle>Get Teams</CardTitle>
          </CardHeader>
          <div class="flex justify-center">
            <UsersRoundIcon class="w-12 h-12"/>
          </div>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              Get NFL teams
            </p>
          </CardContent>
        </Card>
      </Link>
    </div>
  </div>
</template>