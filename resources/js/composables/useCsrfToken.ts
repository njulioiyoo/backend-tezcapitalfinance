import { usePage } from '@inertiajs/vue3';

/**
 * Composable to get CSRF token with multiple fallback methods and refresh capability
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

    /**
     * Refresh CSRF token by making a request to get a new one
     */
    const refreshCsrfToken = async (): Promise<string> => {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'include',
            });
            
            if (response.ok) {
                const data = await response.json();
                const newToken = data.csrf_token;
                
                // Update meta tag if it exists
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', newToken);
                }
                
                return newToken;
            }
        } catch (error) {
            console.warn('Failed to refresh CSRF token:', error);
        }
        
        return getCsrfToken();
    };

    return {
        getCsrfToken,
        refreshCsrfToken
    };
}