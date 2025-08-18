<template>
    <div class="pihole-manager">
        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto max-w-2xl">
                <h1 class="mb-4 text-3xl font-bold neon-red neon-text">Convert Video to MP3</h1>

                <div class="mb-6 apocalypse-alert p-4 rounded-md">
                    <h2 class="mb-2 text-lg font-semibold neon-red">INSTRUCTIONS:</h2>
                    <ul class="space-y-1 neon-green">
                        <li>Enter URL of video being converted</li>
                    </ul>
                </div>

                <div class="rounded-lg zombie-card p-6 shadow-md mb-6">
                    <!-- Success Message with Download Link -->
                    <div v-if="successMessage" class="mt-4 rounded-md survivor-success p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-2xl neon-green">✅</span>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm neon-green font-medium mb-2">🎯 Conversion completed!</p>
                                <div v-if="downloadData" class="space-y-2">
                                    <p class="text-xs neon-green">File: {{ downloadData.file_name }}</p>
                                    <p class="text-xs neon-green">Size: {{ formatFileSize(downloadData.file_size) }}</p>
                                    <a
                                        :href="downloadData.file_url"
                                        download
                                        class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
                                    >
                                        📥 Download MP3
                                    </a>
                                </div>
                            </div>
                            <button @click="closeSuccess" class="ml-auto cursor-pointer">
                                <span class="text-2xl neon-green">×</span>
                            </button>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="mt-4 rounded-md apocalypse-alert p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-2xl neon-red">💀</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm neon-red font-medium">🚨 {{ errorMessage }}</p>
                            </div>
                            <button @click="closeError" class="ml-auto cursor-pointer">
                                <span class="text-2xl neon-red">×</span>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitForm" class="">
                        <div class="mb-6">
                            <label for="url" class="mb-2 block text-md font-medium neon-green">Enter video URL to convert:</label>
                            <input
                                id="url"
                                v-model="formData.url"
                                type="text"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full rounded-md zombie-input px-3 py-2 shadow-sm focus:outline-none"
                                :class="{ 'border-red-500 shadow-red-500/50': errors.url }"
                            />
                            <p v-if="errors.url" class="mt-1 text-sm neon-red">
                                {{ errors.url }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="isSubmitLoading"
                            class="cursor-pointer w-full rounded-md zombie-button px-4 py-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span v-if="isSubmitLoading" class="flex items-center justify-center">
                                <svg
                                    class="mr-3 -ml-1 h-5 w-5 animate-spin text-white"
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
                                Converting...
                            </span>
                            <span v-else>CONVERT TO MP3</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import axios from 'axios';
import { defineComponent } from 'vue';

interface FormData {
    url: string;
}

interface Errors {
    url?: string;
}

interface DownloadData {
    file_name: string;
    file_url: string;
    file_size: number;
}

export default defineComponent({
    name: 'Mp3Converter',
    data() {
        return {
            formData: {
                url: '',
            } as FormData,
            errors: {} as Errors,
            isSubmitLoading: false,
            successMessage: '',
            errorMessage: '',
            downloadData: null as DownloadData | null,
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
                this.errors.url = 'Please enter a video URL';
                return false;
            }
            if (!this.validateUrl(this.formData.url)) {
                this.errors.url = 'Please enter a valid URL (must include http:// or https://)';
                return false;
            }
            return true;
        },

        formatFileSize(bytes: number): string {
            if (!bytes) return '0 Bytes';

            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));

            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        },

        async submitForm(): Promise<void> {
            this.successMessage = '';
            this.errorMessage = '';
            this.downloadData = null;

            if (!this.validateForm()) {
                return;
            }

            this.isSubmitLoading = true;

            try {
                const response = await axios.post('/api/converter', {
                    url: this.formData.url,
                });

                console.log('Response:', response.data);

                // Check if the conversion was successful
                if (response.data.success) {
                    this.successMessage = 'Video converted successfully!';
                    this.downloadData = {
                        file_name: response.data.file_name,
                        file_url: response.data.file_url,
                        file_size: response.data.file_size
                    };
                    this.formData.url = ''; // Clear the form
                } else {
                    this.errorMessage = response.data.error || 'Conversion failed. Please try again.';
                }

            } catch (error: any) {
                console.error('Error:', error);

                if (error.response?.status === 400 && error.response?.data?.error) {
                    this.errorMessage = error.response.data.error;
                } else if (error.response?.data?.message) {
                    this.errorMessage = error.response.data.message;
                } else {
                    this.errorMessage = 'An error occurred while converting your video. Please try again.';
                }
            } finally {
                this.isSubmitLoading = false;
            }
        },

        closeError(): void {
            this.errorMessage = '';
        },

        closeSuccess(): void {
            this.successMessage = '';
            this.downloadData = null;
        },
    },
});
</script>

<style scoped>
.container {
    max-width: 1200px;
}

/* Additional styles for better download button appearance */
a.download-button {
    display: inline-block;
    background-color: #16a34a;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.2s;
}

a.download-button:hover {
    background-color: #15803d;
}
</style>
