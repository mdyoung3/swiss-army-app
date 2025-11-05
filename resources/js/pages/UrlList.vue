<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from '../components/NavBar.vue';
import DragDropUrlZone from '../components/DragDropUrlZone.vue';
import UrlListComponent from '../components/UrlList.vue';
</script>

<template>
    <Head title="URL List - Network Manager">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
    </Head>

    <div class="min-h-screen scanline">
        <NavBar />

        <div class="container mx-auto px-4 py-8 pt-24">
            <div class="mx-auto max-w-6xl">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold terminal-glow mb-3">
                        <span class="terminal-text">#</span> URL Management
                    </h1>
                    <p class="terminal-secondary text-sm md:text-base">
                        View, manage, and block URLs for your network
                    </p>
                </div>

                <DragDropUrlZone />
                <UrlListComponent />
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap');

* {
    font-family: 'JetBrains Mono', 'Fira Code', 'Source Code Pro', monospace;
}
</style>
