<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

// props
const props = defineProps({
    leagueTypes: Object,
});

// form
const form = useForm({
    leagueName: "",
    leagueMotto: "",
    leagueTypeId: "",
    newMembers: ""
});

const submit = () => {
    form.post(route("league.store"));
};
</script>

<template>
    <Head title="Create League"></Head>
    <AuthLayout>
        <div class="py-6">
            <h1 class="text-3xl text-center">Create Your League!</h1>
            <div
                class="p-3 max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-2 gap-2"
            >
                <div
                    class="w-full mx-auto sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
                >
                    <form @submit.prevent="submit">
                        <div
                            class="mx-auto pb-4 pt-3 sm:px-6 lg:px-8 max-w-7xl"
                        >
                            <fieldset>
                                <legend class="mb-2 border-b border-gray-300 text-xl w-full">League Details</legend>
                                <div class="mb-2">
                                    <InputLabel for="leagueName" value="League Name" />
                                    <TextInput
                                        id="leagueName"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model.trim="form.leagueName"
                                        required
                                        autofocus
                                        autocomplete="leagueName"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.leagueName"
                                    />
                                </div>
                                <div class="mb-2">
                                    <InputLabel
                                        for="leagueMotto"
                                        value="League Motto"
                                    />
                                    <TextInput
                                        id="leagueMotto"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model.trim="form.leagueMotto"
                                        required
                                        autofocus
                                        autocomplete="leagueMotto"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.leagueMotto"
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        for="leagueType"
                                        value="League Type"
                                    />
                                    <select
                                        id="leagueType"
                                        class="mt-2 block w-full rounded-md border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        v-model="form.leagueTypeId"
                                    >
                                        <option disabled value="">Select League Type...</option>
                                        <option
                                            v-for="type in leagueTypes"
                                            :key="type.id"
                                            :value="type.id"
                                        >
                                            {{ type.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.leagueTypeId"
                                    />
                                </div>
                            </fieldset>

                            <fieldset class="mt-3">
                                <legend class="mb-2 border-b border-gray-300 w-full text-xl">Invite League Members</legend>
                                <div>
                                    <InputLabel
                                        for="addMembers"
                                        value="Add members (can be done later)"
                                    />
                                    <textarea rows="6" id="addMembers" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Enter email addresses separated by a comma" v-model.trim="form.newMembers"></textarea>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.newMembers"
                                    />
                                </div>
                            </fieldset>
                            <hr class="h-px my-3 bg-gray-300 border-0">
                            <PrimaryButton> Create League </PrimaryButton>
                        </div>
                    </form>
                </div>
                <div
                    class="mx-auto w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg self-start"
                >
                    <h4 class="text-xl">League Types</h4>
                    <div v-for="type in leagueTypes" :key="type.id">
                        <dl class="mb-2">
                            <dt>
                                <strong>{{ type.name }}</strong>
                            </dt>
                            <dd>{{ type.description }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
