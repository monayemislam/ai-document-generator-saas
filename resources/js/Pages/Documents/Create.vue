<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const loading = ref(false);
const generatedContent = ref('');
const error = ref('');

const form = ref({
    type: 'proposal',
    client_name: '',
    project_name: '',
    budget: '',
    timeline: '',
    scope: ''
});

const generateDocument = async () => {
    loading.value = true;
    error.value = '';
    generatedContent.value = '';

    try {
        const response = await axios.post('/api/generate-document', form.value);
        generatedContent.value = response.data.content;
    } catch (e) {
        error.value = e.response?.data?.error || 'An error occurred';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Generate Document" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Generate Document
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="generateDocument" class="space-y-6">
                            <!-- Document Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Document Type
                                </label>
                                <select v-model="form.type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="proposal">Business Proposal</option>
                                    <option value="invoice">Invoice</option>
                                </select>
                            </div>

                            <!-- Client Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Client Name
                                </label>
                                <input type="text" 
                                       v-model="form.client_name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                       required>
                            </div>

                            <!-- Project Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Project Name
                                </label>
                                <input type="text" 
                                       v-model="form.project_name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                       required>
                            </div>

                            <!-- Budget -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Budget
                                </label>
                                <input type="text" 
                                       v-model="form.budget"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                       required>
                            </div>

                            <!-- Timeline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Timeline
                                </label>
                                <input type="text" 
                                       v-model="form.timeline"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                       required>
                            </div>

                            <!-- Scope -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Project Scope
                                </label>
                                <textarea v-model="form.scope"
                                          rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          required></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button type="submit"
                                        :disabled="loading"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                                    {{ loading ? 'Generating...' : 'Generate Document' }}
                                </button>
                            </div>
                        </form>

                        <!-- Error Message -->
                        <div v-if="error" class="mt-4 text-red-600">
                            {{ error }}
                        </div>

                        <!-- Generated Content -->
                        <div v-if="generatedContent" class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">Generated Document</h3>
                            <div class="mt-2 whitespace-pre-wrap rounded-md bg-gray-50 p-4">
                                {{ generatedContent }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
