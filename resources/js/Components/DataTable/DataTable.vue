<template>
    <div>
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <JetButton v-if="createTitle" @click="newItem">
                    {{ createTitle }}
                </JetButton>
                <slot name="toolbar-left"/>
            </div>
            <div class="table-toolbar-right">
                <JetInput v-model="query.search" type="search" class="h-9"/>
                <slot name="toolbar-right"/>
            </div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr v-if="columns.length > 0">
                    <th v-for="column in columns" :class="column.class">
                        <div v-if="column.sortable" @click="setSortBy(column.name)"
                             class="flex items-center justify-between cursor-pointer pl-1">
                            <fa v-if="query.sortBy === column.name"
                                :icon="query.sortDir === 'asc' ? 'arrow-up' : 'arrow-down'"
                            />
                            <div class="text-center flex-1">{{ column.name }}</div>
                        </div>
                    </th>
                    <th v-if="hasAction"></th>
                </tr>
                <tr v-if="columns.length > 0" class="bg-gray-300">
                    <td v-for="column in columns">
                        <JetInput
                            v-if="column.filterable"
                            v-model="query.filters[column.name]"
                            type="search" class="p-1 h-6 w-full"
                        />
                    </td>
                    <td v-if="hasAction"></td>
                </tr>
                </thead>
                <tbody v-if="items.total">
                <tr v-for="item in items.data">
                    <td v-for="column in columns">
                        {{ item[column.name] }}
                    </td>
                    <td v-if="hasAction">
                        <div class="flex items-center justify-around space-x-2">
                            <button v-if="hasShowing" @click="show(item.id)"
                                    class="text-gray-500 transition hover:text-gray-900">
                                <fa icon="eye"/>
                            </button>
                            <button v-if="hasEditable" @click="edit(item.id)"
                                    class="text-gray-500 transition hover:text-gray-900">
                                <fa icon="pen"/>
                            </button>
                            <button v-if="hasDeletable" @click="getDeleteDialog(item.id)"
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
            <div v-if="loading" class="loading">
                <div class="loading-backdrop"></div>
                <fa icon="cog" spin/>
            </div>
        </div>
        <div v-if="items.total" class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-700">
                Showing {{ items.from }} to {{ items.to }} of {{ items.total }} results
            </div>
            <nav v-if="items.last_page > 1" aria-label="Pagination" class="pagination">
                <template v-for="link in items.links">
                    <div
                        :class="{'active': link.active, 'disabled': !link.url}"
                        @click="setPage(link)"
                        v-html="link.label"
                    ></div>
                </template>
            </nav>
        </div>
        <JetDialogModal :show="formDialog" @close="close">
            <template #title>
                {{ id !== null ? editTitle : createTitle }}
            </template>
            <template #content>
                <JetValidationErrors class="mb-4"/>
                <slot name="form-content"/>
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
                <JetButton class="ml-3" @click="deleteItem">
                    Delete
                </JetButton>
            </template>
        </JetDialogModal>
    </div>
</template>

<script>
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetDialogModal from '@/Components/DialogModal.vue';
import JetSecondaryButton from '@/Components/SecondaryButton.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';
import {pickBy, throttle} from "lodash";
import {Inertia} from "@inertiajs/inertia";

export default {
    name: "DataTable",
    props: {
        hasShowing: {
            type: Boolean,
            default: false,
        },
        hasEditable: {
            type: Boolean,
            default: false,
        },
        hasDeletable: {
            type: Boolean,
            default: false,
        },
        routePrefix: String,
        createTitle: {
            type: String,
            default: 'Add new',
        },
        editTitle: {
            type: String,
            default: 'Edit',
        },
        sortBy: {
            type: String,
            default: 'id',
        },
        sortDir: {
            type: String,
            default: 'desc',
        },
        columns: {
            type: Array,
            default: [],
        },
        form: Object,
    },
    components: {JetButton, JetInput, JetDialogModal, JetSecondaryButton, JetValidationErrors},
    data() {
        return {
            loading: true,
            query: {
                page: 1,
                search: '',
                sortBy: this.sortBy,
                sortDir: this.sortDir,
                filters: {},
            },
            items: {},
            formDialog: false,
            deleteDialog: false,
            id: null,
        }
    },
    mounted() {
        this.getItems();
    },
    watch: {
        query: {
            deep: true,
            handler: throttle(function () {
                this.getItems()
            }, 150),
        },
    },
    computed: {
        hasAction() {
            return !!this.hasShowing || !!this.hasEditable || !!this.hasDeletable;
        }
    },
    methods: {
        getItems() {
            this.loading = true
            axios.get(route(`${this.routePrefix}.data`, pickBy(this.query)))
                .finally(() => this.loading = false)
                .then(response => {
                    this.items = response.data;
                    if (this.items.last_page < this.query.page) {
                        this.query.page = 1;
                    }
                });
        },
        setSortBy(name) {
            this.query.sortBy = name;
            this.query.sortDir = this.query.sortDir === 'asc' ? 'desc' : 'asc';
        },
        setPage(link) {
            if (!link.active && link.url) {
                let page = parseInt(new URL(link.url).searchParams.get('page'));
                if (page > 0 && page !== this.query.page) {
                    this.query.page = page;
                }
            }
        },
        close() {
            this.deleteDialog = false;
            this.formDialog = false;
            this.id = null;
            this.form.reset();
        },
        newItem() {
            this.formDialog = true;
        },
        show(id) {
            axios.get(route(`${this.routePrefix}.show`, [id])).then(response => {
                this.id = response.data.id
                this.form.name = response.data.name;
                this.form.email = response.data.email;
            })
        },
        edit(id) {
            axios.get(route(`${this.routePrefix}.show`, [id])).then(response => {
                this.id = response.data.id
                this.form.name = response.data.name;
                this.form.email = response.data.email;
                this.formDialog = true;
            })
        },
        submit() {
            if (this.id) {
                this.form.put(route(`${this.routePrefix}.update`, [this.id]), {
                    onSuccess: () => {
                        this.close()
                        this.getItems()
                    },
                })
            } else {
                this.form.post(route(`${this.routePrefix}.store`), {
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

<style scoped>
.table-toolbar {
    @apply mb-4 flex flex-col sm:flex-row items-center justify-between space-x-4
}

.table-toolbar-left, .table-toolbar-right {
    @apply flex items-center space-x-1
}

.table-wrap {
    @apply overflow-x-auto border bg-white shadow-lg rounded-lg relative
}

.table {
    @apply min-w-full
}

.table thead {
    @apply bg-gray-50 border-b border-gray-200
}

.table thead tr {
    @apply divide-x divide-gray-200
}

.table th {
    @apply px-1 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider
}

.table tbody {
    @apply bg-white
}

.table tbody tr {
    @apply divide-x divide-gray-200 text-gray-500 text-sm text-center whitespace-nowrap hover:bg-sky-200 cursor-pointer
}

.table tbody tr:nth-child(even) {
    @apply bg-gray-100 hover:bg-sky-200
}

.table td {
    @apply px-1 py-2
}

.loading {
    @apply absolute inset-0 text-4xl p-4 text-center text-gray-500
}

.loading-backdrop {
    @apply absolute bg-white inset-0 opacity-50
}

.pagination {
    @apply flex bg-white divide-x divide-gray-200 text-sm overflow-hidden rounded-md border border-gray-200 text-gray-500 shadow
}

.pagination > div {
    @apply px-3 py-1
}

.pagination > div:not(.disabled) {
    @apply hover:bg-sky-300 hover:text-white cursor-pointer
}

.pagination > div.disabled {
    @apply opacity-50
}

.pagination > div.active {
    @apply bg-sky-300 text-white
}
</style>
