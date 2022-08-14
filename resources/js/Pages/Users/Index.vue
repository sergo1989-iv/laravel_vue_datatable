<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle }}</h2>
        </template>
        <div class="p-4">
            <DataTable
                :create-title="createFormTitle"
                :edit-title="editFormTitle"
                :columns="[
                    {name: 'id', sortable: true,filterable: true, class: 'w-20'},
                    {name: 'name', sortable: true,filterable: true},
                    {name: 'email', sortable: true,filterable: true},
                ]"
                :route-prefix="routePrefix"
                :has-editable="true"
                :has-deletable="true"
                :form="form"
            >
                <template #form-content>
                    <div>
                        <JetLabel for="name" value="Name"/>
                        <JetInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <JetInputError :message="form.errors.name" class="mt-2"/>
                    </div>
                    <div class="mt-4">
                        <JetLabel for="email" value="Email"/>
                        <JetInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <JetInputError :message="form.errors.email" class="mt-2"/>
                    </div>
                    <div class="mt-4">
                        <JetLabel for="password" value="Password"/>
                        <JetInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <JetInputError :message="form.errors.password" class="mt-2"/>
                    </div>
                    <div class="mt-4">
                        <JetLabel for="password_confirmation" value="Confirm Password"/>
                        <JetInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <JetInputError :message="form.errors.password_confirmation" class="mt-2"/>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from '@/Components/Button.vue';
import JetSecondaryButton from '@/Components/SecondaryButton.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/Label.vue';
import JetDialogModal from '@/Components/DialogModal.vue';
import {useForm} from "@inertiajs/inertia-vue3";
import DataTable from "@/Components/DataTable/DataTable.vue";

export default {
    components: {
        AppLayout,
        JetButton,
        JetSecondaryButton,
        JetInput,
        JetInputError,
        JetLabel,
        JetDialogModal,
        DataTable
    },
    data() {
        return {
            routePrefix: 'users',
            pageTitle: 'Users',
            createFormTitle: 'New user',
            editFormTitle: 'Edit user',
            form: useForm({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            })
        }
    },
}
</script>
