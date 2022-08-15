<script setup>
import {ref} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import {Head, Link} from '@inertiajs/inertia-vue3';
import JetApplicationMark from '@/Components/ApplicationMark.vue';
import JetBanner from '@/Components/Banner.vue';
import JetDropdown from '@/Components/Dropdown.vue';
import JetDropdownLink from '@/Components/DropdownLink.vue';
import JetNavLink from '@/Components/NavLink.vue';
import JetResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const menu = [
    {href: route('dashboard'), name: 'dashboard', label: 'Dashboard'},
    {href: route('users.index'), name: 'users.index', label: 'Users'},
];

const switchToTeam = (team) => {
    Inertia.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    Inertia.post(route('logout'));
};
</script>

<template>
    <div class="bg-light bg-gradient min-vh-100">
        <Head :title="title"/>
        <div class="bg-white shadow-sm">
            <div class="container">
                <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3">
                    <Link :href="route('dashboard')" class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-dark text-decoration-none">
                        <svg width="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.395 44.428C4.557 40.198 0 32.632 0 24 0 10.745 10.745 0 24 0a23.891 23.891 0 0113.997 4.502c-.2 17.907-11.097 33.245-26.602 39.926z"
                                fill="#6875F5"/>
                            <path
                                d="M14.134 45.885A23.914 23.914 0 0024 48c13.255 0 24-10.745 24-24 0-3.516-.756-6.856-2.115-9.866-4.659 15.143-16.608 27.092-31.75 31.751z"
                                fill="#6875F5"/>
                        </svg>
                    </Link>
                    <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                        <li v-for="item in menu">
                            <Link
                                :href="item.href"
                                class="nav-link px-2 link-secondary"
                                :class="route().current(item.name) ? 'link-secondary' : 'link-dark'"
                            >
                                {{ item.label }}
                            </Link>
                        </li>
                    </ul>
                    <div class="col-md-3 text-end">
                        <Link :href="route('profile.show')" class="btn btn-outline-secondary me-2">Profile</Link>
                        <form method="POST" @submit.prevent="logout" class="d-inline">
                            <button type="submit" class="btn btn-dark">Log Out</button>
                        </form>
                    </div>
                </header>
            </div>
        </div>
        <div class="container">
            <div v-if="$slots.header" class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <slot name="header"/>
                </h1>
            </div>
            <!-- Page Content -->
            <main>
                <JetBanner/>
                <slot/>
            </main>
        </div>
    </div>
</template>
