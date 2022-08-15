<template>
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center pt-6 pt-sm-0 bg-light">
        <div>
            <slot name="logo" />
        </div>

        <div class="card shadow-sm px-6 py-3 mt-6">
            <slot />
        </div>
    </div>
</template>
