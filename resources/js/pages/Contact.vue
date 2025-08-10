<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Mail, Phone, MapPin, Clock, Send } from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';

const { t, initLanguage } = useTranslation();

onMounted(() => {
    initLanguage();
});

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: ''
});

const isSubmitting = ref(false);

const submitForm = () => {
    isSubmitting.value = true;
    form.post(route('contact.send'), {
        onSuccess: () => {
            form.reset();
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const contactInfo = [
    {
        icon: Mail,
        title: 'Email',
        value: 'k1ngr3n1@gmail.com',
        link: 'mailto:k1ngr3n1@gmail.com'
    },
    {
        icon: Phone,
        title: 'Phone',
        value: '+1 (555) 123-4567',
        link: 'tel:+15551234567'
    },
    {
        icon: MapPin,
        title: 'Studio Location',
        value: '123 Photography Street, City, State 12345',
        link: 'https://maps.google.com'
    },
    {
        icon: Clock,
        title: 'Business Hours',
        value: 'Mon-Fri: 9AM-6PM, Sat: 10AM-4PM',
        link: null
    }
];
</script>

<template>
    <Head title="Contact">
        <meta name="description" content="Contact Fotomimi for professional photography services. Weddings, baptisms, concerts, studio sessions, and more." />
    </Head>
    <div class="min-h-screen bg-white dark:bg-black transition-colors duration-300">
        <Navigation />
        <section class="py-20 px-4 bg-black dark:bg-white transition-colors duration-300">
            <div class="max-w-4xl mx-auto text-center text-white dark:text-black">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Contact</h1>
                <p class="text-xl text-red-600 dark:text-red-500 max-w-2xl mx-auto">
                    Let's connect and make your vision a reality.
                </p>
            </div>
        </section>
        
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white mb-6">Send us a message</h2>
                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">Name</label>
                                <input 
                                    v-model="form.name"
                                    type="text" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                                    required
                                />
                                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">Email</label>
                                <input 
                                    v-model="form.email"
                                    type="email" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                                    required
                                />
                                <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                            </div>
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">Phone (optional)</label>
                                <input 
                                    v-model="form.phone"
                                    type="tel" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                                />
                                <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
                            </div>
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">Message</label>
                                <textarea 
                                    v-model="form.message"
                                    rows="5" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600"
                                    placeholder="Tell us about your photography needs..."
                                    required
                                ></textarea>
                                <div v-if="form.errors.message" class="text-red-600 text-sm mt-1">{{ form.errors.message }}</div>
                            </div>
                            <button 
                                type="submit" 
                                :disabled="isSubmitting"
                                class="bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 btn-camera"
                            >
                                <Send v-if="!isSubmitting" class="w-4 h-4" />
                                <span v-if="isSubmitting">Sending...</span>
                                <span v-else>Send Message</span>
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white mb-6">Get in touch</h2>
                        <div class="space-y-6">
                            <div 
                                v-for="info in contactInfo" 
                                :key="info.title"
                                class="flex items-start gap-4"
                            >
                                <div class="w-12 h-12 bg-red-600 text-white rounded-lg flex items-center justify-center flex-shrink-0">
                                    <component :is="info.icon" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-black dark:text-white">{{ info.title }}</h3>
                                    <a 
                                        v-if="info.link" 
                                        :href="info.link" 
                                        class="text-red-600 dark:text-red-500 hover:underline"
                                    >
                                        {{ info.value }}
                                    </a>
                                    <p v-else class="text-black/70 dark:text-white/70">{{ info.value }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <h3 class="font-semibold text-black dark:text-white mb-3">What to expect</h3>
                            <ul class="space-y-2 text-sm text-black/70 dark:text-white/70">
                                <li>• We'll respond within 24 hours</li>
                                <li>• Free consultation for your project</li>
                                <li>• Custom quotes based on your needs</li>
                                <li>• Flexible scheduling options</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <footer class="bg-white dark:bg-black text-black dark:text-white py-12 px-4 transition-colors duration-300">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-black/70 dark:text-white/70">&copy; 2024 Fotomimi. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template> 