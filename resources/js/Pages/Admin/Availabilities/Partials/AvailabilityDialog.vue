<script setup>
import { watch, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { Dialog, Button, DatePicker } from 'primevue';

const props = defineProps({
    visible: Boolean,
    availability: {
        type: Object,
    },
    date: {
        type: Date,
    },
});

const toast = useToast();
const page = usePage();
const emit = defineEmits(['close']);

// Determina se estamos editando ou criando uma disponibilidade
const isEditing = computed(() => !!props.availability?.id);

const form = useForm({
    id: null, // Campo ID essencial para o updateOrCreate no backend
    date: '',
    hour: null,
});

// Identifica quando o Dialog é aberto
watch(
    () => props.visible,
    (isOpen) => {
        if (isOpen) {
            if (props.availability) {
                // Modo Edição: Preenche com os dados da disponibilidade selecionada
                form.id = props.availability.id;

                // Converte a string de hora (ex: "14:30:00") para um objeto Date para o DatePicker
                const [hours, minutes] = props.availability.hour.split(':');
                const dateObj = new Date();
                dateObj.setHours(parseInt(hours), parseInt(minutes), 0);
                form.hour = dateObj;
            } else {
                // Modo Criação: Reseta o formulário
                form.reset();
            }
        }
    },
);

const closeDialog = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const submit = () => {
    // O offset previne bugs de fuso horário onde o JS subtrai 1 dia na conversão para ISO
    const offset = props.date.getTimezoneOffset() * 60000;
    const localDate = new Date(props.date.getTime() - offset);
    form.date = localDate.toISOString().split('T')[0];

    // Formata a hora para string (HH:mm) antes de enviar para o backend
    let formattedHour = form.hour;
    if (form.hour instanceof Date) {
        const hours = String(form.hour.getHours()).padStart(2, '0');
        const minutes = String(form.hour.getMinutes()).padStart(2, '0');
        formattedHour = `${hours}:${minutes}`;
    }

    // Como você usa updateOrCreate na controller, usamos sempre a rota store com POST
    form.transform((data) => ({
        ...data,
        hour: formattedHour,
    })).post(route('availabilities.store'), {
        onSuccess: () => {
            closeDialog();
            // Pega a mensagem de sucesso definida no controller (flash message)
            const defaultMessage = isEditing.value ? 'Horário atualizado com sucesso!' : 'Horário cadastrado com sucesso!';
            const mensagem = page.props.flash?.success || defaultMessage;

            toast.add({
                severity: 'success',
                summary: 'Sucesso!',
                detail: mensagem,
                life: 3000,
            });
        },
        onError: (errors) => {
            // Prioriza erros específicos de data e hora, depois mensagens genéricas do backend, e por fim uma mensagem padrão
            const mensagem = errors.date || errors.hour || page.props.flash?.error || 'Verifique os dados preenchidos.';

            toast.add({
                severity: 'error',
                summary: 'Atenção',
                detail: mensagem,
                life: 5000,
            });
        },
    });
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="closeDialog" modal class="mx-4 w-full max-w-lg">
        <!-- Cabeçalho do Dialog -->
        <template #header>
            <div class="flex flex-col items-start mr-8">
                <h3 class="text-xl font-bold leading-tight text-gray-600">
                    {{ isEditing ? 'Editar Horário' : 'Adicionar Horário' }}
                </h3>
                <p class="text-sm text-gray-500">
                    {{ isEditing ? 'Altere o horário selecionado.' : 'Complete o campo para adicionar um novo horário disponível.' }}
                    para o dia <strong>{{ date?.toLocaleDateString('pt-BR') }}</strong
                    >.
                </p>
            </div>
        </template>
        <!-- Conteúdo do Dialog -->
        <form id="form-horario" @submit.prevent="submit" class="space-y-4 pt-2">
            <div class="flex flex-col gap-1">
                <label for="hour" class="font-semibold text-gray-700">Hora do Atendimento</label>
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
                <small v-if="form.errors.hour" class="text-red-500">{{ form.errors.hour }}</small>
                <p class="text-xs text-gray-400 mt-1">Selecione a hora e os minutos em que o profissional estará livre.</p>
            </div>
        </form>
        <!-- Rodapé do Dialog -->
        <template #footer>
            <Button label="Cancelar" variant="text" class="p-button-text" @click="closeDialog()" />
            <!-- Mantendo o botão dinâmico para salvar ou atualizar -->
            <Button :label="isEditing ? 'Atualizar' : 'Salvar'" iconPos="right" type="submit" form="form-horario" :loading="form.processing" />
        </template>
    </Dialog>
</template>
