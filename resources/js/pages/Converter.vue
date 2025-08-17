<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ConverterForm from '../components/ConverterForm.vue';
</script>

<template>
    <Head title="Pi-hole URL Manager">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <ConverterForm />
</template>
