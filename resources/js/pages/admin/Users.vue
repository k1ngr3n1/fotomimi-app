<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Check, X, Shield, Mail, User, Calendar } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    approved: boolean;
    is_superadmin: boolean;
    email_verified_at: string | null;
    created_at: string;
    created_at_human: string;
}

defineProps<{
    users: User[];
}>();

const approveForm = useForm({
    approved: true
});

const approveUser = (userId: number, approved: boolean) => {
    approveForm.approved = approved;
    approveForm.patch(route('admin.users.approve', userId), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('User approval updated');
        }
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="User Management" />

        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">User Management</h1>
                <p class="text-gray-400">
                    Manage user approvals and permissions
                </p>
            </div>

            <div class="grid gap-6">
                <Card v-for="user in users" :key="user.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-muted">
                                    <User class="h-5 w-5" />
                                </div>
                                <div>
                                    <CardTitle class="text-lg">{{ user.name }}</CardTitle>
                                    <CardDescription class="flex items-center gap-2">
                                        <Mail class="h-4 w-4" />
                                        {{ user.email }}
                                    </CardDescription>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge v-if="user.is_superadmin" variant="destructive" class="gap-1">
                                    <Shield class="h-3 w-3" />
                                    Superadmin
                                </Badge>
                                <Badge 
                                    :variant="user.approved ? 'default' : 'secondary'"
                                    class="gap-1"
                                >
                                    <Check v-if="user.approved" class="h-3 w-3" />
                                    <X v-else class="h-3 w-3" />
                                    {{ user.approved ? 'Approved' : 'Pending' }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Email Verified:</span>
                                    <span class="ml-2">
                                        {{ user.email_verified_at ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <Calendar class="h-4 w-4" />
                                    <span class="font-medium">Registered:</span>
                                    <span class="ml-2">{{ user.created_at_human }}</span>
                                </div>
                            </div>
                            
                            <div v-if="!user.is_superadmin" class="flex gap-2 pt-2">
                                <Button 
                                    v-if="!user.approved"
                                    @click="approveUser(user.id, true)"
                                    size="sm"
                                    class="gap-1"
                                    :disabled="approveForm.processing"
                                >
                                    <Check class="h-4 w-4" />
                                    Approve User
                                </Button>
                                <Button 
                                    v-if="user.approved"
                                    @click="approveUser(user.id, false)"
                                    variant="destructive"
                                    size="sm"
                                    class="gap-1"
                                    :disabled="approveForm.processing"
                                >
                                    <X class="h-4 w-4" />
                                    Revoke Approval
                                </Button>
                            </div>
                            <div v-else class="pt-2">
                                <p class="text-sm text-muted-foreground">
                                    Superadmin accounts cannot be modified
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="users.length === 0" class="text-center py-12">
                <User class="mx-auto h-12 w-12 text-muted-foreground" />
                <h3 class="mt-4 text-lg font-semibold">No users found</h3>
                <p class="text-muted-foreground">There are no registered users yet.</p>
            </div>
        </div>
    </AdminLayout>
</template>
