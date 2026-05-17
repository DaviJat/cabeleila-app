<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { DataTable, Column, Panel, Button, ConfirmDialog, useConfirm } from 'primevue';
import { formatCurrency } from '@/Utils/formatters';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceDialog from '@/Pages/Admin/Services/Partials/ServiceDialog.vue';

const displayDialog = ref(false);
const selectedService = ref(null);

const confirm = useConfirm();

const props = defineProps({
    services: {
        type: Array,
        required: true,
    },
});

const openDialog = (service = null) => {
    selectedService.value = service; // If service is null, it will open the dialog in create mode; otherwise, it will be in edit mode
    displayDialog.value = true;
};

// Confirm Dialog for deleting (deactivate) a service
const deleteService = (id) => {
    confirm.require({
        header: 'Confirmar Exclusão',
        message: 'Tem certeza que deseja excluir este serviço?',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, Excluir',
        rejectLabel: 'Cancelar',
        rejectProps: { severity: 'secondary', variant: 'outlined' },
        acceptProps: { severity: 'danger' },
        accept: () => {
            router.delete(route('services.destroy', id), {
                preserveScroll: true,
            });
        },
    });
};
</script>

<template>
    <Head title="Serviços" />
    <AuthenticatedLayout>
        <ConfirmDialog class="mx-4" />
        <Panel :pt="{ contentWrapper: 'overflow-x-auto' }">
            <!-- Table Header -->
            <template #header>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full px-2 gap-4">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Serviços</h2>
                        <p class="text-sm text-gray-500">Gerencie seus serviços</p>
                    </div>
                    <!-- Add Service Button -->
                    <Button label="Adicionar Serviço" icon="pi pi-plus" class="w-full sm:w-auto" @click="openDialog()" />
                </div>
            </template>
            <!-- Table Content -->
            <DataTable
                :value="services"
                :paginator="true"
                :rows="10"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
                stripedRows
                class="border-x border-t">
                <!-- ID (hidden for small screens) -->
                <Column field="id" header="Código" sortable class="hidden sm:table-cell" />
                <!-- Name -->
                <Column field="name" header="Nome" sortable />
                <!-- Description (hidden for small screens) -->
                <Column field="description" header="Descrição" sortable class="hidden sm:table-cell" />
                <!-- Price -->
                <Column field="price" header="Preço" sortable bodyStyle="text-align: right">
                    <template #body="slotProps"> {{ formatCurrency(slotProps.data.price) }} </template>
                </Column>
                <!-- Duration -->
                <Column field="duration_minutes" header="Duração" sortable bodyStyle="text-align: right">
                    <template #body="slotProps"> {{ slotProps.data.duration_minutes }} min </template>
                </Column>
                <Column header="Ações">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <!-- Edit Button -->
                            <Button icon="pi pi-pencil" variant="outlined" class="sm:!hidden" aria-label="Editar" @click="openDialog(slotProps.data)" />
                            <Button label="Editar" icon="pi pi-pencil" variant="outlined" class="!hidden sm:!inline-flex" @click="openDialog(slotProps.data)" />
                            <!-- Delete Button (deactivate) -->
                            <Button icon="pi pi-trash" variant="outlined" severity="danger" class="sm:!hidden" aria-label="Excluir" @click="deleteService(slotProps.data.id)" />
                            <Button
                                label="Excluir"
                                icon="pi pi-trash"
                                variant="outlined"
                                severity="danger"
                                class="!hidden sm:!inline-flex"
                                @click="deleteService(slotProps.data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </Panel>
        <!-- Dialog for creating/editing service -->
        <ServiceDialog :visible="displayDialog" :service="selectedService" @close="displayDialog = false" />
    </AuthenticatedLayout>
</template>
