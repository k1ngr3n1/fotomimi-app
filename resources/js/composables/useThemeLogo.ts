import { ref, computed, onMounted } from 'vue';

export function useThemeLogo() {
    const isDark = ref(false);

    // Computed property to get the appropriate logo based on theme
    const logoSrc = computed(() => {
        return isDark.value ? '/output-onlinepngtools.png' : '/mimi-logo.png';
    });

    // Function to update theme state
    const updateThemeState = () => {
        if (typeof window !== 'undefined') {
            isDark.value = document.documentElement.classList.contains('dark');
        }
    };

    onMounted(() => {
        // Initialize theme state
        updateThemeState();
        
        // Set up a MutationObserver to watch for theme changes
        if (typeof window !== 'undefined') {
            const observer = new MutationObserver(() => {
                updateThemeState();
            });
            
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
    });

    return {
        isDark,
        logoSrc,
        updateThemeState
    };
}
