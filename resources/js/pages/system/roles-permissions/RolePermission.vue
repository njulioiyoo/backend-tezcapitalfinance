<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import { toast } from '@/components/ui/toast';
import { Plus, Edit, Trash2, Shield, Key, Search } from 'lucide-vue-next';

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

interface Props {
    roles: Role[];
    permissions: Permission[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'System Management', href: '/system' },
    { title: 'Roles & Permissions', href: '/roles' },
];

const roleDialogOpen = ref(false);
const permissionDialogOpen = ref(false);
const editingRole = ref<Role | null>(null);
const editingPermission = ref<Permission | null>(null);
const confirmDialog = ref({
    open: false,
    type: '' as 'role' | 'permission',
    itemId: 0,
    loading: false
});

const roleForm = reactive({
    name: '',
    permissions: [] as number[],
});

const permissionForm = reactive({
    name: '',
});

const permissionSearchQuery = ref('');
const rolePermissionSearchQuery = ref('');

const filteredPermissions = computed(() => {
    if (!permissionSearchQuery.value) {
        return props.permissions;
    }
    return props.permissions.filter(permission => 
        permission.name.toLowerCase().includes(permissionSearchQuery.value.toLowerCase())
    );
});

const filteredRolePermissions = computed(() => {
    if (!rolePermissionSearchQuery.value) {
        return props.permissions;
    }
    return props.permissions.filter(permission => 
        permission.name.toLowerCase().includes(rolePermissionSearchQuery.value.toLowerCase())
    );
});

const openRoleDialog = (role?: Role) => {
    if (role) {
        editingRole.value = role;
        roleForm.name = role.name;
        roleForm.permissions = role.permissions.map(p => p.id);
    } else {
        editingRole.value = null;
        roleForm.name = '';
        roleForm.permissions = [];
    }
    rolePermissionSearchQuery.value = ''; // Clear search when opening dialog
    roleDialogOpen.value = true;
};

const openPermissionDialog = (permission?: Permission) => {
    if (permission) {
        editingPermission.value = permission;
        permissionForm.name = permission.name;
    } else {
        editingPermission.value = null;
        permissionForm.name = '';
    }
    permissionDialogOpen.value = true;
};

const saveRole = async () => {
    const url = editingRole.value ? `/api/system/roles-permissions/roles/${editingRole.value.id}` : '/api/system/roles-permissions/roles';
    const method = editingRole.value ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify(roleForm),
        });
        
        if (response.ok) {
            roleDialogOpen.value = false;
            toast({
                title: 'Success',
                description: editingRole.value ? 'Role updated successfully' : 'Role created successfully',
                variant: 'success'
            });
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toast({
                title: 'Error',
                description: 'Failed to save role',
                variant: 'error'
            });
        }
    } catch (error) {
        // Error saving role
        toast({
            title: 'Error',
            description: 'Failed to save role',
            variant: 'error'
        });
    }
};

const savePermission = async () => {
    const url = editingPermission.value ? `/api/system/roles-permissions/permissions/${editingPermission.value.id}` : '/api/system/roles-permissions/permissions';
    const method = editingPermission.value ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify(permissionForm),
        });
        
        if (response.ok) {
            permissionDialogOpen.value = false;
            toast({
                title: 'Success',
                description: editingPermission.value ? 'Permission updated successfully' : 'Permission created successfully',
                variant: 'success'
            });
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toast({
                title: 'Error',
                description: 'Failed to save permission',
                variant: 'error'
            });
        }
    } catch (error) {
        // Error saving permission
        toast({
            title: 'Error',
            description: 'Failed to save permission',
            variant: 'error'
        });
    }
};

const deleteRole = (role: Role) => {
    confirmDialog.value.type = 'role';
    confirmDialog.value.itemId = role.id;
    confirmDialog.value.open = true;
};

const deletePermission = (permission: Permission) => {
    confirmDialog.value.type = 'permission';
    confirmDialog.value.itemId = permission.id;
    confirmDialog.value.open = true;
};

const confirmDelete = async () => {
    confirmDialog.value.loading = true;
    
    try {
        const endpoint = confirmDialog.value.type === 'role' 
            ? `/api/system/roles-permissions/roles/${confirmDialog.value.itemId}`
            : `/api/system/roles-permissions/permissions/${confirmDialog.value.itemId}`;
            
        // Helper function to get CSRF token (same as other modules)
        const getCsrfToken = () => {
            const tokenFromMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (tokenFromMeta) return tokenFromMeta;
            
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
            return '';
        };

        const response = await fetch(endpoint, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            credentials: 'same-origin',
        });
        
        if (response.ok) {
            toast({
                title: 'Success',
                description: `${confirmDialog.value.type === 'role' ? 'Role' : 'Permission'} deleted successfully`,
                variant: 'success'
            });
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toast({
                title: 'Error',
                description: `Failed to delete ${confirmDialog.value.type}`,
                variant: 'error'
            });
        }
    } catch (error) {
        toast({
            title: 'Error',
            description: `Failed to delete ${confirmDialog.value.type}`,
            variant: 'error'
        });
    } finally {
        confirmDialog.value.loading = false;
        confirmDialog.value.open = false;
        confirmDialog.value.itemId = 0;
        confirmDialog.value.type = '' as any;
    }
};
</script>

<template>
    <Head title="Roles & Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Roles & Permissions</h1>
                        <p class="text-muted-foreground">Manage user roles and permissions</p>
                    </div>
                </div>

                <!-- Roles Section -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    <Shield class="w-5 h-5" />
                                    Roles
                                </CardTitle>
                                <CardDescription>Manage user roles</CardDescription>
                            </div>
                            <Dialog v-model:open="roleDialogOpen">
                                <DialogTrigger as-child>
                                    <Button @click="openRoleDialog()">
                                        <Plus class="w-4 h-4 mr-2" />
                                        Add Role
                                    </Button>
                                </DialogTrigger>
                                <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                                    <DialogHeader>
                                        <DialogTitle>{{ editingRole ? 'Edit Role' : 'Create Role' }}</DialogTitle>
                                        <DialogDescription>
                                            {{ editingRole ? 'Update role information' : 'Create a new role with permissions' }}
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="space-y-6">
                                        <!-- Role Name Field -->
                                        <div class="space-y-2">
                                            <Label for="role-name" class="text-sm font-medium">Role Name</Label>
                                            <Input 
                                                id="role-name" 
                                                v-model="roleForm.name" 
                                                placeholder="Enter role name"
                                                class="h-10"
                                            />
                                            <p class="text-xs text-muted-foreground">
                                                Enter a descriptive name for this role
                                            </p>
                                        </div>
                                        
                                        <!-- Permissions Field -->
                                        <div class="space-y-3">
                                            <Label class="text-sm font-medium">Permissions</Label>
                                            <p class="text-xs text-muted-foreground">
                                                Select the permissions this role should have
                                            </p>
                                            
                                            <!-- Permission Search -->
                                            <div class="relative">
                                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4" />
                                                <Input
                                                    v-model="rolePermissionSearchQuery"
                                                    placeholder="Search permissions..."
                                                    class="pl-10 h-9"
                                                />
                                            </div>
                                            
                                            <div class="border rounded-lg p-4 max-h-64 overflow-y-auto">
                                                <div v-if="filteredRolePermissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <label 
                                                        v-for="permission in filteredRolePermissions" 
                                                        :key="permission.id" 
                                                        class="flex items-center space-x-3 p-2 rounded hover:bg-muted/50 cursor-pointer"
                                                    >
                                                        <input 
                                                            type="checkbox" 
                                                            :value="permission.id"
                                                            v-model="roleForm.permissions"
                                                            class="h-4 w-4 rounded border-input bg-background text-primary focus:ring-ring/20 dark:bg-background dark:border-input dark:text-primary"
                                                        />
                                                        <span class="text-sm font-medium">{{ permission.name }}</span>
                                                    </label>
                                                </div>
                                                
                                                <!-- No Results Message -->
                                                <div v-else-if="rolePermissionSearchQuery" class="text-center py-6">
                                                    <Search class="w-8 h-8 text-muted-foreground mx-auto mb-2" />
                                                    <p class="text-sm text-muted-foreground">
                                                        No permissions match "{{ rolePermissionSearchQuery }}"
                                                    </p>
                                                </div>
                                                
                                                <!-- No Permissions Available -->
                                                <div v-else class="text-center py-4 text-sm text-muted-foreground">
                                                    No permissions available
                                                </div>
                                            </div>
                                            
                                            <!-- Results Summary -->
                                            <div class="flex justify-between text-xs text-muted-foreground">
                                                <span>
                                                    Showing {{ filteredRolePermissions.length }} of {{ permissions.length }} permissions
                                                </span>
                                                <span>
                                                    Selected {{ roleForm.permissions.length }} permissions
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <DialogFooter>
                                        <Button @click="saveRole">
                                            {{ editingRole ? 'Update' : 'Create' }}
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="role in roles" :key="role.id" class="flex items-center justify-between p-4 border rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-semibold">{{ role.name }}</h3>
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        <Badge v-for="permission in role.permissions" :key="permission.id" variant="secondary">
                                            {{ permission.name }}
                                        </Badge>
                                        <span v-if="role.permissions.length === 0" class="text-sm text-muted-foreground">No permissions assigned</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Button @click="openRoleDialog(role)" variant="outline" size="sm">
                                        <Edit class="w-4 h-4" />
                                    </Button>
                                    <Button @click="deleteRole(role)" variant="destructive" size="sm">
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Permissions Section -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    <Key class="w-5 h-5" />
                                    Permissions
                                </CardTitle>
                                <CardDescription>Manage system permissions</CardDescription>
                            </div>
                            <Dialog v-model:open="permissionDialogOpen">
                                <DialogTrigger as-child>
                                    <Button @click="openPermissionDialog()">
                                        <Plus class="w-4 h-4 mr-2" />
                                        Add Permission
                                    </Button>
                                </DialogTrigger>
                                <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                                    <DialogHeader>
                                        <DialogTitle>{{ editingPermission ? 'Edit Permission' : 'Create Permission' }}</DialogTitle>
                                        <DialogDescription>
                                            {{ editingPermission ? 'Update permission name' : 'Create a new permission' }}
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="space-y-4">
                                        <div>
                                            <Label for="permission-name">Permission Name</Label>
                                            <Input id="permission-name" v-model="permissionForm.name" placeholder="Enter permission name" />
                                        </div>
                                    </div>
                                    <DialogFooter>
                                        <Button @click="savePermission">
                                            {{ editingPermission ? 'Update' : 'Create' }}
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-4 h-4" />
                                <Input
                                    v-model="permissionSearchQuery"
                                    placeholder="Search permissions..."
                                    class="pl-10"
                                />
                            </div>
                        </div>
                        
                        <!-- Results Count -->
                        <div class="mb-4 text-sm text-muted-foreground">
                            Showing {{ filteredPermissions.length }} of {{ permissions.length }} permissions
                        </div>
                        
                        <div v-if="filteredPermissions.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div v-for="permission in filteredPermissions" :key="permission.id" class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="font-medium">{{ permission.name }}</span>
                                <div class="flex items-center gap-2">
                                    <Button @click="openPermissionDialog(permission)" variant="outline" size="sm">
                                        <Edit class="w-4 h-4" />
                                    </Button>
                                    <Button @click="deletePermission(permission)" variant="destructive" size="sm">
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- No Results Message -->
                        <div v-else class="text-center py-8">
                            <Key class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-muted-foreground mb-2">No permissions found</h3>
                            <p class="text-sm text-muted-foreground">
                                {{ permissionSearchQuery ? `No permissions match "${permissionSearchQuery}"` : 'No permissions available' }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
    
    <!-- Confirm Delete Dialog -->
    <ConfirmDialog
        v-model:open="confirmDialog.open"
        :title="`Delete ${confirmDialog.type === 'role' ? 'Role' : 'Permission'}`"
        :description="`Are you sure you want to delete this ${confirmDialog.type}? This action cannot be undone.`"
        confirm-text="Delete"
        :loading="confirmDialog.loading"
        @confirm="confirmDelete"
    />
</template>