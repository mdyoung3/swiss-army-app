<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from '../components/NavBar.vue';
</script>

<template>
    <Head title="Submit URL - Network Manager">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
    </Head>

    <div class="min-h-screen scanline">
        <NavBar />

        <div class="container mx-auto px-4 py-8 pt-24">
            <div class="max-w-3xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold terminal-glow mb-3">
                        <span class="terminal-text">></span> Submit URL
                    </h1>
                    <p class="terminal-secondary text-sm md:text-base">
                        Temporarily disable Pi-hole DNS blocking for specific URLs
                    </p>
                </div>

                <!-- Instructions Card -->
                <div class="terminal-card rounded p-6 mb-6">
                    <h2 class="terminal-text font-semibold mb-3 flex items-center">
                        <span class="mr-2 text-lg">📋</span>
                        Instructions
                    </h2>
                    <ul class="terminal-secondary space-y-2 text-sm">
                        <li class="flex items-start">
                            <span class="terminal-text mr-2 mt-0.5">></span>
                            <span>Enter the URL that is being blocked by your DNS filter</span>
                        </li>
                        <li class="flex items-start">
                            <span class="terminal-text mr-2 mt-0.5">></span>
                            <span>Submit to temporarily disable the firewall for 6 minutes</span>
                        </li>
                        <li class="flex items-start">
                            <span class="terminal-text mr-2 mt-0.5">></span>
                            <span>All submissions are logged for security monitoring</span>
                        </li>
                        <li class="flex items-start">
                            <span class="terminal-text mr-2 mt-0.5">></span>
                            <span>View all logged URLs in the URL List page</span>
                        </li>
                    </ul>
                </div>

                <!-- Manager Form Component -->
                <ManagerForm />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import axios from 'axios';
import { defineComponent } from 'vue';

const ManagerForm = defineComponent({
    name: 'ManagerForm',
    data() {
        return {
            formData: {
                url: '',
            },
            errors: {} as { url?: string },
            isSubmitLoading: false,
            isDisablePiLoading: false,
            successMessage: '',
            errorMessage: '',
        };
    },
    methods: {
        validateUrl(url: string): boolean {
            try {
                const urlObj = new URL(url);
                return ['http:', 'https:'].includes(urlObj.protocol);
            } catch {
                return false;
            }
        },

        validateForm(): boolean {
            this.errors = {};

            if (!this.formData.url.trim()) {
                return true;
            }

            if (!this.validateUrl(this.formData.url)) {
                this.errors.url = 'Please enter a valid URL (must include http:// or https://)';
                return false;
            }

            return true;
        },

        async submitForm(): Promise<void> {
            this.successMessage = '';
            this.errorMessage = '';

            if (!this.validateForm()) {
                return;
            }

            this.isSubmitLoading = true;

            try {
                const response = await axios.post('/api/pihole/add-url', {
                    url: this.formData.url,
                });

                this.successMessage = response.data.message || 'URL has been stored successfully.';
                this.formData.url = '';
            } catch (error: any) {
                console.error('Error:', error);
                this.errorMessage = error.response?.data?.message || 'An error occurred while processing your request.';
            } finally {
                this.isSubmitLoading = false;
            }
        },

        closeError() {
            this.errorMessage = '';
        },

        closeSuccess() {
            this.successMessage = '';
        },

        async disablePi(): Promise<void> {
            this.successMessage = '';
            this.errorMessage = '';

            if (!this.validateForm()) {
                return;
            }

            this.isDisablePiLoading = true;

            try {
                const response = await axios.post('/api/pihole/temporary-disable', {
                    url: this.formData.url,
                });

                this.successMessage = response.data.message || 'Pi-hole temporarily disabled successfully!';
                this.formData.url = '';
            } catch (error: any) {
                console.error('Error:', error);
                this.errorMessage = error.response?.data?.message || 'An error occurred while processing your request.';
            } finally {
                this.isDisablePiLoading = false;
            }
        },
    },
    template: `
        <div class="space-y-6">
            <!-- Success Message -->
            <transition name="fade">
                <div v-if="successMessage" class="terminal-alert-success rounded p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <span class="text-xl terminal-glow">✓</span>
                            <div>
                                <p class="terminal-text font-medium text-sm">{{ successMessage }}</p>
                            </div>
                        </div>
                        <button @click="closeSuccess" class="terminal-text hover:opacity-70 transition-opacity ml-4">
                            <span class="text-xl">×</span>
                        </button>
                    </div>
                </div>
            </transition>

            <!-- Error Message -->
            <transition name="fade">
                <div v-if="errorMessage" class="terminal-alert-error rounded p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <span class="text-xl text-[#ff6b6b]">⚠</span>
                            <div>
                                <p class="text-[#ff6b6b] font-medium text-sm">{{ errorMessage }}</p>
                            </div>
                        </div>
                        <button @click="closeError" class="text-[#ff6b6b] hover:opacity-70 transition-opacity ml-4">
                            <span class="text-xl">×</span>
                        </button>
                    </div>
                </div>
            </transition>

            <!-- Submit URL Form -->
            <div class="terminal-card rounded p-6">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <label for="url" class="block terminal-text text-sm font-medium mb-3">
                            <span class="mr-2">$</span>Enter URL:
                        </label>
                        <input
                            id="url"
                            v-model="formData.url"
                            type="text"
                            placeholder="https://example.com"
                            class="w-full terminal-input rounded px-4 py-3 text-sm"
                            :class="{ 'border-[#ff6b6b]': errors.url }"
                        />
                        <p v-if="errors.url" class="mt-2 text-sm text-[#ff6b6b]">
                            {{ errors.url }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="isSubmitLoading"
                        class="w-full terminal-button-primary terminal-button rounded px-6 py-3 font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <span v-if="isSubmitLoading" class="flex items-center justify-center">
                            <svg
                                class="animate-spin -ml-1 mr-3 h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Processing...
                        </span>
                        <span v-else><span class="mr-2">></span>Submit URL</span>
                    </button>
                </form>
            </div>

            <!-- Emergency Disable -->
            <div class="terminal-card rounded p-6">
                <div class="text-center">
                    <h3 class="terminal-text font-semibold mb-3 text-lg">Emergency Disable</h3>
                    <p class="terminal-secondary text-sm mb-6">
                        Click below to temporarily disable Pi-hole DNS filtering for 6 minutes
                    </p>

                    <button
                        @click="disablePi"
                        :disabled="isDisablePiLoading"
                        class="terminal-button px-8 py-4 rounded font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-all inline-flex items-center justify-center hover:scale-105"
                    >
                        <span v-if="isDisablePiLoading" class="flex items-center">
                            <svg
                                class="animate-spin -ml-1 mr-3 h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Processing...
                        </span>
                        <span v-else class="text-lg">
                            <span class="mr-2">⚡</span>Disable Pi-hole
                        </span>
                    </button>
                </div>
            </div>

            <!-- Quick Link -->
            <div class="text-center">
                <a href="/urllist" class="terminal-secondary hover:terminal-text transition-colors text-sm underline">
                    <span class="mr-1">#</span>View all logged URLs
                </a>
            </div>
        </div>
    `,
});

export default defineComponent({
    name: 'Manager',
    components: {
        NavBar,
        ManagerForm,
    },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap');

* {
    font-family: 'JetBrains Mono', 'Fira Code', 'Source Code Pro', monospace;
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
