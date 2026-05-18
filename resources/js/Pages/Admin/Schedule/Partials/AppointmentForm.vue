<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { Select, Button, Textarea, MultiSelect, Tag } from 'primevue';
import { formatCurrency } from '@/Utils/formatters';

const props = defineProps({
    clients: { type: Array, required: true },
    services: { type: Array, required: true },
    availability: { type: Object, required: true },
});

const emit = defineEmits(['close']);
const toast = useToast();

const form = useForm({
    availability_id: props.availability?.id,
    client_id: null,
    service_ids: [],
    notes: '',
});

// Fetches the single active appointment (matches the backend model logic)
const activeAppointment = computed(() => {
    if (!props.availability || !props.availability.appointments) return null;
    return props.availability.appointments.find((appt) => appt.status !== 'canceled');
});

// Checks if there is an active appointment linked to this slot
const hasActiveAppointment = computed(() => {
    return !!activeAppointment.value;
});

// Handles the creation of a new appointment
const submitAppointment = () => {
    form.post(route('appointments.store'), {
        onSuccess: () => {
            emit('close');
            toast.add({ severity: 'success', summary: 'Sucesso!', detail: 'Agendamento realizado com sucesso!', life: 3000 });
        },
    });
};
</script>

<template>
    <!-- Dynamic Header -->
    <div class="flex flex-col items-start mr-8">
        <h3 class="text-lg font-bold leading-tight text-gray-600">
            {{ hasActiveAppointment ? 'Detalhes do Agendamento' : 'Agendamento' }}
        </h3>
        <p class="text-xs text-gray-500">
            <span v-if="!hasActiveAppointment">Complete os campos para agendar um novo horário.</span>
            <span v-else>Este horário já possui um agendamento.</span>
        </p>
    </div>
    <!-- Show appointment form only if there are no active appointments for this slot -->
    <form v-if="!hasActiveAppointment" id="appointment-form" @submit.prevent="submitAppointment" class="space-y-4 pt-4">
        <!-- Client Selection -->
        <div class="flex flex-col gap-1">
            <label for="client" class="font-semibold text-gray-700 text-sm">Cliente</label>
            <Select
                id="client"
                v-model="form.client_id"
                :options="clients"
                optionLabel="full_name"
                optionValue="id"
                placeholder="Selecione um cliente"
                emptyMessage="Nenhum cliente encontrado"
                :invalid="!!form.errors.client_id"
                fluid
                filter
                @update:modelValue="form.clearErrors('client_id')" />
            <small v-if="form.errors.client_id" class="text-red-500 block mt-1">{{ form.errors.client_id }}</small>
        </div>
        <!-- Service Multi-Selection -->
        <div class="flex flex-col gap-1">
            <label for="services" class="font-semibold text-gray-700 text-sm">Serviços</label>
            <MultiSelect
                id="services"
                v-model="form.service_ids"
                :options="services"
                optionLabel="name"
                optionValue="id"
                placeholder="Selecione os serviços"
                emptyMessage="Nenhum serviço encontrado"
                :invalid="!!form.errors.service_ids"
                fluid
                display="chip"
                :maxSelectedLabels="2"
                selectedItemsLabel="{0} serviços selecionados"
                :showToggleAll="false"
                @update:modelValue="form.clearErrors('service_ids')" />
            <small v-if="form.errors.service_ids" class="text-red-500 block mt-1">{{ form.errors.service_ids }}</small>
        </div>
        <!-- Notes -->
        <div class="flex flex-col gap-1">
            <label for="notes" class="font-semibold text-gray-700 text-sm">Observações (Opcional)</label>
            <Textarea id="notes" v-model="form.notes" rows="2" fluid placeholder="Ex: Cliente tem alergia a produto X..." />
        </div>
        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <Button label="Confirmar Agendamento" icon="pi pi-calendar-plus" type="submit" severity="primary" :loading="form.processing" class="w-full" />
        </div>
    </form>
    <!-- Active Appointment Display -->
    <div v-else class="pt-4 space-y-4">
        <!-- Renders the activeAppointment object directly -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-sm text-sm text-gray-700">
            <!-- Status and ID -->
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200">
                <span class="font-semibold">Agendamento #{{ activeAppointment.id }}</span>
                <Tag :value="props.availability.status.label" :severity="props.availability.status.severity" />
            </div>
            <!-- Client Name -->
            <div class="mb-3">
                <p class="font-semibold text-gray-800 mb-0.5">Cliente:</p>
                <p class="text-gray-600 font-medium">{{ activeAppointment.client.full_name }}</p>
            </div>
            <!-- Services -->
            <div v-if="activeAppointment.services && activeAppointment.services.length > 0" class="mb-3">
                <p class="font-semibold text-gray-800 mb-1">Serviços escolhidos:</p>
                <ul class="list-disc list-inside space-y-1 ml-1 text-gray-600">
                    <li v-for="service in activeAppointment.services" :key="service.id">
                        {{ service.name }} <span class="font-medium text-gray-800">{{ formatCurrency(service.price) }}</span>
                    </li>
                </ul>
            </div>
            <!-- Notes -->
            <div v-if="activeAppointment.notes">
                <p class="font-semibold text-gray-800 mb-1">Observações:</p>
                <p class="text-gray-600 bg-white p-2 rounded border border-gray-100">{{ activeAppointment.notes }}</p>
            </div>
        </div>
    </div>
</template>
