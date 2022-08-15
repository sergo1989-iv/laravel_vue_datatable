<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>
        <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
        <div v-if="status" class="mb-3 text-sm text-success">{{ status }}</div>
        <JetValidationErrors class="mb-4" />
        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" :class="{'is-invalid': form.errors.email}" id="email" v-model="form.email" required autocomplete="email" autofocus>
                <div class="invalid-feedback">{{ form.errors.email }}</div>
            </div>
            <button type="submit" class="btn btn-dark w-100 my-3" :disabled="form.processing">
                Email Password Reset Link
            </button>
        </form>
    </JetAuthenticationCard>
</template>
