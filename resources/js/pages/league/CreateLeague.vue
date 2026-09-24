<script setup>
import { ref } from 'vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog"
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/league';
import { dashboard } from '@/routes';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Dashboard',
        href: dashboard(),
      },
      {
        title: 'Create League',
        href: null,
      },
    ],
  },
});

const props = defineProps({
  leagueTypes: Object,
});

const isSelectOpen = ref(false);
</script>

<template>

  <Head title="Create League"></Head>

  <div class="flex min-h-svh flex-col items-center gap-6 p-6 md:p-10">
    <Form v-bind="store()" v-slot="{ errors, processing }"
      class="p-4 w-full max-w-md border flex flex-col gap-6 rounded-md shadow-sm shadow-slate-800">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="leagueName">League Name</Label>
          <Input id="leagueName" type="text" name="leagueName" required autofocus :tabindex="1"
            autocomplete="leagueName" placeholder="League name" />
          <InputError :message="errors.leagueName" />
        </div>
        <div class="grid gap-2">
          <Label for="leagueMotto">Motto</Label>
          <Input id="leagueMotto" type="text" name="leagueMotto" autofocus :tabindex="1" autocomplete="leagueMotto"
            placeholder="League motto (optional)" />
          <InputError :message="errors.leagueMotto" />
        </div>
        <div class="grid gap-2">
          <Label class="mb-2" for="leagueType">League Type</Label>
          <Select items={leagueTypes} name="leagueTypeId" v-model:open="isSelectOpen">
            <SelectTrigger class="w-full" id="leagueType" :tabindex="1">
              <SelectValue placeholder="Choose League Type" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectLabel>League Types</SelectLabel>
                <SelectItem v-for="leagueType in leagueTypes" :key="leagueType.id" :value="leagueType.id">
                  {{ leagueType.name }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError :message="errors.leagueTypeId" />
          <div class="text-start">
            <Dialog>
              <DialogTrigger class="p-1 text-xs cursor border rounded-md bg-gray-800 cursor-pointer">League Type
                Descriptions</DialogTrigger>
              <DialogContent>
                <DialogHeader>
                  <DialogTitle>League Type Descriptions</DialogTitle>
                  <DialogDescription>
                    <div v-for="type in leagueTypes" :key="type.id">
                      <dl class="mb-2">
                        <dt>
                          <strong>{{ type.name }}</strong>
                        </dt>
                        <dd>{{ type.description }}</dd>
                      </dl>
                    </div>
                  </DialogDescription>
                </DialogHeader>
              </DialogContent>
            </Dialog>
          </div>
        </div>
        <div>
          <Label class="mb-2" for="addMembers">Add Members <span class="text-xs">(can be done later)</span></Label>
          <textarea rows="6" id="addMembers" name="addMembers" tabindex="1"
            class="p-2 block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-gray-400 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            placeholder="Enter email addresses separated by a comma"></textarea>
          <InputError :message="errors.addMembers" />
        </div>
        <hr class="mb-1">
        <div class="grid grid-cols-2 gap-x-2">
          <Button type="submit" class="w-full" :tabindex="4" :disabled="processing" data-test="login-button">
            <Spinner v-if="processing" />
            Create League
          </Button>
          <Link :href="dashboard()">
            <Button type="button" variant="outline" class="w-full" :tabindex="4">
              Cancel
            </Button>
          </Link>
        </div>
      </div>
    </Form>
  </div>
</template>
