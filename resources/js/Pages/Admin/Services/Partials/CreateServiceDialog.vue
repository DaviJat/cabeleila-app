<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { Dialog, Button, InputText, InputNumber, Textarea, Toast } from 'primevue';

const props = defineProps({
    visible: Boolean,
});

const toast = useToast();
const page = usePage();

const form = useForm({
    name: '',
    description: '',
    price: null,
    duration_minutes: null,
});

const emit = defineEmits(['close']);

const closeDialog = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const submit = () => {
    form.post(route('admin.services.store'), {
        onSuccess: () => {
            closeDialog();
            const mensagem = page.props.flash?.success || 'Serviço cadastrado com sucesso!';

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
        <template #header>
            <div class="flex flex-col items-start mr-8">
                <h3 class="text-xl font-bold leading-tight text-gray-600">Adicionar Serviço</h3>
                <p class="text-sm text-gray-500">Complete os campos para adicionar um novo serviço.</p>
            </div>
        </template>
        <form id="form-servico" @submit.prevent="submit" class="space-y-4">
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
                <!-- Placeholder alterado para exemplo -->
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
        <template #footer>
            <Button label="Cancelar" class="p-button-text" @click="closeDialog()" />
            <Button label="Salvar" iconPos="right" type="submit" form="form-servico" :loading="form.processing" />
        </template>
    </Dialog>
</template>
