<template>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 font-sans text-gray-900">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="mx-auto h-12 w-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Uptime Monitor</h1>
                <p class="mt-2 text-lg text-gray-600">Check the status of your monitored websites in real-time.</p>
            </div>

            <!-- Client Selection -->
            <div class="max-w-xl mx-auto mb-10">
                <label for="client" class="block text-sm font-medium text-gray-700 mb-2">Select Client</label>
                <div class="relative">
                    <select 
                        v-model="selectedClient" 
                        id="client" 
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base appearance-none bg-white transition ease-in-out duration-150"
                        style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e&quot;); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                    >
                        <option :value="null" disabled>Choose a client...</option>
                        <option v-for="client in clients" :key="client.id" :value="client">
                            {{ client.name }} ({{ client.email }})
                        </option>
                    </select>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!selectedClient && !isLoading" class="text-center py-12 bg-white rounded-2xl shadow-sm border border-gray-100">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No client selected</h3>
                <p class="mt-1 text-sm text-gray-500">Select a client from the dropdown to view their websites.</p>
            </div>

            <!-- Client Info -->
            <div v-if="selectedClient" class="text-center mb-10">
                <h2 class="text-xl font-medium text-gray-700">
                    Welcome, <span class="text-indigo-600 font-bold">{{ selectedClient.name }}</span>
                </h2>
                <p class="text-gray-500">{{ selectedClient.email }}</p>
            </div>

            <!-- Website List -->
            <div v-if="selectedClient" class="transition-all duration-300 ease-in-out">
                
                <!-- Add Website Form -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Add New Website</h3>
                    <div class="flex gap-4">
                        <input 
                            v-model="newWebsiteUrl" 
                            type="url" 
                            placeholder="https://example.com" 
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
                            @keyup.enter="addWebsite"
                        >
                        <button 
                            @click="addWebsite" 
                            :disabled="isAdding"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
                        >
                            <svg v-if="isAdding" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-else>Add Website</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Monitored Websites</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ selectedClient.websites.length }} monitored
                        </span>
                    </div>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="website in selectedClient.websites" :key="website.id" class="px-6 py-5 hover:bg-gray-50 transition duration-150 ease-in-out group">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center mb-1">
                                        <p class="text-base font-medium text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                            {{ website.url }}
                                        </p>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            <span 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize border"
                                                :class="website.is_up 
                                                    ? 'bg-green-50 text-green-700 border-green-200' 
                                                    : 'bg-red-50 text-red-700 border-red-200'"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="website.is_up ? 'bg-green-500' : 'bg-red-500'"></span>
                                                {{ website.is_up ? 'Operational' : 'Down' }}
                                            </span>
                                        </div>
                                        <div v-if="website.last_checked_at" class="flex items-center" title="Last Checked">
                                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-xs">{{ new Date(website.last_checked_at).toLocaleString() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                                    <button 
                                        @click="confirmVisit(website.url)" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                    >
                                        Visit
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </button>
                                    <button 
                                        @click="deleteWebsite(website)" 
                                        class="inline-flex items-center px-3 py-2 border border-red-200 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                    >
                                        Delete
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="text-center py-12">
                 <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-gray-500">Loading client data...</p>
            </div>

            <!-- Visit Modal -->
            <div v-if="showModal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Visit External Site</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                You are about to visit <span class="font-bold text-gray-800">{{ targetUrl }}</span>. 
                                                This link will open in a new tab. Do you want to continue?
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button" @click="proceedToUrl" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors">Continue</button>
                                <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const clients = ref([]);
const selectedClient = ref(null);
const showModal = ref(false);
const targetUrl = ref('');
const newWebsiteUrl = ref('');
const isAdding = ref(false);
const isLoading = ref(true);
let pollingInterval = null;

const fetchClients = async (background = false) => {
    if (!background) isLoading.value = true;
    try {
        const response = await axios.get('/api/clients');
        clients.value = response.data;
        
        // If a client is selected, update their data to reflect new statuses
        if (selectedClient.value) {
            const updatedClient = clients.value.find(c => c.id === selectedClient.value.id);
            if (updatedClient) {
                selectedClient.value = updatedClient;
            }
        }
    } catch (error) {
        console.error('Error fetching clients:', error);
    } finally {
        if (!background) isLoading.value = false;
    }
};

const confirmVisit = (url) => {
    targetUrl.value = url;
    showModal.value = true;
};

const proceedToUrl = () => {
    window.open(targetUrl.value, '_blank');
    showModal.value = false;
};

const addWebsite = async () => {
    if (!newWebsiteUrl.value || !selectedClient.value) return;
    
    isAdding.value = true;
    try {
        const response = await axios.post('/api/websites', {
            client_id: selectedClient.value.id,
            url: newWebsiteUrl.value
        });
        
        // Add to list immediately (will be refreshed by polling later too)
        selectedClient.value.websites.push(response.data);
        newWebsiteUrl.value = '';
    } catch (error) {
        console.error('Error adding website:', error);
        alert('Failed to add website. Please check the URL.');
    } finally {
        isAdding.value = false;
    }
};

const deleteWebsite = async (website) => {
    if (!confirm('Are you sure you want to delete this website?')) return;

    try {
        await axios.delete(`/api/websites/${website.id}`);
        // Remove from list
        selectedClient.value.websites = selectedClient.value.websites.filter(w => w.id !== website.id);
    } catch (error) {
        console.error('Error deleting website:', error);
        alert('Failed to delete website.');
    }
};

onMounted(() => {
    fetchClients();
    // Poll every 30 seconds
    pollingInterval = setInterval(() => {
        fetchClients(true);
    }, 30000);
});

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});
</script>
