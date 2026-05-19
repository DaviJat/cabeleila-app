<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { DataTable, Column, Panel, Button, ConfirmDialog, useConfirm } from 'primevue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ClientDialog from '@/Pages/Admin/Clients/Partials/ClientDialog.vue';

const displayDialog = ref(false);
const selectedClient = ref(null);

const confirm = useConfirm();

const props = defineProps({
    clients: {
        type: [Array, Object], // Aceita Array (get) ou Object (paginate)
        required: true,
    },
});

// Garante que o DataTable funcione quer você use get() ou paginate() no Controller
const clientsData = computed(() => props.clients.data || props.clients);

const openDialog = (client = null) => {
    selectedClient.value = client; // Se null, abre modo criação; senão, modo edição
    displayDialog.value = true;
};

// Confirm Dialog para exclusão de cliente
const deleteClient = (id) => {
    confirm.require({
        header: 'Confirmar Exclusão',
        message: 'Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, Excluir',
        rejectLabel: 'Cancelar',
        rejectProps: { severity: 'secondary', variant: 'outlined' },
        acceptProps: { severity: 'danger' },
        accept: () => {
            router.delete(route('clients.destroy', id), {
                preserveScroll: true,
            });
        },
    });
};
</script>

<template>
    <Head title="Clientes" />
    <AuthenticatedLayout>
        <ConfirmDialog class="mx-4" />
        <Panel :pt="{ contentWrapper: 'overflow-x-auto' }">
            <!-- Cabeçalho da Tabela -->
            <template #header>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full px-2 gap-4">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Clientes</h2>
                        <p class="text-sm text-gray-500">Gerencie a base de clientes do sistema</p>
                    </div>
                    <!-- Botão Adicionar Cliente -->
                    <Button label="Adicionar Cliente" icon="pi pi-plus" class="w-full sm:w-auto" @click="openDialog()" />
                </div>
            </template>

            <!-- Conteúdo da Tabela -->
            <DataTable
                :value="clientsData"
                :paginator="true"
                :rows="10"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
                stripedRows
                class="border-x border-t">
                <Column field="id" header="Código" sortable class="hidden sm:table-cell" />
                <Column field="full_name" header="Nome" sortable />
                <Column field="phone" header="Telefone" sortable />
                <Column field="email" header="E-mail" sortable class="hidden md:table-cell" />
                <Column field="cpf" header="CPF" sortable class="hidden lg:table-cell" />

                <Column header="Ações" :exportable="false" style="min-width: 8rem">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <!-- Botão Editar -->
                            <Button icon="pi pi-pencil" variant="outlined" class="sm:!hidden" aria-label="Editar" @click="openDialog(slotProps.data)" />
                            <Button label="Editar" icon="pi pi-pencil" variant="outlined" class="!hidden sm:!inline-flex" @click="openDialog(slotProps.data)" />

                            <!-- Botão Excluir -->
                            <Button icon="pi pi-trash" variant="outlined" severity="danger" class="sm:!hidden" aria-label="Excluir" @click="deleteClient(slotProps.data.id)" />
                            <Button
                                label="Excluir"
                                icon="pi pi-trash"
                                variant="outlined"
                                severity="danger"
                                class="!hidden sm:!inline-flex"
                                @click="deleteClient(slotProps.data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </Panel>

        <!-- Modal de criação/edição -->
        <ClientDialog :visible="displayDialog" :client="selectedClient" @close="displayDialog = false" />
    </AuthenticatedLayout>
</template>
