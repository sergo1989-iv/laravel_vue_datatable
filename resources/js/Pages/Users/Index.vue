<template>
    <AppLayout :title="pageTitle">
        <template #header>{{ pageTitle }}</template>
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
                    <div class="input-group mb-3">
                        <span class="input-group-text">Name</span>
                        <input type="text" class="form-control" :class="{'is-invalid': form.errors.name}" v-model="form.name" required autocomplete="name">
                        <div class="invalid-feedback">{{ form.errors.name }}</div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Email</span>
                        <input type="email" class="form-control" :class="{'is-invalid': form.errors.email}" v-model="form.email" required autocomplete="email">
                        <div class="invalid-feedback">{{ form.errors.email }}</div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Password</span>
                        <input type="password" class="form-control" :class="{'is-invalid': form.errors.password}" v-model="form.password" required autocomplete="new-password">
                        <div class="invalid-feedback">{{ form.errors.password }}</div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Confirm Password</span>
                        <input type="password" class="form-control" :class="{'is-invalid': form.errors.password_confirmation}" v-model="form.password_confirmation" required autocomplete="new-password">
                        <div class="invalid-feedback">{{ form.errors.password_confirmation }}</div>
                    </div>
                </template>
            </DataTable>
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
