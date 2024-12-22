<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { formatDate, formatCurrency, formatNumber } from '@/utils/formatters';

defineProps({
    document: {
        type: Object,
        required: true
    },
    logoUrl: {
        type: String,
        required: false,
        default: null
    }
});

const handleImageError = (e) => {
    console.error('Image failed to load:', e.target.src);
};

const handleImageLoad = () => {
    console.log('Image loaded successfully');
};
</script>

<template>
    <Head :title="`Document - ${document.project_title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Document Details
                </h2>
                <div class="flex space-x-4">
                    <Link 
                        :href="route('documents.index')" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                    >
                        Back to List
                    </Link>
                    <Link 
                        :href="route('documents.pdf', document.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        target="_blank"
                    >
                        Download PDF
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <!-- Document Header -->
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ document.project_title }}</h1>
                            <p class="text-gray-600">Document #{{ document.id }}</p>
                        </div>
                        <div v-if="logoUrl" class="w-32">
                            <img 
                                :src="logoUrl" 
                                alt="Company Logo" 
                                class="w-full h-auto object-contain"
                                @error="handleImageError"
                                @load="handleImageLoad"
                            />
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Client Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Client Name</p>
                                <p>{{ document.client_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Company</p>
                                <p>{{ document.company_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Contact</p>
                                <p>{{ document.contact_person }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p>{{ document.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Project Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Project Details</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Start Date</p>
                                    <p>{{ document.start_date }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">End Date</p>
                                    <p>{{ document.end_date }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Description</p>
                                <p class="mt-1">{{ document.project_description }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Scope</p>
                                <p class="mt-1">{{ document.project_scope }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Items -->
                    <div v-if="document.items?.length" class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Cost Breakdown</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in document.items" :key="item.id">
                                    <td class="px-6 py-4">{{ item.description }}</td>
                                    <td class="px-6 py-4 text-right">{{ item.quantity }}</td>
                                    <td class="px-6 py-4 text-right">{{ item.unit_price }}</td>
                                    <td class="px-6 py-4 text-right">{{ item.total_cost }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
