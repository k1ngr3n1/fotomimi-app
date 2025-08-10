<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Settings, Save } from 'lucide-vue-next';

const form = {
    site_name: 'Fotomimi Photography',
    site_description: 'Professional photography services',
    contact_email: 'contact@fotomimi.com',
    contact_phone: '+1 (555) 123-4567',
    social_facebook: '',
    social_instagram: '',
    social_twitter: ''
};
</script>

<template>
    <Head title="Settings - Admin">
        <meta name="description" content="Admin settings for photography website." />
    </Head>

    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
                <p class="mt-2 text-gray-600">
                    Configure your photography website settings and preferences.
                </p>
            </div>

            <!-- Settings Form -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">General Settings</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Site Information -->
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-4">Site Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Site Name
                                </label>
                                <input
                                    v-model="form.site_name"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Contact Email
                                </label>
                                <input
                                    v-model="form.contact_email"
                                    type="email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                                />
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Site Description
                            </label>
                            <textarea
                                v-model="form.site_description"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none bg-white text-gray-900"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-4">Contact Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone Number
                                </label>
                                <input
                                    v-model="form.contact_phone"
                                    type="tel"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-4">Social Media</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Facebook
                                </label>
                                <input
                                    v-model="form.social_facebook"
                                    type="url"
                                    placeholder="https://facebook.com/yourpage"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Instagram
                                </label>
                                <input
                                    v-model="form.social_instagram"
                                    type="url"
                                    placeholder="https://instagram.com/yourprofile"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Twitter
                                </label>
                                <input
                                    v-model="form.social_twitter"
                                    type="url"
                                    placeholder="https://twitter.com/yourprofile"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                        >
                            <Save class="w-4 h-4 mr-2" />
                            Save Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- Coming Soon -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                    <Settings class="w-6 h-6 text-blue-600 mr-3" />
                    <div>
                        <h3 class="text-lg font-medium text-blue-900">More Settings Coming Soon</h3>
                        <p class="text-blue-700 mt-1">
                            Additional configuration options including gallery settings, booking preferences, and more will be available soon.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template> 