<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { Panel } from 'primevue';
</script>

<template>
    <Head title="Meu Perfil" />
    <AuthenticatedLayout>
        <Panel>
            <template #header>
                <div class="flex justify-between items-center w-full px-2">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Meu Perfil</h2>
                        <p class="text-sm text-gray-500">Gerencie suas informações pessoais e segurança da conta</p>
                    </div>
                </div>
            </template>
            <div class="flex flex-col gap-8 p-2 sm:p-6">
                <!-- Section for updating profile information -->
                <section class="max-w-xl">
                    <UpdateProfileInformationForm />
                </section>
                <!-- Section for updating password -->
                <hr class="border-gray-200" />
                <section class="max-w-xl">
                    <UpdatePasswordForm />
                </section>
                <!-- Section for deleting account -->
                <!-- Commented out for now -->
                <!-- <hr class="border-gray-200" />
                <section class="max-w-xl">
                    <DeleteUserForm />
                </section> -->
            </div>
        </Panel>
    </AuthenticatedLayout>
</template>
