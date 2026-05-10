<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { InputText, Button } from 'primevue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.$el.focus(); // Ajuste para focar no PrimeVue
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.$el.focus(); // Ajuste para focar no PrimeVue
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Atualizar Senha</h2>
            <p class="mt-1 text-sm text-gray-600">Certifique-se de que sua conta esteja usando uma senha longa e aleatória para se manter segura.</p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div class="flex flex-col gap-2">
                <label for="current_password" class="font-medium text-gray-700">Senha Atual</label>
                <InputText id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="w-full" autocomplete="current-password" />
                <small v-if="form.errors.current_password" class="text-red-500">{{ form.errors.current_password }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="password" class="font-medium text-gray-700">Nova Senha</label>
                <InputText id="password" ref="passwordInput" v-model="form.password" type="password" class="w-full" autocomplete="new-password" />
                <small v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="font-medium text-gray-700">Confirmar Senha</label>
                <InputText id="password_confirmation" v-model="form.password_confirmation" type="password" class="w-full" autocomplete="new-password" />
                <small v-if="form.errors.password_confirmation" class="text-red-500">{{ form.errors.password_confirmation }}</small>
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
