import { usePage } from '@inertiajs/vue3';

/**
 * Composable to get CSRF token with multiple fallback methods
 */
export function useCsrfToken() {
    const getCsrfToken = (): string => {
        try {
            // First try to get from Inertia shared props (most reliable after login)
            const page = usePage();
            const tokenFromProps = page.props.csrf_token as string;
            if (tokenFromProps) {
                return tokenFromProps;
            }
        } catch (e) {
            // usePage might not be available in some contexts
        }
        
        // Fallback to meta tag
        const tokenFromMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (tokenFromMeta) {
            return tokenFromMeta;
        }
        
        // Fallback to cookie
        const tokenFromCookie = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
        
        if (tokenFromCookie) {
            try {
                return decodeURIComponent(tokenFromCookie);
            } catch (e) {
                console.warn('Failed to decode CSRF token from cookie:', e);
            }
        }
        
        console.warn('CSRF token not found. Make sure meta tag, shared props, or cookie is available.');
        return '';
    };

    return {
        getCsrfToken
    };
}