<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from '../components/NavBar.vue';
</script>

<template>
    <Head title="Parental Controls - Network Manager">
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
                        <span class="terminal-text">@</span> Parental Controls
                    </h1>
                    <p class="terminal-secondary text-sm md:text-base">
                        Block social media platforms and manage access for your family
                    </p>
                </div>

                <!-- Description Card -->
                <div class="terminal-card rounded p-6 mb-6">
                    <h2 class="terminal-text font-semibold mb-3 flex items-center">
                        <span class="mr-2 text-lg">ℹ️</span>
                        About Parental Controls
                    </h2>
                    <p class="terminal-secondary text-sm leading-relaxed">
                        Use this feature to temporarily block social media sites and other platforms using your Pi-hole backend.
                        Select a platform from the list below and click "Block" to restrict access across your home network.
                    </p>
                </div>

                <!-- Parental Controls Form -->
                <ParentalControlsForm />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import axios from 'axios';
import { defineComponent } from 'vue';

const ParentalControlsForm = defineComponent({
    name: 'ParentalControlsForm',
    data() {
        return {
            selectedPlatform: '',
            platforms: [
                { value: 'youtube', label: '📺 YouTube', domain: 'youtube.com' },
                { value: 'instagram', label: '📷 Instagram', domain: 'instagram.com' },
                { value: 'facebook', label: '👍 Facebook', domain: 'facebook.com' },
                { value: 'snapchat', label: '👻 Snapchat', domain: 'snapchat.com' },
                { value: 'telegram', label: '✈️ Telegram', domain: 'telegram.org' },
                { value: 'tiktok', label: '🎵 TikTok', domain: 'tiktok.com' },
                { value: 'twitter', label: '🐦 Twitter/X', domain: 'twitter.com' },
                { value: 'reddit', label: '🤖 Reddit', domain: 'reddit.com' },
            ],
            isLoading: false,
            successMessage: '',
            errorMessage: '',
        };
    },
    methods: {
        async blockPlatform(): Promise<void> {
            if (!this.selectedPlatform) {
                this.errorMessage = 'Please select a platform to block';
                return;
            }

            this.successMessage = '';
            this.errorMessage = '';
            this.isLoading = true;

            try {
                const platform = this.platforms.find(p => p.value === this.selectedPlatform);

                // Using the blocked-urls API endpoint to block the platform
                const response = await axios.post('/api/blocked-urls', {
                    url: `https://${platform?.domain}`,
                });

                this.successMessage = `${platform?.label} has been blocked successfully!`;
                this.selectedPlatform = '';
            } catch (error: any) {
                console.error('Error:', error);
                this.errorMessage = error.response?.data?.message || 'Failed to block platform. Please try again.';
            } finally {
                this.isLoading = false;
            }
        },

        closeError() {
            this.errorMessage = '';
        },

        closeSuccess() {
            this.successMessage = '';
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

            <!-- Block Platform Form -->
            <div class="terminal-card rounded p-6">
                <h3 class="terminal-text font-semibold mb-4 text-lg">
                    <span class="mr-2">🚫</span>Block Social Media Platform
                </h3>

                <form @submit.prevent="blockPlatform" class="space-y-6">
                    <div>
                        <label for="platform" class="block terminal-text text-sm font-medium mb-3">
                            <span class="mr-2">$</span>Select Platform:
                        </label>
                        <select
                            id="platform"
                            v-model="selectedPlatform"
                            class="w-full terminal-input rounded px-4 py-3 text-sm cursor-pointer"
                        >
                            <option value="" disabled>-- Choose a platform --</option>
                            <option
                                v-for="platform in platforms"
                                :key="platform.value"
                                :value="platform.value"
                            >
                                {{ platform.label }}
                            </option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        :disabled="isLoading || !selectedPlatform"
                        class="w-full px-6 py-3 bg-[#ff6b6b33] border border-[#ff6b6b] text-[#ff6b6b] rounded font-semibold hover:bg-[#ff6b6b55] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="isLoading" class="flex items-center justify-center">
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
                            Blocking...
                        </span>
                        <span v-else><span class="mr-2">🚫</span>Block Platform</span>
                    </button>
                </form>
            </div>

            <!-- Info Box -->
            <div class="terminal-card rounded p-6">
                <h3 class="terminal-text font-semibold mb-3 flex items-center">
                    <span class="mr-2">💡</span>How It Works
                </h3>
                <ul class="terminal-secondary text-sm space-y-2">
                    <li class="flex items-start">
                        <span class="terminal-text mr-2 mt-0.5">1.</span>
                        <span>Select a social media platform from the dropdown menu above</span>
                    </li>
                    <li class="flex items-start">
                        <span class="terminal-text mr-2 mt-0.5">2.</span>
                        <span>Click "Block Platform" to add it to your blocked URLs list</span>
                    </li>
                    <li class="flex items-start">
                        <span class="terminal-text mr-2 mt-0.5">3.</span>
                        <span>The platform will be blocked across all devices on your network</span>
                    </li>
                    <li class="flex items-start">
                        <span class="terminal-text mr-2 mt-0.5">4.</span>
                        <span>To unblock, visit the URL List page and remove the entry</span>
                    </li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center text-center">
                <a href="/urllist" class="terminal-secondary hover:terminal-text transition-colors text-sm underline">
                    <span class="mr-1">#</span>View all blocked URLs
                </a>
                <a href="/manager" class="terminal-secondary hover:terminal-text transition-colors text-sm underline">
                    <span class="mr-1">+</span>Submit a URL
                </a>
            </div>
        </div>
    `,
});

export default defineComponent({
    name: 'ParentalControls',
    components: {
        NavBar,
        ParentalControlsForm,
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

select.terminal-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2300ff66'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>
