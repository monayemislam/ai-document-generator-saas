<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.client.name,
    email: props.client.email,
    phone: props.client.phone,
    company_name: props.client.company_name,
    address: props.client.address,
    city: props.client.city,
    state: props.client.state,
    postal_code: props.client.postal_code,
    country: props.client.country,
});

const submit = () => {
    form.put(route('clients.update', props.client.id));
};
</script>

<template>
    <Head title="Edit Client" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="phone" value="Phone" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="company_name" value="Company Name" />
                                <TextInput
                                    id="company_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.company_name"
                                />
                                <InputError :message="form.errors.company_name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="address" value="Address" />
                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.address"
                                />
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="city" value="City" />
                                    <TextInput
                                        id="city"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.city"
                                    />
                                    <InputError :message="form.errors.city" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="state" value="State" />
                                    <TextInput
                                        id="state"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.state"
                                    />
                                    <InputError :message="form.errors.state" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="postal_code" value="Postal Code" />
                                    <TextInput
                                        id="postal_code"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.postal_code"
                                    />
                                    <InputError :message="form.errors.postal_code" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="country" value="Country" />
                                    <TextInput
                                        id="country"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.country"
                                    />
                                    <InputError :message="form.errors.country" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Client
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
