<script setup>
import { Panel } from 'primevue';
import vTooltip from 'primevue/tooltip';
import { formatCurrency } from '@/Utils/formatters';

defineProps({
    data: {
        type: Object,
        required: true,
        default: () => ({
            revenue: 0,
            appointments: 0,
            completed: 0,
            confirmed: 0,
            pending: 0,
        }),
    },
});
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 w-full">
        <!-- Card for Revenue -->
        <Panel class="px-2" :pt="{ header: '!pb-1.5' }" v-tooltip.bottom="'Receita total gerada apenas por agendamentos concluídos dentro do período selecionado.'">
            <template #header>
                <h3 class="font-semibold text-gray-600">Receita</h3>
            </template>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(data?.revenue ?? '0.00') }}</p>
        </Panel>
        <!-- Card for Total Appointments -->
        <Panel class="px-2" :pt="{ header: '!pb-1.5' }" v-tooltip.bottom="'Total de agendamentos dentro do período selecionado.'">
            <template #header>
                <h3 class="font-semibold text-gray-600">Agendamentos</h3>
            </template>
            <p class="text-2xl font-bold text-gray-700">
                {{ data?.appointments ?? 0 }}
            </p>
        </Panel>
        <!-- Card for Completed Appointments -->
        <Panel class="px-2" :pt="{ header: '!pb-1.5' }" v-tooltip.bottom="'Agendamentos que foram concluídos com sucesso.'">
            <template #header>
                <h3 class="font-semibold text-gray-600">Concluídos</h3>
            </template>
            <p class="text-2xl font-bold text-blue-600">
                {{ data?.completed ?? 0 }}
            </p>
        </Panel>
        <!-- Card for Confirmed Appointments -->
        <Panel class="px-2" :pt="{ header: '!pb-1.5' }" v-tooltip.bottom="'Agendamentos confirmados e agendados para acontecer.'">
            <template #header>
                <h3 class="font-semibold text-gray-600">Confirmados</h3>
            </template>
            <p class="text-2xl font-bold text-green-600">
                {{ data?.confirmed ?? 0 }}
            </p>
        </Panel>
        <!-- Card for Pending Appointments -->
        <Panel class="px-2" :pt="{ header: '!pb-1.5' }" v-tooltip.bottom="'Agendamentos pendentes de aprovação ou confirmação.'">
            <template #header>
                <h3 class="font-semibold text-gray-600">Pendentes</h3>
            </template>
            <p class="text-2xl font-bold text-yellow-600">
                {{ data?.pending ?? 0 }}
            </p>
        </Panel>
    </div>
</template>
