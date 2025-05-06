<template>
    <div>
        <HeaderComponent />

        <main class="container py-4">
            <slot />
        </main>

        <FooterComponent />
    </div>
</template>

<script setup>
import HeaderComponent from "@/Components/HeaderComponent.vue";
import FooterComponent from "@/Components/FooterComponent.vue";
</script>
