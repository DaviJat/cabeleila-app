<script setup>
import { watch, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { Dialog, Button, InputText, InputNumber, Textarea, Toast } from 'primevue';

const props = defineProps({
    visible: Boolean,
    service: {
        type: Object,
    },
});

const toast = useToast();
const page = usePage();
const emit = defineEmits(['close']);

// Determina se estamos editando ou criando um serviço
const isEditing = computed(() => !!props.service?.id);

const form = useForm({
    id: null,
    name: '',
    description: '',
    price: null,
    duration_minutes: null,
});

// Identifica quando o Dialog é aberto
watch(
    () => props.visible,
    (isOpen) => {
        if (isOpen) {
            if (props.service) {
                // Modo Edição: Preenche com os dados do serviço selecionado
                form.id = props.service.id;
                form.name = props.service.name;
                form.description = props.service.description;
                form.price = Number(props.service.price);
                form.duration_minutes = Number(props.service.duration_minutes);
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
    form.post(route('admin.services.store'), {
        onSuccess: () => {
            closeDialog();
            const defaultMessage = isEditing.value ? 'Serviço atualizado com sucesso!' : 'Serviço cadastrado com sucesso!';
            const mensagem = page.props.flash?.success || defaultMessage;

            toast.add({
                severity: 'success',
                summary: 'Sucesso!',
                detail: mensagem,
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <Toast />
    <Dialog :visible="visible" @update:visible="closeDialog" modal>
        <!-- Cabeçalho do Dialog -->
        <template #header>
            <div class="flex flex-col items-start mr-8">
                <!-- Título e Descrição Dinâmicos -->
                <h3 class="text-xl font-bold leading-tight text-gray-600">
                    {{ isEditing ? 'Editar Serviço' : 'Adicionar Serviço' }}
                </h3>
                <p class="text-sm text-gray-500">
                    {{ isEditing ? 'Altere os dados do serviço selecionado.' : 'Complete os campos para adicionar um novo serviço.' }}
                </p>
            </div>
        </template>
        <!-- Conteúdo do Dialog -->
        <form id="form-servico" @submit.prevent="submit" class="space-y-4 pt-2">
            <!-- Nome do Serviço -->
            <div class="flex flex-col gap-1">
                <label for="name" class="font-semibold">Nome</label>
                <!-- Placeholder alterado para exemplo -->
                <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" placeholder="Corte de Cabelo Masculino" @update:modelValue="form.clearErrors('name')" />
                <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
            </div>
            <!-- Descrição do Serviço -->
            <div class="flex flex-col gap-1">
                <label for="description" class="font-semibold">Descrição</label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    :invalid="!!form.errors.description"
                    placeholder="Serviço completo de corte com tesoura, incluindo lavagem e finalização"
                    @update:modelValue="form.clearErrors('description')" />
                <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <!-- Preço do Serviço -->
                <div class="flex flex-col gap-1">
                    <label for="price" class="font-semibold">Preço</label>
                    <InputNumber
                        id="price"
                        v-model="form.price"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        :invalid="!!form.errors.price"
                        placeholder="R$ 100,00"
                        @update:modelValue="form.clearErrors('price')" />
                    <small v-if="form.errors.price" class="text-red-500">{{ form.errors.price }}</small>
                </div>
                <!-- Duração do Serviço -->
                <div class="flex flex-col gap-1">
                    <label for="duration" class="font-semibold">Duração (min)</label>
                    <InputNumber
                        id="duration"
                        v-model="form.duration_minutes"
                        suffix=" min"
                        :min="1"
                        :invalid="!!form.errors.duration_minutes"
                        placeholder="45 min"
                        @update:modelValue="form.clearErrors('duration_minutes')" />
                    <small v-if="form.errors.duration_minutes" class="text-red-500">{{ form.errors.duration_minutes }}</small>
                </div>
            </div>
        </form>
        <!-- Rodapé do Dialog -->
        <template #footer>
            <!-- Botão de Cancelar -->
            <Button label="Cancelar" class="p-button-text" @click="closeDialog()" />
            <!-- Botão Dinâmico para Criar/Atualizar -->
            <Button :label="isEditing ? 'Atualizar' : 'Salvar'" iconPos="right" type="submit" form="form-servico" :loading="form.processing" />
        </template>
    </Dialog>
</template>
