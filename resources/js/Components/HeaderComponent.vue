<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">TaskApp</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <Link class="nav-link" href="/">Home</Link>
                    </li>
                    <li class="nav-item">
                        <Link class="nav-link" href="/tasks">Tasks</Link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
</script>
