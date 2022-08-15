<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';

const form = useForm({
    password: '',
});

const passwordInput = ref(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();

            passwordInput.value.focus();
        },
    });
};
</script>

<template>
    <Head title="Secure Area" />
    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>
        <p>This is a secure area of the application. Please confirm your password before continuing.</p>
        <JetValidationErrors class="mb-4" />
        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" :class="{'is-invalid': form.errors.password}" id="password" v-model="form.password" required autocomplete="current-password" autofocus>
                <div class="invalid-feedback">{{ form.errors.password }}</div>
            </div>
            <button type="submit" class="btn btn-dark w-100 my-3" :disabled="form.processing">
                Confirm
            </button>
        </form>
    </JetAuthenticationCard>
</template>
