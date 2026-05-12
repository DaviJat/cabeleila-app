<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { DataTable, Column, Panel, Button, ConfirmDialog, useConfirm } from 'primevue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceDialog from '@/Pages/Admin/Services/Partials/ServiceDialog.vue';

// Estados para controlar a exibição dos Dialogs
const displayDialog = ref(false);

// Armazenar o serviço selecionado para edição
const selectedService = ref(null);

const confirm = useConfirm();

const props = defineProps({
    services: {
        type: Array,
        required: true,
    },
});

const formatCurrency = (value) => {
    return Number(value).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    });
};

// Função para abrir o Dialog
const openDialog = (service = null) => {
    selectedService.value = service; // Armazena o serviço selecionado para edição (ou null para criação)
    displayDialog.value = true;
};

// Função para excluir (desativar) um serviço
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
            <!-- Cabeçalho da tabela -->
            <template #header>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full px-2 gap-4">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Serviços</h2>
                        <p class="text-sm text-gray-500">Gerencie seus serviços</p>
                    </div>
                    <!-- Botão para adicionar serviço -->
                    <Button label="Adicionar Serviço" icon="pi pi-plus" class="w-full sm:w-auto" @click="openDialog()" />
                </div>
            </template>
            <!-- Conteúdo da tabela -->
            <DataTable
                :value="services"
                :paginator="true"
                :rows="10"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
                stripedRows
                class="border-x border-t">
                <!-- ID (escondido para telas pequenas) -->
                <Column field="id" header="Código" sortable class="hidden sm:table-cell" />
                <!-- Nome -->
                <Column field="name" header="Nome" sortable />
                <!-- Descrição (escondido para telas pequenas) -->
                <Column field="description" header="Descrição" sortable class="hidden sm:table-cell" />
                <!-- Preço -->
                <Column field="price" header="Preço" sortable bodyStyle="text-align: right">
                    <template #body="slotProps"> {{ formatCurrency(slotProps.data.price) }} </template>
                </Column>
                <!-- Duração -->
                <Column field="duration_minutes" header="Duração" sortable bodyStyle="text-align: right">
                    <template #body="slotProps"> {{ slotProps.data.duration_minutes }} min </template>
                </Column>
                <Column header="Ações">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <!-- Botão de editar -->
                            <Button icon="pi pi-pencil" variant="outlined" class="sm:!hidden" aria-label="Editar" @click="openDialog(slotProps.data)" />
                            <Button label="Editar" icon="pi pi-pencil" variant="outlined" class="!hidden sm:!inline-flex" @click="openDialog(slotProps.data)" />
                            <!-- Botão de excluir (desativar) -->
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
        <!-- Diálogo para criar/editar serviço -->
        <ServiceDialog :visible="displayDialog" :service="selectedService" @close="displayDialog = false" />
    </AuthenticatedLayout>
</template>
