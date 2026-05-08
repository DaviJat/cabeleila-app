<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Panel from 'primevue/panel';
import Button from 'primevue/button';

import CreateServiceDialog from './Partials/CreateServiceDialog.vue';

const displayDialog = ref(false);

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
</script>

<template>
    <Head title="Serviços" />
    <AuthenticatedLayout>
        <Panel>
            <template #header>
                <div class="flex justify-between items-center w-full px-2">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Serviços</h2>
                        <p class="text-sm text-gray-500">Gerencie seus serviços</p>
                    </div>
                    <Button label="Adicionar Serviço" icon="pi pi-plus" @click="displayDialog = true" />
                </div>
            </template>
            <DataTable
                :value="services"
                :paginator="true"
                :rows="10"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
                stripedRows
                class="border-x border-t">
                <Column field="id" header="Código" sortable />
                <Column field="name" header="Nome" sortable />
                <Column field="description" header="Descrição" sortable />

                <Column field="price" header="Preço" sortable>
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.price) }}
                    </template>
                </Column>

                <Column field="duration_minutes" header="Duração" sortable>
                    <template #body="slotProps"> {{ slotProps.data.duration_minutes }} min </template>
                </Column>
            </DataTable>
        </Panel>
        <CreateServiceDialog :visible="displayDialog" @close="displayDialog = false" />
    </AuthenticatedLayout>
</template>
