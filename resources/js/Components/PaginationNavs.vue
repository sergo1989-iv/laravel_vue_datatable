<template>
    <div class="py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
            Showing {{ from }} to {{ to }} of {{ total }} results
        </div>
        <nav aria-label="Pagination" class="flex bg-white divide-x divide-gray-200 text-sm overflow-hidden rounded-md border border-gray-200 shadow">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" :class="getClasses(link, key)" v-html="link.label"></div>
                <Link
                    v-else
                    :class="getClasses(link, key)"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </nav>
    </div>
</template>

<script>
import {Link} from '@inertiajs/inertia-vue3';

export default {
    name: "PaginationNavs",
    components: {Link},
    props: {
        links: Array,
        from: Number,
        to: Number,
        total: Number,
    },
    data() {
        return {
            class: 'py-2'
        }
    },
    methods: {
        getClasses(link, index) {
            return [
                this.class,
                (index === 0 || index === this.links.length - 1) ? 'px-2' : 'px-4',
                index === 0 ? 'rounded-l-md' : '',
                index === this.links.length - 1 ? 'rounded-r-md' : '',
                link.active ? 'text-white bg-sky-300 hover:bg-sky-700' : '',
            ];
        }
    },
}
</script>

<style scoped>

</style>
