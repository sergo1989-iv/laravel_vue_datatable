<template>
    <div>
        <div class="btn-toolbar justify-content-between mb-3" role="toolbar">
            <button v-if="createTitle" @click="newItem" type="button" class="btn btn-dark bt-sm">
                {{ createTitle }}
            </button>
            <div class="btn-group btn-group-sm me-2" role="group">
                <slot name="toolbar-left"/>
            </div>
            <div class="input-group input-group-sm">
                <span class="input-group-text" id="basic-addon1">
                    <fa icon="fa-solid fa-magnifying-glass" />
                </span>
                <input v-model="query.search" type="search" class="form-control"/>
            </div>
        </div>
        <div class="table-responsive bg-white shadow-sm rounded position-relative">
            <table class="table table-striped table-hover table-sm mb-0">
                <thead>
                <tr v-if="columns.length > 0">
                    <th v-for="column in columns" :class="column.class" class="text-center text-uppercase" scope="col">
                        <div v-if="column.sortable" @click="setSortBy(column.name)" class="d-flex align-items-center" role="button">
                            <fa v-if="query.sortBy === column.name" :icon="query.sortDir === 'asc' ? 'arrow-up' : 'arrow-down'"/>
                            <div class="flex-grow-1">{{ column.name }}</div>
                        </div>
                    </th>
                    <th v-if="hasAction" width="80"></th>
                </tr>
                <tr v-if="columns.length > 0" class="bg-light">
                    <td v-for="column in columns">
                        <input v-if="column.filterable"
                               v-model="query.filters[column.name]"
                               type="search"
                               class="form-control form-control-sm"
                        />
                    </td>
                    <td v-if="hasAction"></td>
                </tr>
                </thead>
                <tbody v-if="items.total">
                <tr v-for="item in items.data">
                    <td v-for="column in columns" class="text-center align-middle">
                        {{ item[column.name] }}
                    </td>
                    <td v-if="hasAction" class="align-middle">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                            <button v-if="hasShowing" @click="show(item.id)"
                                    class="btn btn-primary">
                                <fa icon="eye"/>
                            </button>
                            <button v-if="hasEditable" @click="edit(item.id)"
                                    class="btn btn-primary">
                                <fa icon="pen"/>
                            </button>
                            <button v-if="hasDeletable" @click="getDeleteDialog(item.id)"
                                    class="btn btn-danger">
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
            <div v-if="loading" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white opacity-50">
                <div class="loading-backdrop"></div>
                <fa icon="cog" spin size="2x"/>
            </div>
        </div>
        <div v-if="items.total" class="mt-3 row justify-content-between">
            <div class="col-md-auto">
                Showing {{ items.from }} to {{ items.to }} of {{ items.total }} results
            </div>
            <div class="col-md-auto" v-if="items.last_page > 1">
                <nav aria-label="...">
                    <ul class="pagination pagination-sm">
                        <li v-for="link in items.links" class="page-item" :class="{'active': link.active}" aria-current="page">
                            <a v-if="link.url" class="page-link" href="#" @click.prevent="setPage(link)" v-html="link.label"></a>
                            <span v-else class="page-link" v-html="link.label"></span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="modal fade" ref="formDialog" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ id !== null ? editTitle : createTitle }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <JetValidationErrors class="mb-4"/>
                        <slot name="form-content"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" :disabled="form.processing" @click="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="deleteDialog" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" :disabled="form.processing" @click="deleteItem">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetValidationErrors from '@/Components/ValidationErrors.vue';
import {pickBy, throttle} from "lodash";
import {Inertia} from "@inertiajs/inertia";
import { Modal } from "bootstrap";


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
    components: {JetValidationErrors},
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
            formDialog: null,
            deleteDialog: null,
            id: null,
        }
    },
    mounted() {
        this.getItems();
        this.formDialog = new Modal(this.$refs.formDialog);
        this.deleteDialog = new Modal(this.$refs.deleteDialog);
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
            this.deleteDialog.hide();
            this.formDialog.hide();
            this.id = null;
            this.form.reset();
        },
        newItem() {
            this.formDialog.show();
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
                this.formDialog.show();
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
            this.deleteDialog.show();
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
