<template>
    <div class="url-history">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold terminal-text"><span class="mr-2">#</span>Submitted URLs</h2>
                <a
                    href="/manager"
                    class="terminal-button px-4 py-2 rounded hover:scale-105 transition-transform"
                >
                    <span class="mr-2">+</span>Add New URL
                </a>
            </div>

            <div v-if="isLoading" class="flex justify-center items-center py-8">
                <svg class="animate-spin h-8 w-8 terminal-text" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div v-else-if="urls.length === 0" class="text-center py-12 terminal-card rounded">
                <div class="terminal-secondary text-lg mb-4">[ No URLs stored yet ]</div>
                <a
                    href="/manager"
                    class="terminal-text hover:terminal-glow transition-all underline"
                >
                    <span class="mr-1">+</span>Add your first URL
                </a>
            </div>

            <div v-else class="terminal-card rounded overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                        <tr class="border-b border-[#00ff6633]">
                            <th class="px-6 py-4 text-left text-xs font-medium terminal-text uppercase tracking-wider">
                                <span class="mr-2">></span>URL
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium terminal-text uppercase tracking-wider hidden md:table-cell">
                                <span class="mr-2">⏰</span>Added Date
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium terminal-text uppercase tracking-wider">
                                <span class="mr-2">⚙</span>Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-[#00ff6622]">
                        <tr v-for="url in urls" :key="url.id" class="terminal-table-row">
                            <td class="px-6 py-4">
                                <div class="text-sm terminal-secondary break-all">
                                    <a
                                        :href="url.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="terminal-text hover:terminal-glow transition-all underline"
                                    >
                                        {{ url.url }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm terminal-secondary hidden md:table-cell">
                                {{ formatDate(url.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button
                                    @click="confirmDelete(url)"
                                    class="text-[#ff6b6b] hover:text-[#ff8888] p-2 rounded transition-all hover:bg-[#ff6b6b22]"
                                    :disabled="isDeletingId === url.id"
                                    title="Delete URL"
                                >
                                    <svg
                                        v-if="isDeletingId === url.id"
                                        class="animate-spin h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 bg-black bg-opacity-80 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
                @click="cancelDelete"
            >
                <div class="terminal-card border-2 border-[#00ff6666] w-full max-w-md shadow-2xl rounded-lg p-6" @click.stop>
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-[#ff6b6b22] border border-[#ff6b6b] mb-4">
                            <svg class="h-8 w-8 text-[#ff6b6b]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.882 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold terminal-text mb-3">Delete URL</h3>
                        <div class="mb-6">
                            <p class="text-sm terminal-secondary mb-3">
                                Are you sure you want to delete this URL? This action cannot be undone.
                            </p>
                            <div class="terminal-card rounded p-3 break-all">
                                <p class="text-sm terminal-text font-medium">
                                    {{ urlToDelete?.url }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button
                                @click="cancelDelete"
                                class="terminal-button px-6 py-2 rounded font-medium"
                            >
                                Cancel
                            </button>
                            <button
                                @click="deleteUrl"
                                class="px-6 py-2 bg-[#ff6b6b33] border border-[#ff6b6b] text-[#ff6b6b] rounded font-medium hover:bg-[#ff6b6b55] transition-all"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <transition name="fade">
                <div v-if="successMessage" class="mt-4 terminal-alert-success rounded p-4">
                    <div class="flex items-center">
                        <span class="terminal-glow text-lg mr-3">✓</span>
                        <p class="text-sm terminal-text">{{ successMessage }}</p>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div v-if="errorMessage" class="mt-4 terminal-alert-error rounded p-4">
                    <div class="flex items-center">
                        <span class="text-[#ff6b6b] text-lg mr-3">⚠</span>
                        <p class="text-sm text-[#ff6b6b]">{{ errorMessage }}</p>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'UrlHistory',
    data() {
        return {
            urls: [],
            isLoading: true,
            showDeleteModal: false,
            urlToDelete: null,
            isDeletingId: null,
            successMessage: '',
            errorMessage: ''
        }
    },
    async mounted() {
        await this.fetchUrls()
    },
    methods: {
        async fetchUrls() {
            try {
                this.isLoading = true
                const response = await axios.get('/api/urls')
                this.urls = response.data.data || response.data
            } catch (error) {
                console.error('Error fetching URLs:', error)
                this.errorMessage = 'Failed to load URLs'
            } finally {
                this.isLoading = false
            }
        },

        formatDate(dateString) {
            try {
                const date = new Date(dateString)
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
            } catch {
                return dateString
            }
        },

        confirmDelete(url) {
            this.urlToDelete = url
            this.showDeleteModal = true
            this.successMessage = ''
            this.errorMessage = ''
        },

        cancelDelete() {
            this.showDeleteModal = false
            this.urlToDelete = null
        },

        async deleteUrl() {
            if (!this.urlToDelete) return

            try {
                this.isDeletingId = this.urlToDelete.id
                await axios.delete(`/api/urls/${this.urlToDelete.id}`)

                // Remove from local array
                this.urls = this.urls.filter(url => url.id !== this.urlToDelete.id)

                this.successMessage = 'URL deleted successfully'
                this.showDeleteModal = false
                this.urlToDelete = null

            } catch (error) {
                console.error('Error deleting URL:', error)
                this.errorMessage = error.response?.data?.message || 'Failed to delete URL'
            } finally {
                this.isDeletingId = null
            }
        }
    }
}
</script>

<style scoped>
.container {
    max-width: 1200px;
}
</style>