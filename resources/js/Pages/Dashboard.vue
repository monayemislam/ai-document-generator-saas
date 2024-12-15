<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recentClients: Array,
    recentProjects: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Clients Card -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
                        <dt class="truncate text-sm font-medium text-gray-500">Total Clients</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ stats.total_clients }}
                        </dd>
                    </div>

                    <!-- Projects Card -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
                        <dt class="truncate text-sm font-medium text-gray-500">Total Projects</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ stats.total_projects }}
                        </dd>
                    </div>

                    <!-- Revenue Card -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
                        <dt class="truncate text-sm font-medium text-gray-500">Total Revenue</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            ${{ stats.total_revenue.toLocaleString() }}
                        </dd>
                    </div>

                    <!-- Pending Amount Card -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
                        <dt class="truncate text-sm font-medium text-gray-500">Pending Amount</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            ${{ stats.pending_amount.toLocaleString() }}
                        </dd>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Recent Clients -->
                    <div class="overflow-hidden rounded-lg bg-white shadow">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900">Recent Clients</h3>
                            <div class="mt-6 flow-root">
                                <ul class="-my-5 divide-y divide-gray-200">
                                    <li v-for="client in recentClients" :key="client.id" class="py-5">
                                        <div class="flex items-center space-x-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900">
                                                    {{ client.name }}
                                                </p>
                                                <p class="truncate text-sm text-gray-500">
                                                    {{ client.email }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Projects -->
                    <div class="overflow-hidden rounded-lg bg-white shadow">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900">Recent Projects</h3>
                            <div class="mt-6 flow-root">
                                <ul class="-my-5 divide-y divide-gray-200">
                                    <li v-for="project in recentProjects" :key="project.id" class="py-5">
                                        <div class="flex items-center space-x-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900">
                                                    {{ project.name }}
                                                </p>
                                                <p class="truncate text-sm text-gray-500">
                                                    {{ project.client.name }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
