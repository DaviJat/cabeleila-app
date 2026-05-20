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

// Emit event to parent component to close the dialog
const emit = defineEmits(['close']);

// Determines if we are editing an existing service or creating a new one
const isEditing = computed(() => !!props.service?.id);

const form = useForm({
    id: null,
    name: '',
    description: '',
    price: null,
    duration_minutes: null,
});

watch(
    () => props.visible,
    (isOpen) => {
        if (isOpen) {
            if (props.service) {
                form.id = props.service.id;
                form.name = props.service.name;
                form.description = props.service.description;
                form.price = Number(props.service.price);
                form.duration_minutes = Number(props.service.duration_minutes);
            } else {
                form.reset();
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

// Submits the form to create or update a service
const submit = () => {
    form.post(route('services.store'), {
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
    <Dialog :visible="visible" @update:visible="closeDialog" modal class="mx-4">
        <!-- Dialog Header -->
        <template #header>
            <div class="flex flex-col items-start mr-8">
                <!-- Dynamic Title and Description -->
                <h3 class="text-xl font-bold leading-tight text-gray-600">
                    {{ isEditing ? 'Editar Serviço' : 'Adicionar Serviço' }}
                </h3>
                <p class="text-xs text-gray-500">
                    {{ isEditing ? 'Altere os dados do serviço selecionado.' : 'Complete os campos para adicionar um novo serviço.' }}
                </p>
            </div>
        </template>
        <!-- Dialog Content -->
        <form id="service-form" @submit.prevent="submit" class="space-y-4 pt-2">
            <!-- Name -->
            <div class="flex flex-col gap-1">
                <label for="name" class="font-semibold">Nome</label>
                <InputText
                    id="name"
                    maxlength="50"
                    v-model="form.name"
                    :invalid="!!form.errors.name"
                    placeholder="Corte de Cabelo Masculino"
                    @update:modelValue="form.clearErrors('name')" />
                <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
            </div>
            <!-- Description -->
            <div class="flex flex-col gap-1">
                <label for="description" class="font-semibold">Descrição</label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    maxlength="250"
                    :invalid="!!form.errors.description"
                    placeholder="Serviço completo de corte com tesoura, incluindo lavagem e finalização."
                    @update:modelValue="form.clearErrors('description')" />
                <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Price -->
                <div class="flex flex-col gap-1">
                    <label for="price" class="font-semibold">Preço</label>
                    <InputNumber
                        id="price"
                        v-model="form.price"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        :min="0"
                        :max="1000000"
                        :invalid="!!form.errors.price"
                        placeholder="R$ 100,00"
                        @update:modelValue="form.clearErrors('price')" />
                    <small v-if="form.errors.price" class="text-red-500">{{ form.errors.price }}</small>
                </div>
                <!-- Duration -->
                <div class="flex flex-col gap-1">
                    <label for="duration" class="font-semibold">Duração (min)</label>
                    <InputNumber
                        id="duration"
                        v-model="form.duration_minutes"
                        suffix=" min"
                        :min="5"
                        :max="480"
                        :invalid="!!form.errors.duration_minutes"
                        placeholder="45 min"
                        @update:modelValue="form.clearErrors('duration_minutes')" />
                    <small v-if="form.errors.duration_minutes" class="text-red-500">{{ form.errors.duration_minutes }}</small>
                </div>
            </div>
        </form>
        <!-- Dialog Footer -->
        <template #footer>
            <!-- Cancel Button -->
            <Button label="Cancelar" class="p-button-text" @click="closeDialog()" />
            <!-- Dynamic Submit Button -->
            <Button :label="isEditing ? 'Atualizar' : 'Adicionar'" iconPos="right" type="submit" form="service-form" :loading="form.processing" />
        </template>
    </Dialog>
</template>
