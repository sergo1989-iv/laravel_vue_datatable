<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetCheckbox from '@/Components/Checkbox.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <JetValidationErrors class="mb-3" />

        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" :class="{'is-invalid': form.errors.name}" id="name" v-model="form.name" required autocomplete="name" autofocus>
                <div class="invalid-feedback">{{ form.errors.name }}</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" :class="{'is-invalid': form.errors.email}" id="email" v-model="form.email" required autocomplete="email">
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
            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="form-check mt-3">
                <input class="form-check-input" type="checkbox" v-model="form.terms" name="terms" id="terms">
                <label class="form-check-label" for="terms">
                    I agree to the <a target="_blank" :href="route('terms.show')">Terms of Service</a> and <a target="_blank" :href="route('policy.show')">Privacy Policy</a>
                </label>
            </div>
            <button type="submit" class="btn btn-dark w-100 my-3" :disabled="form.processing">
                Register
            </button>
            <div class="text-center">
                <Link :href="route('login')" class="text-sm">
                    Already registered?
                </Link>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
