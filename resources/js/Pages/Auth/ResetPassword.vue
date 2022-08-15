<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />
    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>
        <JetValidationErrors class="mb-4" />
        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" :class="{'is-invalid': form.errors.email}" id="email" v-model="form.email" required autocomplete="email" autofocus>
                <div class="invalid-feedback">{{ form.errors.email }}</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" :class="{'is-invalid': form.errors.password}" id="password" v-model="form.password" required autocomplete="new-password">
                <div class="invalid-feedback">{{ form.errors.password }}</div>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" :class="{'is-invalid': form.errors.password_confirmation}" id="password_confirmation" v-model="form.password_confirmation" required autocomplete="new-password">
                <div class="invalid-feedback">{{ form.errors.password_confirmation }}</div>
            </div>
            <button type="submit" class="btn btn-dark w-100 my-3" :disabled="form.processing">
                Reset Password
            </button>
        </form>
    </JetAuthenticationCard>
</template>
