<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />
    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>
        <p>Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
        <p v-if="verificationLinkSent" class="text-success">A new verification link has been sent to the email address you provided in your profile settings.</p>
        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <button type="submit" class="btn btn-dark w-100 mb-3" :disabled="form.processing">
                    Resend Verification Email
                </button>
                <div>
                    <Link :href="route('profile.show')">Edit Profile</Link>
                    <Link :href="route('logout')" method="post" as="button" class="btn btn-link ms-2">Log Out</Link>
                </div>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
