<template>
    <div class="url-history">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-primary-green">URL History</h1>
                <a
                    href="/"
                    class="pro-button-primary px-4 py-2"
                >
                    Add New URL
                </a>
            </div>

            <div v-if="isLoading" class="flex justify-center items-center py-8">
                <svg class="animate-spin h-8 w-8 text-primary-green" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div v-else-if="urls.length === 0" class="text-center py-8">
                <div class="text-muted-green text-lg mb-4">No URLs stored yet</div>
                <a
                    href="/"
                    class="pro-link"
                >
                    Add your first URL
                </a>
            </div>

            <div v-else class="pro-table overflow-hidden">
                <table class="min-w-full">
                    <thead class="pro-table-header">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-green uppercase tracking-wider">
                            URL
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-green uppercase tracking-wider">
                            Added Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-green uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="url in urls" :key="url.id" class="pro-table-row">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <a
                                    :href="url.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="pro-link"
                                >
                                    {{ url.url }}
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-green">
                            {{ formatDate(url.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button
                                @click="confirmDelete(url)"
                                class="text-red-400 hover:text-red-300 p-1 rounded-md hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-transparent"
                                :disabled="isDeletingId === url.id"
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

            <!-- Delete Confirmation Modal -->
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 pro-overlay overflow-y-auto h-full w-full z-50"
                @click="cancelDelete"
            >
                <div class="relative top-20 mx-auto p-5 w-96 pro-modal" @click.stop>
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-900/30">
                            <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.882 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-primary-green mt-4">Delete URL</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-muted-green">
                                Are you sure you want to delete this URL? This action cannot be undone.
                            </p>
                            <p class="text-sm text-primary-green mt-2 font-medium break-all">
                                {{ urlToDelete?.url }}
                            </p>
                        </div>
                        <div class="items-center px-4 py-3 space-x-3">
                            <button
                                @click="cancelDelete"
                                class="pro-button-secondary px-4 py-2"
                            >
                                Cancel
                            </button>
                            <button
                                @click="deleteUrl"
                                class="pro-button-danger px-4 py-2"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="successMessage" class="mt-4 pro-alert-success p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-primary-green">{{ successMessage }}</p>
                    </div>
                </div>
            </div>

            <div v-if="errorMessage" class="mt-4 pro-alert-error p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-400">{{ errorMessage }}</p>
                    </div>
                </div>
            </div>
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
