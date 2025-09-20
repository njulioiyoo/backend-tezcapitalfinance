import { usePage } from '@inertiajs/vue3';

interface AuthData {
  user: any;
  permissions: string[];
  roles: string[];
}

export function usePermissions() {
  const page = usePage();
  const auth = page.props.auth as AuthData;

  /**
   * Check if user has a specific permission
   */
  const hasPermission = (permission: string): boolean => {
    if (!auth.user) return false;
    
    // Super admin has all permissions
    if (auth.roles.includes('super-admin')) return true;
    
    return auth.permissions.includes(permission);
  };

  /**
   * Check if user has any of the specified permissions
   */
  const hasAnyPermission = (permissions: string[]): boolean => {
    if (!auth.user) return false;
    
    // Super admin has all permissions
    if (auth.roles.includes('super-admin')) return true;
    
    return permissions.some(permission => auth.permissions.includes(permission));
  };

  /**
   * Check if user has all of the specified permissions
   */
  const hasAllPermissions = (permissions: string[]): boolean => {
    if (!auth.user) return false;
    
    // Super admin has all permissions
    if (auth.roles.includes('super-admin')) return true;
    
    return permissions.every(permission => auth.permissions.includes(permission));
  };

  /**
   * Check if user has a specific role
   */
  const hasRole = (role: string): boolean => {
    if (!auth.user) return false;
    
    return auth.roles.includes(role);
  };

  /**
   * Check if user has any of the specified roles
   */
  const hasAnyRole = (roles: string[]): boolean => {
    if (!auth.user) return false;
    
    return roles.some(role => auth.roles.includes(role));
  };

  /**
   * Check if user can access a menu route
   */
  const canAccessRoute = (href: string): boolean => {
    if (!auth.user || !href) return false;
    
    // Super admin can access everything
    if (auth.roles.includes('super-admin')) return true;
    
    // Define route to permission mapping
    const routePermissions: Record<string, string> = {
      // System routes
      '/system/users': 'users.view',
      '/system/roles': 'roles.view',
      '/system/permissions': 'permissions.view',
      '/system/configurations': 'configurations.view',
      '/system/menu': 'menu.view',
      '/system/audit-log': 'audit-log.view',
      '/system/news-events': 'news-events.view',
      
      // Content routes
      '/content/about': 'about.view',
      '/content/news-events': 'news-events.view',
      '/content/partners': 'partners.view',
      '/content/services': 'services.view',
      
      // Legacy routes
      '/users': 'users.view',
      '/roles': 'roles.view',
      '/configurations': 'configurations.view',
      '/menu-manager': 'menu.view',
      '/audit-log': 'audit-log.view',
      '/content': 'content.view',
    };
    
    const requiredPermission = routePermissions[href];
    
    if (!requiredPermission) {
      // If no specific permission defined, allow access
      // (for dashboard, settings, etc.)
      return true;
    }
    
    return hasPermission(requiredPermission);
  };

  return {
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    hasRole,
    hasAnyRole,
    canAccessRoute,
    permissions: auth.permissions,
    roles: auth.roles,
    user: auth.user,
  };
}