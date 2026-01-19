<template>
    <div class="pihole-manager">
        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto">
                <h1 class="mb-4 text-3xl font-bold text-primary-green">Pi-hole URL Manager</h1>
            </div>
            <div class="w-1/5 float-left ">
                <ul>
                    <li><a href="/converter">Converter</a></li>
                    <li><a href="/urllist">UrlList</a></li>
                </ul>
            </div>
            <div class="w-4/5 float-left">

                <div class="mb-6 pro-alert-info p-4">
                    <h2 class="mb-2 text-lg font-semibold text-primary-green">Instructions</h2>
                    <ul class="space-y-1 text-muted-green text-sm">
                        <li>Enter a URL that is being blocked by Pi-hole</li>
                        <li>Use the disable button to temporarily bypass DNS blocking for 6 minutes</li>
                        <li>All submitted URLs are logged for reference</li>
                        <li>View your URL history in the Archive section</li>
                    </ul>
                </div>

                <div class="pro-card p-6 mb-6">
                    <div v-if="successMessage" class="mt-4 pro-alert-success p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-primary-green font-medium">{{ successMessage }}</p>
                            </div>
                        </div>
                    </div>

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
                    <form @submit.prevent="submitForm" class="">
                        <div class="mb-6">
                            <label for="url" class="mb-2 block text-md font-medium text-primary-green">URL</label>
                            <input
                                id="url"
                                v-model="formData.url"
                                type="text"
                                placeholder="https://example.com"
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
                            Processing...
                        </span>
                            <span v-else>Submit URL</span>
                        </button>
                    </form>
                    <div class="mt-8 pt-6 border-t border-border text-center">
                        <p class="mb-4 text-sm text-muted-green font-medium">Temporarily Disable Pi-hole</p>
                        <form @submit.prevent="disablePi" class="flex justify-center">
                            <button
                                type="submit"
                                :disabled="isDisablePiLoading"
                                class="cursor-pointer pro-action-button focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                title="Temporarily disable Pi-hole for 5 minutes"
                            >
                                <span v-if="isDisablePiLoading">
                                    <svg class="h-6 w-6 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                <span v-else>
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <a href="/urllist" class="pro-link">View URL Archive</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import axios from 'axios';
import { defineComponent } from 'vue';
import { Sidebar } from '@/components/ui/sidebar';
import UrlHistory from '@/components/UrlList.vue';
import UrlList from '@/pages/UrlList.vue';

interface FormData {
    url: string;
}

interface Errors {
    url?: string;
}

export default defineComponent({
    name: 'PiholeForm',
    components: { UrlList, UrlHistory, Sidebar },
    data() {
        return {
            formData: {
                url: '',
            } as FormData,
            errors: {} as Errors,
            isSubmitLoading: false,
            isDisablePiLoading: false,
            successMessage: '',
            errorMessage: '',
            showConfirmationPopup: false,
            showError: true,
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

                this.successMessage = response.data.message || 'URL has been stored.';
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
});
</script>

<style scoped>
.container {
    max-width: 1200px;
}
</style>
