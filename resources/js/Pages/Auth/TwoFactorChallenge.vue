<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head title="Two-factor Confirmation" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>
        <p>
            <template v-if="! recovery">
                Please confirm access to your account by entering the authentication code provided by your authenticator application.
            </template>
            <template v-else>
                Please confirm access to your account by entering one of your emergency recovery codes.
            </template>
        </p>
        <JetValidationErrors class="mb-4" />
        <form @submit.prevent="submit">
            <div v-if="! recovery" class="mb-3">
                <label for="code" class="form-label">Code</label>
                <input ref="codeInput" inputmode="numeric" type="text" class="form-control" :class="{'is-invalid': form.errors.code}" id="code" v-model="form.code" autocomplete="one-time-code">
                <div class="invalid-feedback">{{ form.errors.code }}</div>
            </div>
            <div v-if="! recovery" class="mb-3">
                <label for="recovery_code" class="form-label">Recovery Code</label>
                <input ref="recoveryCodeInput" type="text" class="form-control" :class="{'is-invalid': form.errors.recovery_code}" id="recovery_code" v-model="form.recovery_code" autocomplete="one-time-code">
                <div class="invalid-feedback">{{ form.errors.recovery_code }}</div>
            </div>
            <div class="d-flex align-items-center justify-content-end mt-3">
                <button type="button" class="btn btn-link" @click.prevent="toggleRecovery">
                    <template v-if="! recovery">
                        Use a recovery code
                    </template>
                    <template v-else>
                        Use an authentication code
                    </template>
                </button>
                <button type="submit" class="btn btn-dark ms-3" :disabled="form.processing">
                    Log in
                </button>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
