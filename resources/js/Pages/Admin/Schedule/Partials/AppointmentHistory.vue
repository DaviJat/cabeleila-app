<script setup>
import { computed } from 'vue';
import { Tag } from 'primevue';
import { formatCurrency } from '@/Utils/formatters';

const props = defineProps({
    availability: { type: Object, required: true },
});

// Sorts appointments from newest to oldest based on creation date
const history = computed(() => {
    if (!props.availability || !props.availability.appointments) return [];

    // Create a shallow copy array to avoid mutating the original prop, then sort
    return [...props.availability.appointments].sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at);
    });
});

// Helper to translate status and assign the correct PrimeVue Tag severity
const getStatusProps = (status) => {
    const map = {
        confirmed: { label: 'Confirmado', severity: 'info' },
        pending: { label: 'Pendente', severity: 'warn' },
        canceled: { label: 'Cancelado', severity: 'danger' },
    };
    return map[status] || { label: status, severity: 'secondary' };
};

// Formats the creation timestamp into a readable localized string
const formatDateTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <div class="flex flex-col items-start mr-8 mb-4">
        <h3 class="text-lg font-bold leading-tight text-gray-600">Histórico de Movimentações</h3>
        <p class="text-xs text-gray-500">Acompanhe o registro de reservas e cancelamentos ocorridos nesta vaga.</p>
    </div>

    <div v-if="history.length === 0" class="text-sm text-gray-500 italic p-4 bg-gray-50 rounded-lg text-center border border-dashed border-gray-300">
        Nenhum histórico registrado para este horário.
    </div>

    <div v-else class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
        <div
            v-for="appointment in history"
            :key="appointment.id"
            class="p-4 border rounded-lg shadow-sm text-sm transition-colors"
            :class="appointment.status === 'canceled' ? 'bg-gray-50 border-gray-200 opacity-75' : 'bg-white border-blue-100'">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 pb-2 border-b border-gray-100 gap-2">
                <div class="flex flex-col">
                    <span class="font-semibold text-gray-700">Agendamento #{{ appointment.id }}</span>
                    <span class="text-xs text-gray-400">Modificado em: {{ formatDateTime(appointment.updated_at) }}</span>
                </div>
                <Tag :value="appointment.status_badge.label" :severity="appointment.status_badge.severity" />
            </div>

            <div class="mb-2">
                <span class="font-semibold text-gray-700">Cliente: </span>
                <span class="text-gray-600">{{ appointment.client?.full_name || 'Desconhecido' }}</span>
            </div>

            <div v-if="appointment.services && appointment.services.length > 0" class="mb-2">
                <span class="font-semibold text-gray-700 block mb-1">Serviços:</span>
                <ul class="list-disc list-inside text-gray-600 ml-1">
                    <li v-for="service in appointment.services" :key="service.id">
                        {{ service.name }}
                        <span class="text-xs font-medium text-gray-500">({{ formatCurrency(service.price) }})</span>
                    </li>
                </ul>
            </div>

            <div v-if="appointment.notes" class="mt-3 bg-gray-100/50 p-2 rounded text-gray-600 border-l-2 border-gray-300">
                <p class="font-semibold text-xs text-gray-500 mb-1">Observações:</p>
                <span class="italic">{{ appointment.notes }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
