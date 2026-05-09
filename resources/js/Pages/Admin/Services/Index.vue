<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { DataTable, Column, Panel, Button, ConfirmDialog, useConfirm } from 'primevue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceDialog from '@/Pages/Admin/Services/Partials/ServiceDialog.vue';

// Estados para controlar a exibição dos diálogos
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
    selectedService.value = service;
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
        rejectProps: { severity: 'secondary', variant: 'outlined' }, // Deixa o botão cancelar mais discreto
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
        <ConfirmDialog />
        <Panel>
            <!-- Cabeçalho da tabela -->
            <template #header>
                <div class="flex justify-between items-center w-full px-2">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Serviços</h2>
                        <p class="text-sm text-gray-500">Gerencie seus serviços</p>
                    </div>
                    <!-- Botão para adicionar serviço -->
                    <Button label="Adicionar Serviço" icon="pi pi-plus" @click="openDialog()" />
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
                <!-- ID -->
                <Column field="id" header="Código" sortable />
                <!-- Nome -->
                <Column field="name" header="Nome" sortable />
                <!-- Descrição -->
                <Column field="description" header="Descrição" sortable />
                <!-- Preço -->
                <Column field="price" header="Preço" sortable bodyStyle="text-align: right">
                    <template #body="slotProps"> {{ formatCurrency(slotProps.data.price) }} </template>
                </Column>
                <!-- Duração -->
                <Column field="duration_minutes" header="Duração" sortable bodyStyle="text-align: right">
                    <template #body="slotProps"> {{ slotProps.data.duration_minutes }} min </template>
                </Column>
                <!-- Ações -->
                <Column header="Ações">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <!-- Botão para editar serviço -->
                            <Button label="Editar" icon="pi pi-pencil" variant="outlined" @click="openDialog(slotProps.data)" />
                            <!-- Botão para excluir serviço -->
                            <Button label="Excluir" icon="pi pi-trash" variant="outlined" severity="danger" @click="deleteService(slotProps.data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </Panel>
        <!-- Diálogo para criar/editar serviço -->
        <ServiceDialog :visible="displayDialog" :service="selectedService" @close="displayDialog = false" />
    </AuthenticatedLayout>
</template>
