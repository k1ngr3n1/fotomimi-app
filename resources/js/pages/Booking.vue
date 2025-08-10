<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Calendar, MapPin, Users, DollarSign, Send } from 'lucide-vue-next';
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
    event_type: '',
    event_date: '',
    location: '',
    guests: '',
    budget: '',
    message: ''
});

const isSubmitting = ref(false);

const submitForm = () => {
    isSubmitting.value = true;
    form.post(route('booking.send'), {
        onSuccess: () => {
            form.reset();
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const eventTypes = [
    { value: 'wedding', labelKey: 'booking.eventTypes.wedding', description: 'Complete wedding coverage' },
    { value: 'baptism', labelKey: 'booking.eventTypes.baptism', description: 'Religious ceremony coverage' },
    { value: 'concert', labelKey: 'booking.eventTypes.concert', description: 'Live performance photography' },
    { value: 'studio', labelKey: 'booking.eventTypes.studio', description: 'Professional portraits' },
    { value: 'modelling', labelKey: 'booking.eventTypes.modelling', description: 'Fashion and commercial' },
    { value: 'other', labelKey: 'booking.eventTypes.other', description: 'Custom photography needs' }
];

const budgetRanges = [
    { value: 'under-500', labelKey: 'booking.budgetRanges.under500' },
    { value: '500-1000', labelKey: 'booking.budgetRanges.500to1000' },
    { value: '1000-2000', labelKey: 'booking.budgetRanges.1000to2000' },
    { value: '2000-5000', labelKey: 'booking.budgetRanges.2000to5000' },
    { value: 'over-5000', labelKey: 'booking.budgetRanges.over5000' },
    { value: 'custom', labelKey: 'booking.budgetRanges.custom' }
];

const packages = [
    {
        titleKey: 'booking.packages.basic.title',
        price: '$500',
        descriptionKey: 'booking.packages.basic.description',
        features: [
            '2 hours of coverage',
            '50 edited photos',
            'Online gallery',
            'Basic editing',
            'Delivery within 2 weeks'
        ]
    },
    {
        titleKey: 'booking.packages.standard.title',
        price: '$1,200',
        descriptionKey: 'booking.packages.standard.description',
        features: [
            '4 hours of coverage',
            '100 edited photos',
            'Online gallery',
            'Advanced editing',
            'Delivery within 1 week',
            'Print release'
        ]
    },
    {
        titleKey: 'booking.packages.premium.title',
        price: '$2,500',
        descriptionKey: 'booking.packages.premium.description',
        features: [
            'Full day coverage',
            '200+ edited photos',
            'Online gallery',
            'Premium editing',
            'Priority delivery',
            'Print release',
            'Engagement session included'
        ]
    }
];
</script>

<template>
    <Head :title="t('booking.title')">
        <meta name="description" :content="t('booking.subtitle')" />
    </Head>
    <div class="min-h-screen bg-white dark:bg-black transition-colors duration-300">
        <Navigation />
        <section class="py-20 px-4 bg-black dark:bg-white transition-colors duration-300">
            <div class="max-w-4xl mx-auto text-center text-white dark:text-black">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">{{ t('booking.title') }}</h1>
                <p class="text-xl text-red-600 dark:text-red-500 max-w-2xl mx-auto">
                    {{ t('booking.subtitle') }}
                </p>
            </div>
        </section>
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-2xl mx-auto">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.name') }}</label>
                        <input 
                            v-model="form.name"
                            type="text" 
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                            required
                        />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.email') }}</label>
                        <input 
                            v-model="form.email"
                            type="email" 
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                            required
                        />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.phone') }}</label>
                        <input 
                            v-model="form.phone"
                            type="tel" 
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                            required
                        />
                        <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.eventType') }}</label>
                        <select 
                            v-model="form.event_type"
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600"
                            required
                        >
                            <option value="">{{ t('booking.form.eventType') }}</option>
                            <option v-for="eventType in eventTypes" :key="eventType.value" :value="eventType.value">
                                {{ t(eventType.labelKey) }}
                            </option>
                        </select>
                        <div v-if="form.errors.event_type" class="text-red-600 text-sm mt-1">{{ form.errors.event_type }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.eventDate') }}</label>
                        <input 
                            v-model="form.event_date"
                            type="date" 
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                            required
                        />
                        <div v-if="form.errors.event_date" class="text-red-600 text-sm mt-1">{{ form.errors.event_date }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.location') }}</label>
                        <input 
                            v-model="form.location"
                            type="text" 
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                            required
                        />
                        <div v-if="form.errors.location" class="text-red-600 text-sm mt-1">{{ form.errors.location }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.guests') }}</label>
                        <input 
                            v-model="form.guests"
                            type="number" 
                            min="1"
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600" 
                        />
                        <div v-if="form.errors.guests" class="text-red-600 text-sm mt-1">{{ form.errors.guests }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.budget') }}</label>
                        <select 
                            v-model="form.budget"
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600"
                        >
                            <option value="">{{ t('booking.form.budget') }}</option>
                            <option v-for="budget in budgetRanges" :key="budget.value" :value="budget.value">
                                {{ t(budget.labelKey) }}
                            </option>
                        </select>
                        <div v-if="form.errors.budget" class="text-red-600 text-sm mt-1">{{ form.errors.budget }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-black dark:text-white font-semibold mb-2">{{ t('booking.form.message') }}</label>
                        <textarea 
                            v-model="form.message"
                            rows="5" 
                            class="w-full px-4 py-3 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black text-black dark:text-white focus:ring-2 focus:ring-red-600"
                            placeholder="Tell us more about your event and any special requirements..."
                        ></textarea>
                        <div v-if="form.errors.message" class="text-red-600 text-sm mt-1">{{ form.errors.message }}</div>
                    </div>
                    
                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed btn-camera"
                    >
                        <span v-if="isSubmitting">Sending...</span>
                        <span v-else>{{ t('booking.form.submitButton') }}</span>
                    </button>
                </form>
            </div>
        </section>
        <footer class="bg-white dark:bg-black text-black dark:text-white py-12 px-4 transition-colors duration-300">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-black/70 dark:text-white/70">&copy; 2024 Fotomimi. {{ t('booking.footer.copyright') }}</p>
            </div>
        </footer>
    </div>
</template> 