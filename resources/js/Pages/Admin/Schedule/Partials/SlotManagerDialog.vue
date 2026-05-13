<script setup>
import { watch, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
// Importamos o Select, Textarea e Message para o formulário de Agendamento
import { Dialog, Button, DatePicker, Select, Textarea, Message } from 'primevue';

const props = defineProps({
    visible: Boolean,
    availability: Object,
    date: Date,
    clients: { type: Array, default: () => [] },
    services: { type: Array, default: () => [] },
});

const toast = useToast();
const page = usePage();
const emit = defineEmits(['close']);

// Determina se o horário já está criado no banco
const isEditing = computed(() => !!props.availability?.id);

// Computa se tem algum agendamento ativo nesta vaga
const activeAppointment = computed(() => {
    if (!props.availability || !props.availability.appointments) return null;
    return props.availability.appointments.find((appt) => appt.status !== 'canceled');
});

// FORMULÁRIO 1: O Horário em si
const form = useForm({
    id: null,
    date: '',
    hour: null,
});

// FORMULÁRIO 2: O Agendamento do Cliente
const appointmentForm = useForm({
    availability_id: null,
    client_id: null,
    service_id: null,
    notes: '',
});

// Reseta/Preenche os dados quando o dialog abre
watch(
    () => props.visible,
    (isOpen) => {
        if (isOpen) {
            if (props.availability) {
                // Modo Edição: Preenche form do Horário
                form.id = props.availability.id;
                const [hours, minutes] = props.availability.hour.split(':');
                const dateObj = new Date();
                dateObj.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                form.hour = dateObj;

                // Prepara o formulário de Agendamento referenciando esta vaga
                appointmentForm.reset();
                appointmentForm.availability_id = props.availability.id;
            } else {
                // Modo Criação: Reseta tudo e sugere a hora
                form.reset();
                appointmentForm.reset();
                const now = new Date();
                const roundedMinutes = now.getMinutes() < 30 ? 0 : 30;
                now.setMinutes(roundedMinutes);
                now.setSeconds(0, 0);
                form.hour = now;
            }
        }
    },
);

const closeDialog = () => {
    form.reset();
    form.clearErrors();
    appointmentForm.reset();
    appointmentForm.clearErrors();
    emit('close');
};

const submitTime = () => {
    const offset = props.date.getTimezoneOffset() * 60000;
    const localDate = new Date(props.date.getTime() - offset);
    form.date = localDate.toISOString().split('T')[0];

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

// Nova função para salvar o Agendamento
const submitAppointment = () => {
    appointmentForm.post(route('appointments.store'), {
        onSuccess: () => {
            closeDialog();
            toast.add({ severity: 'success', summary: 'Sucesso!', detail: 'Agendamento realizado com sucesso!', life: 3000 });
        },
    });
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="closeDialog" modal class="mx-4 w-full max-w-md">
        <template #header>
            <div class="flex flex-col items-start mr-8">
                <h3 class="text-xl font-bold leading-tight text-gray-600">
                    {{ isEditing ? 'Gerenciar Horário' : 'Adicionar Horário' }}
                </h3>
                <p class="text-sm text-gray-500">
                    {{ isEditing ? 'Edite a hora ou realize um agendamento' : 'Complete o campo para adicionar um novo horário' }}
                    para <strong>{{ date?.toLocaleDateString('pt-BR') }}</strong
                    >.
                </p>
            </div>
        </template>

        <!-- Conteúdo do Dialog com Scroll interno se ficar muito alto -->
        <div class="space-y-6 pt-2 overflow-y-auto max-h-[70vh] pr-2">
            <!-- SEÇÃO 1: HORÁRIO (Sempre Visível) -->
            <form id="form-horario" @submit.prevent="submitTime" class="space-y-4">
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
                                placeholder="Selecione o horário"
                                :invalid="!!form.errors.hour"
                                @update:modelValue="form.clearErrors('hour')" />
                            <small v-if="form.errors.hour" class="text-red-500 block mt-1">{{ form.errors.hour }}</small>
                            <small v-if="form.errors.date" class="text-red-500 block mt-1">{{ form.errors.date }}</small>
                        </div>
                        <Button :label="isEditing ? 'Atualizar' : 'Salvar'" icon="pi pi-check" type="submit" :loading="form.processing" outlined />
                    </div>
                </div>
            </form>

            <!-- SEÇÃO 2: AGENDAMENTO (Visível apenas se o horário já existir) -->
            <template v-if="isEditing">
                <hr class="border-gray-200" />

                <div>
                    <h4 class="text-lg font-bold text-gray-700 mb-3">Agendamento</h4>

                    <!-- Form de Agendamento (Aparece se a vaga estiver livre) -->
                    <form v-if="availability.is_available" id="form-agendamento" @submit.prevent="submitAppointment" class="space-y-4">
                        <div class="flex flex-col gap-1">
                            <label for="client" class="font-semibold text-gray-700 text-sm">Cliente</label>
                            <Select
                                id="client"
                                v-model="appointmentForm.client_id"
                                :options="clients"
                                optionLabel="full_name"
                                optionValue="id"
                                placeholder="Selecione um cliente"
                                :invalid="!!appointmentForm.errors.client_id"
                                fluid
                                filter />
                            <small v-if="appointmentForm.errors.client_id" class="text-red-500">{{ appointmentForm.errors.client_id }}</small>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label for="service" class="font-semibold text-gray-700 text-sm">Serviço</label>
                            <Select
                                id="service"
                                v-model="appointmentForm.service_id"
                                :options="services"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Selecione o que será feito"
                                :invalid="!!appointmentForm.errors.service_id"
                                fluid />
                            <small v-if="appointmentForm.errors.service_id" class="text-red-500">{{ appointmentForm.errors.service_id }}</small>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label for="notes" class="font-semibold text-gray-700 text-sm">Observações (Opcional)</label>
                            <Textarea id="notes" v-model="appointmentForm.notes" rows="2" fluid placeholder="Ex: Cliente tem alergia a produto X..." />
                        </div>

                        <div class="flex justify-end pt-2">
                            <Button
                                label="Confirmar Agendamento"
                                icon="pi pi-calendar-plus"
                                type="submit"
                                severity="primary"
                                :loading="appointmentForm.processing"
                                class="w-full" />
                        </div>
                    </form>

                    <!-- Aviso de Ocupado (Aparece se a vaga estiver vinculada a alguém) -->
                    <div v-else>
                        <Message severity="secondary" :closable="false" class="m-0 border border-gray-200">
                            <div class="flex items-center gap-2 font-bold mb-1 text-gray-700"><i class="pi pi-lock"></i> Horário Ocupado</div>
                            <p class="text-sm m-0 text-gray-600">Este horário já possui um agendamento vinculado a ele.</p>

                            <!-- Debug do agendamento atual -->
                            <div v-if="activeAppointment" class="mt-3 text-sm bg-white p-2 rounded border border-gray-100">
                                <div><strong>ID da Reserva:</strong> #{{ activeAppointment.id }}</div>
                                <div><strong>Status Atual:</strong> {{ activeAppointment.status }}</div>
                            </div>
                        </Message>
                    </div>
                </div>
            </template>
        </div>
    </Dialog>
</template>
