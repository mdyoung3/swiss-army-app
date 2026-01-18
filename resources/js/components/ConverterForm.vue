<template>
    <div class="pihole-manager">
        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto max-w-2xl">
                <h1 class="mb-4 text-3xl font-bold text-primary-green">Video to MP3 Converter</h1>

                <div class="mb-6 pro-alert-info p-4">
                    <h2 class="mb-2 text-lg font-semibold text-primary-green">Instructions</h2>
                    <ul class="space-y-1 text-muted-green text-sm">
                        <li>Enter the URL of the video you want to convert</li>
                    </ul>
                </div>

                <div class="pro-card p-6 mb-6">
                    <!-- Success Message with Download Link -->
                    <div v-if="successMessage" class="mt-4 pro-alert-success p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-primary-green font-medium mb-2">Conversion completed!</p>
                                <div v-if="downloadData" class="space-y-2">
                                    <p class="text-xs text-muted-green">File: {{ downloadData.file_name }}</p>
                                    <p class="text-xs text-muted-green">Size: {{ formatFileSize(downloadData.file_size) }}</p>
                                    <a
                                        :href="downloadData.file_url"
                                        download
                                        class="inline-block pro-button-primary px-4 py-2 text-sm font-medium"
                                    >
                                        Download MP3
                                    </a>
                                </div>
                            </div>
                            <button @click="closeSuccess" class="ml-auto cursor-pointer text-primary-green hover:text-bright-green">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="mt-4 pro-alert-error p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-red-400 font-medium">{{ errorMessage }}</p>
                            </div>
                            <button @click="closeError" class="ml-auto cursor-pointer text-red-400 hover:text-red-300">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitForm" class="">
                        <div class="mb-6">
                            <label for="url" class="mb-2 block text-md font-medium text-primary-green">Video URL</label>
                            <input
                                id="url"
                                v-model="formData.url"
                                type="text"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full pro-input px-3 py-2"
                                :class="{ 'border-red-500': errors.url }"
                            />
                            <p v-if="errors.url" class="mt-1 text-sm text-red-400">
                                {{ errors.url }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="isSubmitLoading"
                            class="cursor-pointer w-full pro-button-primary px-4 py-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span v-if="isSubmitLoading" class="flex items-center justify-center">
                                <svg
                                    class="mr-3 -ml-1 h-5 w-5 animate-spin"
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
                            <span v-else>Convert to MP3</span>
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
</style>
