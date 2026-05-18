<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { InputText, Button } from 'primevue';
import { useToast } from 'primevue/usetoast'; // Adicionado

const toast = useToast();
const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const submit = () => {
    form.patch(route('perfil.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Informações do perfil atualizadas!',
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Informações do Perfil</h2>
            <p class="mt-1 text-sm text-gray-600">Atualize as informações do perfil e o endereço de e-mail da sua conta.</p>
        </header>
        <!-- Formulário de atualização de informações do perfil -->
        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <!-- Nome -->
            <div class="flex flex-col gap-2">
                <label for="name" class="font-medium text-gray-700">Nome</label>
                <InputText id="name" type="text" class="w-full" v-model="form.name" required autofocus autocomplete="name" placeholder="Seu nome completo" />
                <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
            </div>
            <!-- E-mail -->
            <div class="flex flex-col gap-2">
                <label for="email" class="font-medium text-gray-700">E-mail</label>
                <InputText id="email" type="email" class="w-full" v-model="form.email" required autocomplete="username" placeholder="exemplo@email.com" />
                <small v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</small>
            </div>
            <!-- Botão de envio -->
            <div class="flex items-center gap-4 mt-6">
                <Button label="Salvar" type="submit" :loading="form.processing" />
            </div>
        </form>
    </section>
</template>
