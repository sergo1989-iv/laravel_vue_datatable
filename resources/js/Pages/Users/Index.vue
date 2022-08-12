<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle }}</h2>
        </template>
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <JetButton type="button" @click="newItem">{{ createButtonTitle }}</JetButton>
                <JetInput v-model="filters.search" type="search" class="h-9"/>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col" class="w-20">
                            <div @click="sortBy('id')" class="flex items-center justify-between cursor-pointer">
                                <fa v-if="sort.name === 'id'" :icon="sort.dir === 'asc' ? 'arrow-up' : 'arrow-down'"/>
                                <div class="text-center flex-1">ID</div>
                            </div>
                        </th>
                        <th scope="col">
                            <div @click="sortBy('name')" class="flex items-center justify-between cursor-pointer">
                                <fa v-if="sort.name === 'name'" :icon="sort.dir === 'asc' ? 'arrow-up' : 'arrow-down'"/>
                                <div class="text-center flex-1">Name</div>
                            </div>
                        </th>
                        <th scope="col">
                            <div @click="sortBy('email')" class="flex items-center justify-between cursor-pointer">
                                <fa v-if="sort.name === 'email'" :icon="sort.dir === 'asc' ? 'arrow-up' : 'arrow-down'"/>
                                <div class="text-center flex-1">Email</div>
                            </div>
                        </th>
                        <th scope="col" class="w-20">Actions</th>
                    </tr>
                    <tr class="bg-gray-300">
                        <td>
                            <JetInput v-model="filters.id" type="search" class="p-1 h-6 w-full"/>
                        </td>
                        <td>
                            <JetInput v-model="filters.name" type="search" class="p-1 h-6 w-full"/>
                        </td>
                        <td>
                            <JetInput v-model="filters.email" type="search" class="p-1 h-6 w-full"/>
                        </td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody v-if="items.total">
                    <tr v-for="item in items.data">
                        <td>{{ item.id }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.email }}</td>
                        <td>
                            <div class="flex items-center justify-around space-x-2">
                                <button @click="edit(item.id)"
                                        class="text-gray-500 transition hover:text-gray-900">
                                    <fa icon="pen"/>
                                </button>
                                <button @click="getDeleteDialog(item.id)"
                                        class="text-red-500 transition hover:text-red-900">
                                    <fa icon="trash"/>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                    <tr>
                        <td colspan="5" class="p-4 font-bold text-center">
                            No results found.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <JetDialogModal :show="formDialog" @close="close">
            <template #title>
                {{ id !== null ? editFormTitle : createFormTitle }}
            </template>
            <template #content>
                <JetValidationErrors class="mb-4"/>
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
            <template #footer>
                <JetSecondaryButton @click="close">
                    Cancel
                </JetSecondaryButton>
                <JetButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Save
                </JetButton>
            </template>
        </JetDialogModal>
        <JetDialogModal :show="deleteDialog" @close="close">
            <template #title>Delete</template>
            <template #content>Are you sure you want to delete?</template>
            <template #footer>
                <JetSecondaryButton @click="close">
                    Cancel
                </JetSecondaryButton>
                <JetButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteItem"
                >
                    Delete
                </JetButton>
            </template>
        </JetDialogModal>
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
import JetValidationErrors from '@/Components/ValidationErrors.vue';
import {useForm} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {pickBy, throttle} from "lodash";

export default {
    components: {
        AppLayout,
        JetButton,
        JetSecondaryButton,
        JetInput,
        JetInputError,
        JetLabel,
        JetDialogModal,
        JetValidationErrors
    },
    data() {
        return {
            routePrefix: 'users',
            pageTitle: 'Users',
            createButtonTitle: 'New user',
            createFormTitle: 'New user',
            editFormTitle: 'Edit user',
            items: {},
            deleteDialog: false,
            formDialog: false,
            id: null,
            form: useForm({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            }),
            sort: {
                name: 'id',
                dir: 'desc',
            },
            filters: {
                search: '',
                id: '',
                name: '',
                email: '',
            }
        }
    },
    mounted() {
        this.getItems()
    },
    watch: {
        filters: {
            deep: true,
            handler: throttle(function () {
                this.getItems()
            }, 150),
        },
    },
    methods: {
        getItems() {
            axios.get(route(`${this.routePrefix}.data`, pickBy({filters: this.filters, sort: this.sort})))
                .then(response => this.items = response.data)
        },
        sortBy(name) {
            this.sort.name = name;
            this.sort.dir = this.sort.dir === 'asc' ? 'desc' : 'asc';
            this.getItems()
        },
        newItem() {
            this.formDialog = true;
        },
        edit(id) {
            axios.get(route(`${this.routePrefix}.show`, [id])).then(response => {
                this.id = response.data.id
                this.form.name = response.data.name;
                this.form.email = response.data.email;
                this.formDialog = true;
            })
        },
        close() {
            this.deleteDialog = false;
            this.formDialog = false;
            this.id = null;
            this.form.reset();
        },
        submit() {
            if (this.id) {
                this.form.put(route(`${this.routePrefix}.update`, [this.id]), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                    onSuccess: () => {
                        this.close()
                        this.getItems()
                    },
                })
            } else {
                this.form.post(route(`${this.routePrefix}.store`), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                    onSuccess: () => {
                        this.close()
                        this.getItems()
                    },
                })
            }
        },
        getDeleteDialog(id) {
            this.id = id;
            this.deleteDialog = true;
        },
        deleteItem() {
            Inertia.delete(route(`${this.routePrefix}.destroy`, [this.id]), {
                onSuccess: () => {
                    this.close()
                    this.getItems()
                },
            })
        }
    },
}
</script>
