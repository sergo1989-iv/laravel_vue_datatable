<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetCheckbox from '@/Components/Checkbox.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <JetValidationErrors class="mb-3" />

        <div v-if="status" class="mb-3 text-sm text-success">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" :class="{'is-invalid': form.errors.email}" id="email" v-model="form.email" required autocomplete="email" autofocus>
                <div class="invalid-feedback">{{ form.errors.email }}</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" :class="{'is-invalid': form.errors.password}" id="password" v-model="form.password" required autocomplete="current-password">
                <div class="invalid-feedback">{{ form.errors.password }}</div>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" v-model="form.remember" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-dark w-100 my-3" :disabled="form.processing">
                Log in
            </button>
            <div class="text-center">
                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm">
                    Forgot your password?
                </Link>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
