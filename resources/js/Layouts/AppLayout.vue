<template>
    <div class="d-flex flex-column min-vh-100">
        <HeaderComponent />

        <main class="container py-4 flex-grow-1">
            <slot />
        </main>

        <FooterComponent />
    </div>
</template>

<script setup>
import HeaderComponent from "@/Components/HeaderComponent.vue";
import FooterComponent from "@/Components/FooterComponent.vue";
</script>
