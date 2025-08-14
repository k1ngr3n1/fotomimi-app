<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import Navigation from '@/components/Navigation.vue';
import SuccessPopup from '@/components/SuccessPopup.vue';
import { Mail, Phone, MapPin, Clock, Send, Facebook, Instagram } from 'lucide-vue-next';
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
const showSuccessPopup = ref(false);

const submitForm = () => {
    isSubmitting.value = true;
    form.post(route('contact.send'), {
        onSuccess: () => {
            form.reset();
            isSubmitting.value = false;
            showSuccessPopup.value = true;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const closeSuccessPopup = () => {
    showSuccessPopup.value = false;
};

const contactInfo = [
    {
        icon: Mail,
        titleKey: 'contact.info.email',
        value: 'fotomimi@gmail.com',
        link: 'mailto:fotomimi@gmail.com'
    },
    {
        icon: Phone,
        titleKey: 'contact.info.phone',
        value: '+385 912397004',
        link: 'tel:+385912397004'
    },
    {
        icon: MapPin,
        titleKey: 'contact.info.studio',
        value: 'Bjelovarska 34, Križevci, 48260',
        link: 'https://maps.app.goo.gl/tXskLj7N42Fn2XNJ8'
    },
    {
        icon: Clock,
        titleKey: 'contact.info.hours',
        valueKey: 'contact.info.hoursValue',
        link: null
    }
];
</script>

<template>
    <Head :title="t('contact.title')">
        <meta name="description" content="Contact Fotomimi for professional photography services. Weddings, baptisms, concerts, studio sessions, and more." />
    </Head>
    <div class="min-h-screen bg-white dark:bg-black transition-colors duration-300">
        <Navigation />
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-4xl mx-auto text-center text-black dark:text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">{{ t('contact.title') }}</h1>
                <p class="text-xl text-red-600 dark:text-red-500 max-w-2xl mx-auto">
                    {{ t('contact.subtitle') }}
                </p>
            </div>
        </section>
        
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white mb-6">{{ t('contact.form.title') }}</h2>
                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">{{ t('contact.form.name') }}</label>
                                <input 
                                    v-model="form.name"
                                    type="text" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                                    required
                                />
                                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">{{ t('contact.form.email') }}</label>
                                <input 
                                    v-model="form.email"
                                    type="email" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                                    required
                                />
                                <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                            </div>
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">{{ t('contact.form.phone') }}</label>
                                <input 
                                    v-model="form.phone"
                                    type="tel" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                                />
                                <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
                            </div>
                            <div>
                                <label class="block text-black dark:text-white font-semibold mb-2">{{ t('contact.form.message') }}</label>
                                <textarea 
                                    v-model="form.message"
                                    rows="5" 
                                    class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600"
                                    :placeholder="t('contact.form.placeholder')"
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
                                <span v-if="isSubmitting">{{ t('contact.form.sending') }}</span>
                                <span v-else>{{ t('contact.form.sendButton') }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white mb-6">{{ t('contact.info.title') }}</h2>
                        <div class="space-y-6">
                            <div 
                                v-for="info in contactInfo" 
                                :key="info.titleKey"
                                class="flex items-start gap-4"
                            >
                                <div class="w-12 h-12 bg-red-600 text-white rounded-lg flex items-center justify-center flex-shrink-0">
                                    <component :is="info.icon" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-black dark:text-white">{{ t(info.titleKey) }}</h3>
                                    <a 
                                        v-if="info.link" 
                                        :href="info.link" 
                                        class="text-red-600 dark:text-red-500 hover:underline"
                                    >
                                        {{ info.valueKey ? t(info.valueKey) : info.value }}
                                    </a>
                                    <p v-else class="text-black/70 dark:text-white/70">{{ info.valueKey ? t(info.valueKey) : info.value }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <h3 class="font-semibold text-black dark:text-white mb-3">{{ t('contact.expectations.title') }}</h3>
                            <ul class="space-y-2 text-sm text-black/70 dark:text-white/70">
                                <li>{{ t('contact.expectations.response') }}</li>
                                <li>{{ t('contact.expectations.consultation') }}</li>
                                <li>{{ t('contact.expectations.quotes') }}</li>
                                <li>{{ t('contact.expectations.scheduling') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                 <!-- Social Media Links -->
                 <div class="flex justify-center">

                        <div class="mt-6">
                             <h2 class="text-2xl font-bold text-black dark:text-white mb-6">{{ t('contact.info.followUs') }}</h2>
                            <div class="flex justify-center gap-4">
                                <a 
                                    href="https://www.facebook.com/Fotomimi.hr/"
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="w-12 h-12 bg-red-600 text-white rounded-lg flex items-center justify-center hover:bg-black hover:text-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-300"
                                >
                                    <Facebook class="w-6 h-6" />
                                </a>
                                <a 
                                    href="https://www.instagram.com/fotomimiemotion" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="w-12 h-12 bg-red-600 text-white rounded-lg flex items-center justify-center hover:bg-black hover:text-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-300"
                                >
                                    <Instagram class="w-6 h-6" />
                                </a>
                            </div>
                        </div>
                        </div>
            </div>
        </section>
        
        <footer class="bg-white dark:bg-black text-black dark:text-white py-12 px-4 transition-colors duration-300">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-black/70 dark:text-white/70">&copy; 2025 Fotomimi. {{ t('home.footer.copyright') }}</p>
            </div>
        </footer>
        
        <!-- Success Popup -->
        <SuccessPopup
            :show="showSuccessPopup"
            :title="t('contact.success.title')"
            :message="t('contact.success.message')"
            @close="closeSuccessPopup"
        />
    </div>
</template> 