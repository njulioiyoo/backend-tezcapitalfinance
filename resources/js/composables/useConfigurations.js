import { ref, nextTick } from 'vue';
import { toast } from '@/components/ui/toast';

export function useConfigurations() {
    const configurations = ref({});
    const isLoading = ref(false);
    const isSaving = ref(false);

    // Helper function to get CSRF token
    const getCsrfToken = () => {
        // Try meta tag first (most reliable for Laravel)
        const tokenFromMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (tokenFromMeta) {
            return tokenFromMeta;
        }
        
        // Fallback to cookie (decode properly)
        const tokenFromCookie = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
        
        if (tokenFromCookie) {
            try {
                return decodeURIComponent(tokenFromCookie);
            } catch (e) {
                // Failed to decode CSRF token from cookie
            }
        }
        
        // No CSRF token found
        return '';
    };

    // Helper function to refresh CSRF token from server
    const refreshCsrfToken = async () => {
        try {
            const response = await fetch('/api/csrf-token', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.csrf_token) {
                    // Update meta tag
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', data.csrf_token);
                    }
                    return data.csrf_token;
                }
            }
        } catch (e) {
            // Failed to refresh CSRF token
        }
        return null;
    };

    const loadConfigurations = async () => {
        isLoading.value = true;
        try {
            const csrfToken = getCsrfToken();
            if (!csrfToken) {
                // CSRF token not found for loading configurations
            }

            const response = await fetch('/api/system/configurations', {
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            configurations.value = data.data;
        } catch (error) {
            // Error loading configurations
            toast({
                title: 'Error',
                description: 'Failed to load configurations',
                variant: 'error',
            });
        } finally {
            isLoading.value = false;
        }
    };

    const saveBulkNonFileConfigurations = async (group, changes) => {
        const payload = {
            configurations: changes.map(change => ({
                key: change.key,
                value: typeof change.value === 'object' ? JSON.stringify(change.value) : String(change.value),
                type: change.type,
                group: group,
                description: '',
                is_public: true
            }))
        };

        // Saving bulk configurations

        let csrfToken = getCsrfToken();
        if (!csrfToken) {
            // No CSRF token found, attempting to refresh
            csrfToken = await refreshCsrfToken();
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page and try again.');
            }
        }
        
        // Using CSRF token for bulk update

        const makeRequest = async (token) => {
            const response = await fetch(`/api/system/configurations/bulk-update`, {
                method: 'POST',
                body: JSON.stringify(payload),
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Unknown error' }));
                // API Response Error
                
                if (response.status === 419 || (errorData.message && errorData.message.includes('CSRF'))) {
                    throw new Error('CSRF_TOKEN_MISMATCH');
                }
                
                throw new Error(errorData.message || 'Failed to save configurations');
            }

            return await response.json();
        };

        try {
            return await makeRequest(csrfToken);
        } catch (error) {
            if (error.message === 'CSRF_TOKEN_MISMATCH') {
                // CSRF token mismatch, refreshing token and retrying
                const newToken = await refreshCsrfToken();
                if (newToken) {
                    return await makeRequest(newToken);
                }
                throw new Error('CSRF token mismatch. Please refresh the page and try again.');
            }
            throw error;
        }
    };

    const saveBulkConfigurations = async (group, changes) => {
        isSaving.value = true;
        try {
            // Check if any file uploads are involved
            const hasFileUploads = changes.some(change => 
                change.type === 'file' && change.value instanceof File
            );

            if (hasFileUploads) {
                // Use the same bulk update endpoint that handles both files and other data
                const formData = new FormData();
                
                // Separate files from other configurations
                const configurationsArray = [];
                const fileFields = [];
                
                changes.forEach((change, index) => {
                    if (change.type === 'file' && change.value instanceof File) {
                        // Add file to FormData with unique field name
                        const fieldName = `file_${index}`;
                        formData.append(fieldName, change.value);
                        fileFields.push({
                            fieldName,
                            key: change.key,
                            type: change.type,
                            group: group,
                            description: change.description || '',
                            is_public: change.is_public !== undefined ? change.is_public : true,
                            metadata: change.metadata
                        });
                        
                        console.log('📁 Adding file to FormData:', {
                            fieldName,
                            fileName: change.value.name,
                            fileSize: change.value.size,
                            key: change.key,
                            metadata: change.metadata
                        });
                    } else {
                        // Regular configuration (non-file)
                        configurationsArray.push({
                            key: change.key,
                            value: change.value,
                            type: change.type,
                            group: group,
                            description: change.description || '',
                            is_public: change.is_public !== undefined ? change.is_public : true
                        });
                    }
                });
                
                // Add configurations as JSON
                formData.append('configurations', JSON.stringify(configurationsArray));
                
                // Add file metadata as JSON
                formData.append('file_fields', JSON.stringify(fileFields));
                
                console.log('🚀 Sending configurations:', configurationsArray);
                console.log('📁 Sending file fields:', fileFields);
                
                const csrfToken = getCsrfToken();
                if (!csrfToken) {
                    throw new Error('CSRF token not found. Please refresh the page and try again.');
                }

                const response = await fetch(`/api/system/configurations/bulk-update`, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('❌ Bulk upload error:', errorData);
                    
                    if (response.status === 419 || (errorData.message && errorData.message.includes('CSRF'))) {
                        throw new Error('Session expired or CSRF token mismatch. Please refresh the page and try again.');
                    }
                    
                    throw new Error(errorData.message || 'Failed to save configurations');
                }
                
                const result = await response.json();
                console.log('✅ Bulk upload success:', result);
                
                // Reload configurations to get updated data including file paths
                await loadConfigurations();
            } else {
                // All non-file data, use bulk update
                const result = await saveBulkNonFileConfigurations(group, changes);
                
                // Update local state with server response data for accuracy
                if (result && result.data) {
                    // Updating local state with server response
                    for (const config of result.data) {
                        if (!configurations.value[config.group]) {
                            configurations.value[config.group] = {};
                        }
                        
                        // Updating configuration
                        
                        // Force Vue reactivity by creating new object reference
                        configurations.value[config.group] = {
                            ...configurations.value[config.group],
                            [config.key]: {
                                value: config.value,
                                type: config.type,
                                description: config.description,
                                is_public: config.is_public
                            }
                        };
                    }
                    
                    // Local state updated
                    
                    // Force Vue to update DOM
                    await nextTick();
                } else {
                    // Fallback: reload all configurations if no response data
                    // No response data, reloading configurations
                    await loadConfigurations();
                }
            }
            
            toast({
                title: 'Success',
                description: `${changes.length} configuration${changes.length > 1 ? 's' : ''} saved successfully`,
                variant: 'success',
            });
        } catch (error) {
            // Error saving configurations
            
            if (error.message && error.message.includes('CSRF token mismatch')) {
                toast({
                    title: 'Session Expired',
                    description: 'Your session has expired. The page will reload automatically.',
                    variant: 'error',
                });
                
                // Reload page after short delay to show toast
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                toast({
                    title: 'Error',
                    description: error.message || 'Failed to save configurations',
                    variant: 'error',
                });
            }
        } finally {
            isSaving.value = false;
        }
    };

    const saveOrUpdateConfiguration = async (group, key, value, type = 'string') => {
        return saveBulkConfigurations(group, [{ key, value, type }]);
    };

    // Alias for backward compatibility
    const saveConfiguration = saveOrUpdateConfiguration;
    const updateConfiguration = (configId, group, key, value, type = 'string') => {
        return saveOrUpdateConfiguration(group, key, value, type);
    };

    return {
        configurations,
        isLoading,
        isSaving,
        loadConfigurations,
        saveBulkConfigurations,
        saveConfiguration,
        updateConfiguration
    };
}