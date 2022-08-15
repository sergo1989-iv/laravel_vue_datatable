<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

const show = ref(true);
const style = computed(() => usePage().props.value.jetstream.flash?.bannerStyle || 'success');
const message = computed(() => usePage().props.value.jetstream.flash?.banner || '');

watch(message, async () => {
  show.value = true;
});
</script>

<template>
    <div>
        <div v-if="show && message" class="alert alert-success d-flex align-items-center alert-dismissible fade show" :class="style == 'success' ? 'alert-success' : 'alert-danger'" role="alert">
            <fa v-if="style == 'success'" icon="check-circle" class="text-success me-2 flex-shrink-0"/>
            <fa v-else icon="times-circle" class="text-danger me-2 flex-shrink-0"/>
            <div>{{ message }}</div>
            <button type="button" class="btn-close" @click.prevent="show = false" aria-label="Close"></button>
        </div>
    </div>
</template>
