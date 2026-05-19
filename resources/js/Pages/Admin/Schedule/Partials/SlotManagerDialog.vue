<script setup>
import { watch, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { Dialog, Button, DatePicker } from 'primevue';
import AppointmentForm from '@/Pages/Admin/Schedule/Partials/AppointmentForm.vue';
import AppointmentHistory from '@/Pages/Admin/Schedule/Partials/AppointmentHistory.vue';

const props = defineProps({
    visible: Boolean,
    availability: Object,
    date: Date,
    clients: Array,
    services: Array,
});

const toast = useToast();
const page = usePage();
const emit = defineEmits(['close']);

// Determines if the slot is already saved in the database
const isEditing = computed(() => !!props.availability?.id);

// Form for managing the time slot
const form = useForm({
    id: null,
    date: '',
    hour: null,
});

// Resets and populates form data when the dialog opens
watch(
    () => props.visible,
    (isOpen) => {
        if (isOpen) {
            if (props.availability) {
                // Edit mode: Fill slot form
                form.id = props.availability.id;
                const [hours, minutes] = props.availability.hour.split(':');
                const dateObj = new Date();
                dateObj.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                form.hour = dateObj;
            } else {
                // Create mode: Reset and suggest the current rounded time
                form.reset();
                const now = new Date();
                const roundedMinutes = now.getMinutes() < 30 ? 0 : 30;
                now.setMinutes(roundedMinutes);
                now.setSeconds(0, 0);
                form.hour = now;
            }
        }
    },
);

// Closes the dialog and clears validation errors
const closeDialog = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

// Handles the creation or update of a time slot
const submitTime = () => {
    // Adjust timezone offset to save the correct date
    const offset = props.date.getTimezoneOffset() * 60000;
    const localDate = new Date(props.date.getTime() - offset);
    form.date = localDate.toISOString().split('T')[0];

    // Format hour to HH:mm
    let formattedHour = form.hour;
    if (form.hour instanceof Date) {
        const hours = String(form.hour.getHours()).padStart(2, '0');
        const minutes = String(form.hour.getMinutes()).padStart(2, '0');
        formattedHour = `${hours}:${minutes}`;
    }

    form.transform((data) => ({
        ...data,
        hour: formattedHour,
    })).post(route('availabilities.store'), {
        onSuccess: () => {
            closeDialog();
            const defaultMessage = isEditing.value ? 'Horário atualizado com sucesso!' : 'Horário cadastrado com sucesso!';
            toast.add({ severity: 'success', summary: 'Sucesso!', detail: page.props.flash?.success || defaultMessage, life: 3000 });
        },
    });
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="closeDialog" modal class="mx-4">
        <template #header>
            <div class="flex flex-col items-start mr-8">
                <h3 class="text-xl font-bold leading-tight text-gray-600">
                    {{ isEditing ? 'Gerenciar Horário' : 'Adicionar Horário' }}
                </h3>
                <p class="text-xs text-gray-500">
                    <span v-if="availability?.status.is_blocked"
                        >Visualizando informações do horário <strong>({{ date?.toLocaleDateString('pt-BR') }})</strong>.</span
                    >
                    <span v-else
                        >{{ isEditing ? 'Edite o campo para atualizar o horário' : 'Complete o campo para adicionar um novo horário' }}
                        <strong>({{ date?.toLocaleDateString('pt-BR') }})</strong>.</span
                    >
                </p>
            </div>
        </template>
        <!-- Form for create/edit availability slots -->
        <form id="availability-form" @submit.prevent="submitTime" class="space-y-4 pt-2">
            <div class="flex flex-col gap-1">
                <label for="hour" class="font-semibold text-gray-700 text-sm">Hora do Atendimento</label>
                <div class="flex gap-2 items-start">
                    <div class="flex-1">
                        <DatePicker
                            id="hour"
                            v-model="form.hour"
                            timeOnly
                            hourFormat="24"
                            :stepMinute="30"
                            readonlyInput
                            fluid
                            :readonly="availability?.status.is_blocked"
                            placeholder="Selecione o horário"
                            :invalid="!!form.errors.hour"
                            @update:modelValue="form.clearErrors('hour')" />
                        <small v-if="form.errors.hour" class="text-red-500 block mt-1">{{ form.errors.hour }}</small>
                        <small v-if="form.errors.date" class="text-red-500 block mt-1">{{ form.errors.date }}</small>
                    </div>
                    <Button
                        v-if="!availability?.status.is_blocked"
                        :label="isEditing ? 'Atualizar' : 'Adicionar'"
                        icon="pi pi-check"
                        type="submit"
                        :loading="form.processing"
                        outlined />
                </div>
            </div>
        </form>
        <div v-if="isEditing" class="mt-6 border-t pt-4 border-gray-200">
            <!-- Show Appointment History for blocked slots -->
            <AppointmentHistory v-if="availability?.status?.is_blocked" :availability="availability" :clients="clients" :services="services" />
            <!-- Show Appointment Form for available slots -->
            <AppointmentForm v-else :availability="availability" :clients="clients" :services="services" @close="closeDialog" />
        </div>
    </Dialog>
</template>
