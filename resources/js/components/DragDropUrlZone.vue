<template>
    <div class="drag-drop-zone mb-8">
        <div class="mb-6 rounded-lg terminal-card p-6">
            <h2 class="mb-4 text-xl font-semibold terminal-text"><span class="mr-2">🚫</span>Block URLs</h2>
            <p class="mb-4 terminal-secondary text-sm">Drag and drop URLs here to block them permanently</p>

            <div
                ref="dropZone"
                @drop="handleDrop"
                @dragover="handleDragOver"
                @dragenter="handleDragEnter"
                @dragleave="handleDragLeave"
                :class="[
                    'rounded-lg border-2 border-dashed p-8 text-center transition-all duration-200',
                    isDragOver ? 'border-[#00ff66] bg-[#00ff6622]' : 'border-[#00ff6644] bg-[#00ff6608]',
                ]"
            >
                <div class="flex flex-col items-center justify-center">
                    <svg class="mb-4 h-12 w-12 terminal-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                        />
                    </svg>
                    <p class="mb-2 text-lg font-medium terminal-text">
                        {{ isDragOver ? '[ Drop URL here ]' : '[ Drag URL here to block ]' }}
                    </p>
                    <p class="text-sm terminal-secondary">Or enter a URL manually below</p>
                </div>
            </div>

            <!-- Manual URL input -->
            <div class="mt-4">
                <div class="flex flex-col sm:flex-row gap-2">
                    <input
                        v-model="manualUrl"
                        type="text"
                        placeholder="https://example.com"
                        class="flex-1 terminal-input rounded px-4 py-2 text-sm"
                        @keyup.enter="addManualUrl"
                    />
                    <button
                        @click="addManualUrl"
                        :disabled="isLoading || !manualUrl.trim()"
                        class="px-6 py-2 bg-[#ff6b6b33] border border-[#ff6b6b] text-[#ff6b6b] rounded font-medium hover:bg-[#ff6b6b55] transition-all disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap"
                    >
                        <span class="mr-1">🚫</span>Block URL
                    </button>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <transition name="fade">
            <div v-if="successMessage" class="mb-4 terminal-alert-success rounded p-4">
                <div class="flex items-center">
                    <span class="terminal-glow text-lg mr-3">✓</span>
                    <p class="text-sm terminal-text">{{ successMessage }}</p>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="errorMessage" class="mb-4 terminal-alert-error rounded p-4">
                <div class="flex items-center">
                    <span class="text-[#ff6b6b] text-lg mr-3">⚠</span>
                    <p class="text-sm text-[#ff6b6b]">{{ errorMessage }}</p>
                </div>
            </div>
        </transition>

        <!-- Blocked URLs List -->
        <div v-if="blockedUrls.length > 0" class="terminal-card rounded-lg overflow-hidden">
            <div class="border-b border-[#00ff6633] px-6 py-4">
                <h3 class="text-lg font-medium terminal-text"><span class="mr-2">🚫</span>Blocked URLs</h3>
            </div>
            <ul class="divide-y divide-[#00ff6622]">
                <li v-for="blockedUrl in blockedUrls" :key="blockedUrl.id" class="flex items-center justify-between px-6 py-4 terminal-table-row">
                    <div class="flex-1 mr-4">
                        <p class="text-sm terminal-text break-all">{{ blockedUrl.url }}</p>
                        <p class="text-xs terminal-secondary mt-1">Added {{ formatDate(blockedUrl.created_at) }}</p>
                    </div>
                    <button
                        @click="removeBlockedUrl(blockedUrl.id)"
                        :disabled="isDeletingId === blockedUrl.id"
                        class="ml-4 rounded p-2 text-[#ff6b6b] hover:bg-[#ff6b6b22] transition-all flex-shrink-0"
                        title="Remove block"
                    >
                        <svg
                            v-if="isDeletingId === blockedUrl.id"
                            class="h-4 w-4 animate-spin"
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
                        <svg v-else class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'DragDropUrlZone',
    data() {
        return {
            isDragOver: false,
            manualUrl: '',
            isLoading: false,
            successMessage: '',
            errorMessage: '',
            blockedUrls: [],
            isDeletingId: null,
        };
    },
    async mounted() {
        await this.fetchBlockedUrls();
    },
    methods: {
        handleDragOver(event) {
            event.preventDefault();
        },

        handleDragEnter(event) {
            event.preventDefault();
            this.isDragOver = true;
        },

        handleDragLeave(event) {
            event.preventDefault();
            if (!this.$refs.dropZone.contains(event.relatedTarget)) {
                this.isDragOver = false;
            }
        },

        handleDrop(event) {
            event.preventDefault();
            this.isDragOver = false;

            const data = event.dataTransfer.getData('text/plain');
            if (data) {
                this.addBlockedUrl(data.trim());
            }
        },

        addManualUrl() {
            if (this.manualUrl.trim()) {
                this.addBlockedUrl(this.manualUrl.trim());
                this.manualUrl = '';
            }
        },

        validateUrl(url) {
            try {
                const urlObj = new URL(url);
                return ['http:', 'https:'].includes(urlObj.protocol);
            } catch {
                return false;
            }
        },

        async addBlockedUrl(url) {
            this.successMessage = '';
            this.errorMessage = '';

            if (!this.validateUrl(url)) {
                this.errorMessage = 'Please provide a valid URL (must include http:// or https://)';
                return;
            }

            this.isLoading = true;

            try {
                const response = await axios.post('/api/blocked-urls', { url });

                this.successMessage = 'URL blocked successfully';
                await this.fetchBlockedUrls();
            } catch (error) {
                console.error('Error blocking URL:', error);
                this.errorMessage = error.response?.data?.message || 'Failed to block URL';
            } finally {
                this.isLoading = false;
            }
        },

        async fetchBlockedUrls() {
            try {
                const response = await axios.get('/api/blocked-urls');
                this.blockedUrls = response.data.data || response.data;
            } catch (error) {
                console.error('Error fetching blocked URLs:', error);
            }
        },

        async removeBlockedUrl(id) {
            this.isDeletingId = id;
            this.successMessage = '';
            this.errorMessage = '';

            try {
                await axios.delete(`/api/blocked-urls/${id}`);
                this.blockedUrls = this.blockedUrls.filter((url) => url.id !== id);
                this.successMessage = 'Blocked URL removed successfully';
            } catch (error) {
                console.error('Error removing blocked URL:', error);
                this.errorMessage = 'Failed to remove blocked URL';
            } finally {
                this.isDeletingId = null;
            }
        },

        formatDate(dateString) {
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
            } catch {
                return dateString;
            }
        },
    },
};
</script>

<style scoped>
.drag-drop-zone {
    max-width: 100%;
}
</style>
