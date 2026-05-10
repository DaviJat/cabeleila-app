<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { InputText, Button } from 'primevue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Informações do Perfil</h2>
            <p class="mt-1 text-sm text-gray-600">Atualize as informações do perfil e o endereço de e-mail da sua conta.</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div class="flex flex-col gap-2">
                <label for="name" class="font-medium text-gray-700">Nome</label>
                <InputText id="name" type="text" class="w-full" v-model="form.name" required autofocus autocomplete="name" />
                <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="email" class="font-medium text-gray-700">E-mail</label>
                <InputText id="email" type="email" class="w-full" v-model="form.email" required autocomplete="username" />
                <small v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</small>
            </div>

            <div class="flex items-center gap-4 mt-6">
                <Button label="Salvar" type="submit" :loading="form.processing" />

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">Salvo com sucesso.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
