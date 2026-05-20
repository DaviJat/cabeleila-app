<script setup>
import { watch, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { Dialog, Button, InputText, Textarea, InputMask } from 'primevue';

const props = defineProps({
    visible: Boolean,
    client: {
        type: Object,
    },
});

const toast = useToast();
const page = usePage();

const emit = defineEmits(['close']);

const isEditing = computed(() => !!props.client?.id);

const form = useForm({
    id: null,
    full_name: '',
    phone: '',
    email: '',
    cpf: '',
    birth_date: '',
    postal_code: '',
    street: '',
    number: '',
    complement: '',
    neighborhood: '',
    city: '',
    state: '',
    notes: '',
});

watch(
    () => props.visible,
    (isOpen) => {
        if (isOpen) {
            if (props.client) {
                form.id = props.client.id;
                form.full_name = props.client.full_name || '';
                form.phone = props.client.phone || '';
                form.email = props.client.email || '';
                form.cpf = props.client.cpf || '';
                form.birth_date = props.client.birth_date ? props.client.birth_date.split('T')[0] : '';
                form.postal_code = props.client.postal_code || '';
                form.street = props.client.street || '';
                form.number = props.client.number || '';
                form.complement = props.client.complement || '';
                form.neighborhood = props.client.neighborhood || '';
                form.city = props.client.city || '';
                form.state = props.client.state || '';
                form.notes = props.client.notes || '';
            } else {
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
    const formPayload = form.transform((data) => ({
        ...data,
        phone: data.phone ? data.phone.replace(/\D/g, '') : '',
        cpf: data.cpf ? data.cpf.replace(/\D/g, '') : '',
        postal_code: data.postal_code ? data.postal_code.replace(/\D/g, '') : '',
    }));

    formPayload.post(route('clients.store'), {
        onSuccess: () => {
            closeDialog();
            const defaultMessage = isEditing.value ? 'Cliente atualizado com sucesso!' : 'Cliente cadastrado com sucesso!';
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
        <template #header>
            <div class="flex flex-col items-start mr-8">
                <h3 class="text-xl font-bold leading-tight text-gray-600">
                    {{ isEditing ? 'Editar Cliente' : 'Adicionar Cliente' }}
                </h3>
                <p class="text-xs text-gray-500">
                    {{ isEditing ? 'Atualize os dados cadastrais do cliente.' : 'Preencha as informações para registrar um novo cliente.' }}
                </p>
            </div>
        </template>

        <form id="client-form" @submit.prevent="submit" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 max-h-[65vh] overflow-y-auto pr-4">
            <div class="flex flex-col gap-1 sm:col-span-2">
                <label for="full_name" class="font-semibold">Nome Completo <span class="text-red-500">*</span></label>
                <InputText
                    id="full_name"
                    v-model="form.full_name"
                    maxlength="100"
                    :invalid="!!form.errors.full_name"
                    placeholder="Ex: Ana Silva"
                    @update:modelValue="form.clearErrors('full_name')" />
                <small v-if="form.errors.full_name" class="text-red-500">{{ form.errors.full_name }}</small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="phone" class="font-semibold">Telefone <span class="text-red-500">*</span></label>
                <InputMask
                    id="phone"
                    v-model="form.phone"
                    mask="(99) 99999-9999"
                    :invalid="!!form.errors.phone"
                    placeholder="(11) 99999-9999"
                    @update:modelValue="form.clearErrors('phone')" />
                <small v-if="form.errors.phone" class="text-red-500">{{ form.errors.phone }}</small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="email" class="font-semibold">E-mail</label>
                <InputText
                    id="email"
                    type="email"
                    maxlength="100"
                    v-model="form.email"
                    :invalid="!!form.errors.email"
                    placeholder="joao@exemplo.com"
                    @update:modelValue="form.clearErrors('email')" />
                <small v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="cpf" class="font-semibold">CPF</label>
                <InputMask
                    id="cpf"
                    v-model="form.cpf"
                    mask="999.999.999-99"
                    :invalid="!!form.errors.cpf"
                    placeholder="000.000.000-00"
                    @update:modelValue="form.clearErrors('cpf')" />
                <small v-if="form.errors.cpf" class="text-red-500">{{ form.errors.cpf }}</small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="birth_date" class="font-semibold">Data de Nascimento</label>
                <InputText id="birth_date" type="date" v-model="form.birth_date" :invalid="!!form.errors.birth_date" @update:modelValue="form.clearErrors('birth_date')" />
                <small v-if="form.errors.birth_date" class="text-red-500">{{ form.errors.birth_date }}</small>
            </div>

            <div class="sm:col-span-2 border-t pt-2 mt-2">
                <h4 class="text-sm font-bold text-gray-500">Endereço</h4>
            </div>

            <div class="flex flex-col gap-1">
                <label for="postal_code" class="font-semibold">CEP</label>
                <InputMask
                    id="postal_code"
                    v-model="form.postal_code"
                    mask="99999-999"
                    :invalid="!!form.errors.postal_code"
                    placeholder="00000-000"
                    @update:modelValue="form.clearErrors('postal_code')" />
                <small v-if="form.errors.postal_code" class="text-red-500">{{ form.errors.postal_code }}</small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="street" class="font-semibold">Rua</label>
                <InputText
                    id="street"
                    v-model="form.street"
                    maxlength="100"
                    :invalid="!!form.errors.street"
                    placeholder="Rua das Flores"
                    @update:modelValue="form.clearErrors('street')" />
                <small v-if="form.errors.street" class="text-red-500">{{ form.errors.street }}</small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="number" class="font-semibold">Número</label>
                <InputText id="number" v-model="form.number" maxlength="10" :invalid="!!form.errors.number" placeholder="123" @update:modelValue="form.clearErrors('number')" />
                <small v-if="form.errors.number" class="text-red-500">{{ form.errors.number }}</small>
            </div>

            <div class="flex flex-col gap-1">
                <label for="complement" class="font-semibold">Complemento</label>
                <InputText
                    id="complement"
                    v-model="form.complement"
                    maxlength="50"
                    :invalid="!!form.errors.complement"
                    placeholder="Apt 45, Bloco B"
                    @update:modelValue="form.clearErrors('complement')" />
                <small v-if="form.errors.complement" class="text-red-500">{{ form.errors.complement }}</small>
            </div>

            <div class="flex-col sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="neighborhood" class="font-semibold">Bairro</label>
                    <InputText
                        id="neighborhood"
                        v-model="form.neighborhood"
                        maxlength="50"
                        placeholder="Centro"
                        :invalid="!!form.errors.neighborhood"
                        @update:modelValue="form.clearErrors('neighborhood')" />
                    <small v-if="form.errors.neighborhood" class="text-red-500">{{ form.errors.neighborhood }}</small>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="city" class="font-semibold">Cidade</label>
                    <InputText
                        id="city"
                        placeholder="Feira de Santana"
                        v-model="form.city"
                        maxlength="50"
                        :invalid="!!form.errors.city"
                        @update:modelValue="form.clearErrors('city')" />
                    <small v-if="form.errors.city" class="text-red-500">{{ form.errors.city }}</small>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="state" class="font-semibold">Estado</label>
                    <InputText id="state" placeholder="Bahia" v-model="form.state" maxlength="50" :invalid="!!form.errors.state" @update:modelValue="form.clearErrors('state')" />
                    <small v-if="form.errors.state" class="text-red-500">{{ form.errors.state }}</small>
                </div>
            </div>

            <div class="flex flex-col gap-1 sm:col-span-2 pt-2 border-t mt-2">
                <label for="notes" class="font-semibold">Anotações Internas</label>
                <Textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    maxlength="255"
                    :invalid="!!form.errors.notes"
                    placeholder="Informações adicionais relevantes sobre o cliente."
                    @update:modelValue="form.clearErrors('notes')" />
                <small v-if="form.errors.notes" class="text-red-500">{{ form.errors.notes }}</small>
            </div>
        </form>

        <template #footer>
            <Button label="Cancelar" class="p-button-text" @click="closeDialog()" />
            <Button :label="isEditing ? 'Atualizar' : 'Salvar Cliente'" iconPos="right" type="submit" form="client-form" :loading="form.processing" />
        </template>
    </Dialog>
</template>
