<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, watch, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

const states = [
    'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
    // ... add more states or customize for your needs
]

const countries = [
    'United States', 'United Kingdom', 'Canada', 'Australia',
    // ... add more countries or customize for your needs
]

const form = reactive({
    // Client Information
    client_name: '',
    company_name: '',
    contact_person: '',
    email: '',
    phone: '',
    street_address: '',
    city: '',
    state: 'Alabama',
    zip_code: '',
    country: 'United States',

    // Project Details
    project_title: '',
    project_description: '',
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
    priority_level: 'medium',
    project_type: 'development',
    project_scope: '',

    // Pricing (at least one is required)
    hourly_rate: '',
    fixed_price: '',
    estimated_hours: '',
    items: [],

    // Payment Terms
    payment_method: 'bank_transfer',
    payment_due_date: new Date().toISOString().split('T')[0],
    payment_schedule: 'net_30',

    // Additional Terms
    cancellation_policy: 'Standard 30-day cancellation notice required.',
    revision_policy: 'Up to 2 rounds of revisions included.',
    custom_message: '',
    terms_agreed: false,

    // Branding
    logo: null,
    color_scheme: '#000000',
});

const loading = ref(false);
const error = ref('');
const errors = ref({});

const logoPreviewUrl = ref(null)

const addItem = () => {
    form.items.push({
        description: '',
        quantity: 1,
        unit_price: 0,
        total: 0
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const calculateItemTotal = (index) => {
    const item = form.items[index];
    return (item.quantity * item.unit_price).toFixed(2);
};

const handleLogoUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        // Validate file type
        if (!file.type.match('image.*')) {
            errors.value.logo = ['Please upload an image file']
            return
        }
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            errors.value.logo = ['Image size should be less than 2MB']
            return
        }
        
        // Set the file in form data
        form.logo = file
        
        // Create and store the preview URL
        logoPreviewUrl.value = URL.createObjectURL(file)
    }
}

const resetForm = () => {
    Object.keys(form).forEach(key => {
        if (Array.isArray(form[key])) {
            form[key] = [];
        } else if (typeof form[key] === 'boolean') {
            form[key] = false;
        } else {
            form[key] = '';
        }
    });
    form.priority_level = 'medium';
    form.project_type = 'development';
    form.payment_method = 'bank_transfer';
    form.payment_schedule = 'net_30';
    form.color_scheme = '#000000';
};

const validateForm = () => {
    if (!form.terms_agreed) {
        error.value = 'You must agree to the terms and conditions'
        return false
    }

    if (!form.hourly_rate && !form.fixed_price) {
        error.value = 'Either hourly rate or fixed price must be provided'
        return false
    }

    if (new Date(form.end_date) <= new Date(form.start_date)) {
        error.value = 'End date must be after start date'
        return false
    }

    return true
}

const submitForm = async () => {
    loading.value = true
    errors.value = {}
    error.value = ''

    if (!validateForm()) {
        loading.value = false
        return
    }

    try {
        const formData = new FormData()
        
        // Append all form fields to FormData
        Object.keys(form).forEach(key => {
            if (key === 'logo' && form[key]) {
                formData.append('logo', form[key])
            } else if (key === 'items') {
                formData.append('items', JSON.stringify(form[key]))
            } else if (form[key] !== null && form[key] !== undefined) {
                formData.append(key, form[key].toString())
            }
        })

        const response = await axios.post('/documents/generate', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json'
            }
        })

        if (response.data.success) {
            window.location.href = response.data.redirect
        }

    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
            // Scroll to first error
            const firstError = document.querySelector('.border-red-500')
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' })
            }
            error.value = 'Please check the form for errors'
        } else {
            error.value = e.response?.data?.message || 'An error occurred'
        }
    } finally {
        loading.value = false
    }
}

watch(() => form.start_date, (newStartDate) => {
    // If end date is before start date, reset it
    if (form.end_date && new Date(form.end_date) <= new Date(newStartDate)) {
        form.end_date = ''
    }
})

// Set end date to tomorrow by default
onMounted(() => {
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    form.end_date = tomorrow.toISOString().split('T')[0]
})

// Cleanup object URL when component is destroyed
onBeforeUnmount(() => {
    if (logoPreviewUrl.value) {
        URL.revokeObjectURL(logoPreviewUrl.value)
    }
})
</script>

<template>
    <Head title="Generate Document" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Document
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ error }}
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Client Information Section -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Client Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Client Name</label>
                                <input 
                                    v-model="form.client_name" 
                                    type="text" 
                                    class="mt-1 block w-full rounded-md border-gray-300"
                                    :class="{ 'border-red-500': errors.client_name }"
                                >
                                <p v-if="errors.client_name" class="mt-1 text-sm text-red-600">
                                    {{ errors.client_name[0] }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                                <input v-model="form.company_name" type="text" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                                <input v-model="form.contact_person" type="text" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input v-model="form.phone" type="tel" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>
                    </div>

                    <!-- Address Fields -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Street Address</label>
                            <input v-model="form.street_address" type="text" class="mt-1 block w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">State/Province</label>
                            <select 
                                v-model="form.state" 
                                class="mt-1 block w-full rounded-md border-gray-300"
                                :class="{ 'border-red-500': errors.state }"
                            >
                                <option v-for="state in states" :key="state" :value="state">
                                    {{ state }}
                                </option>
                            </select>
                            <p v-if="errors.state" class="mt-1 text-sm text-red-600">
                                {{ errors.state[0] }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Zip/Postal Code</label>
                            <input v-model="form.zip_code" type="text" class="mt-1 block w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Country</label>
                            <select 
                                v-model="form.country" 
                                class="mt-1 block w-full rounded-md border-gray-300"
                                :class="{ 'border-red-500': errors.country }"
                            >
                                <option v-for="country in countries" :key="country" :value="country">
                                    {{ country }}
                                </option>
                            </select>
                            <p v-if="errors.country" class="mt-1 text-sm text-red-600">
                                {{ errors.country[0] }}
                            </p>
                        </div>
                    </div>

                    <!-- Project Details Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Project Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Project Title</label>
                                <input v-model="form.project_title" type="text" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Project Description</label>
                                <textarea v-model="form.project_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input 
                                    v-model="form.start_date" 
                                    type="date" 
                                    class="mt-1 block w-full rounded-md border-gray-300"
                                    :class="{ 'border-red-500': errors.start_date }"
                                >
                                <p v-if="errors.start_date" class="mt-1 text-sm text-red-600">
                                    {{ errors.start_date[0] }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input 
                                    v-model="form.end_date" 
                                    type="date" 
                                    :min="form.start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300"
                                    :class="{ 'border-red-500': errors.end_date }"
                                >
                                <p v-if="errors.end_date" class="mt-1 text-sm text-red-600">
                                    {{ errors.end_date[0] }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority Level</label>
                                <select v-model="form.priority_level" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Project Type</label>
                                <select v-model="form.project_type" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="consulting">Consulting</option>
                                    <option value="design">Design</option>
                                    <option value="development">Development</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Project Scope <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    v-model="form.project_scope" 
                                    rows="4" 
                                    class="mt-1 block w-full rounded-md border-gray-300"
                                    :class="{ 'border-red-500': errors.project_scope }"
                                ></textarea>
                                <p v-if="errors.project_scope" class="mt-1 text-sm text-red-600">
                                    {{ errors.project_scope[0] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hourly Rate</label>
                                <input v-model="form.hourly_rate" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fixed Price</label>
                                <input v-model="form.fixed_price" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estimated Hours</label>
                                <input v-model="form.estimated_hours" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>

                        <!-- Cost Breakdown -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cost Breakdown</label>
                            <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-2">
                                    <input v-model="item.description" placeholder="Item Description" type="text" class="block w-full rounded-md border-gray-300">
                                </div>
                                <div>
                                    <input v-model="item.quantity" placeholder="Quantity" type="number" min="1" class="block w-full rounded-md border-gray-300" @input="calculateItemTotal(index)">
                                </div>
                                <div>
                                    <input v-model="item.unit_price" placeholder="Unit Price" type="number" min="0" step="0.01" class="block w-full rounded-md border-gray-300" @input="calculateItemTotal(index)">
                                </div>
                                <div class="md:col-span-3">
                                    <input :value="calculateItemTotal(index)" readonly placeholder="Total" type="number" class="block w-full rounded-md border-gray-300 bg-gray-50">
                                </div>
                                <div>
                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <button type="button" @click="addItem" class="mt-2 text-blue-600 hover:text-blue-800">
                                + Add Item
                            </button>
                        </div>
                    </div>

                    <!-- Payment Terms Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Terms</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select v-model="form.payment_method" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="credit_card">Credit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment Schedule</label>
                                <select v-model="form.payment_schedule" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="upon_receipt">Upon Receipt</option>
                                    <option value="net_15">Net 15</option>
                                    <option value="net_30">Net 30</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input v-model="form.payment_due_date" type="date" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Terms Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Terms</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cancellation Policy</label>
                                <textarea v-model="form.cancellation_policy" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Revision Policy</label>
                                <textarea v-model="form.revision_policy" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Custom Message to Client</label>
                                <textarea v-model="form.custom_message" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                            </div>
                            <div class="flex items-center">
                                <input v-model="form.terms_agreed" type="checkbox" class="rounded border-gray-300 text-blue-600">
                                <label class="ml-2 text-sm text-gray-700">I agree to the terms and conditions</label>
                            </div>
                        </div>
                    </div>

                    <!-- Branding Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Branding</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Logo</label>
                                <div class="mt-1 flex items-center space-x-4">
                                    <input 
                                        type="file" 
                                        @change="handleLogoUpload" 
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100"
                                        :class="{ 'border-red-500': errors.logo }"
                                    >
                                    
                                    <!-- Logo Preview -->
                                    <div v-if="logoPreviewUrl" class="relative w-20 h-20">
                                        <img 
                                            :src="logoPreviewUrl" 
                                            class="object-contain w-full h-full"
                                            alt="Logo preview"
                                        >
                                        <button 
                                            type="button"
                                            @click="() => { form.logo = null; logoPreviewUrl = null; }"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                        >
                                            <span class="sr-only">Remove logo</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p v-if="errors.logo" class="mt-1 text-sm text-red-600">
                                    {{ errors.logo[0] }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Color Scheme</label>
                                <input v-model="form.color_scheme" type="color" class="mt-1 block w-full h-10">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="resetForm" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Reset
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                            :disabled="loading"
                        >
                            <span v-if="loading">Processing...</span>
                            <span v-else>Generate Document</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-section {
    @apply mb-8;
}

.form-section-title {
    @apply text-lg font-medium text-gray-900 mb-4;
}

.input-group {
    @apply mb-4;
}

.input-label {
    @apply block text-sm font-medium text-gray-700 mb-1;
}

.text-input {
    @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500;
}
</style>
