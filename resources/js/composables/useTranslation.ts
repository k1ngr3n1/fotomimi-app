import { ref, computed } from 'vue';

// Import language files
import en from '../../lang/en.json';
import hr from '../../lang/hr.json';

type LanguageData = typeof en;

const languages: Record<string, LanguageData> = {
    en,
    hr
};

const currentLanguage = ref('en');

export function useTranslation() {
    const t = computed(() => {
        return (key: string, params?: Record<string, string>) => {
            const keys = key.split('.');
            let value: any = languages[currentLanguage.value];
            
            for (const k of keys) {
                if (value && typeof value === 'object' && k in value) {
                    value = value[k];
                } else {
                    return key; // Return the key if translation not found
                }
            }
            
            if (typeof value !== 'string') {
                return key;
            }
            
            // Replace parameters if provided
            if (params) {
                return Object.entries(params).reduce((str, [param, replacement]) => {
                    return str.replace(new RegExp(`:${param}`, 'g'), replacement);
                }, value);
            }
            
            return value;
        };
    });

    const setLanguage = (lang: string) => {
        if (lang in languages) {
            currentLanguage.value = lang;
            localStorage.setItem('language', lang);
        }
    };

    const getCurrentLanguage = () => currentLanguage.value;

    const getAvailableLanguages = () => [
        { code: 'en', name: 'English', flag: '🇺🇸' },
        { code: 'hr', name: 'Hrvatski', flag: '🇭🇷' }
    ];

    // Initialize language from localStorage or default to 'en'
    const initLanguage = () => {
        const savedLanguage = localStorage.getItem('language');
        if (savedLanguage && savedLanguage in languages) {
            currentLanguage.value = savedLanguage;
        }
    };

    return {
        t,
        setLanguage,
        getCurrentLanguage,
        getAvailableLanguages,
        initLanguage
    };
} 