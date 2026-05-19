<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Password, Button } from 'primevue';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
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
        onSuccess: () => {
            form.reset();
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Senha alterada com sucesso!',
                life: 3000,
            });
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                // Focus the new password field if there are errors related to it
                passwordInput.value?.$el.querySelector('input')?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.$el.querySelector('input')?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Atualizar Senha</h2>
            <p class="mt-1 text-sm text-gray-600">Certifique-se de que sua conta esteja usando uma senha segura.</p>
        </header>
        <!-- Update Password Form -->
        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <input type="text" name="username" :value="$page.props.auth.user.email" class="hidden" autocomplete="username" />
            <!-- Current Password -->
            <div class="flex flex-col gap-1">
                <label for="current_password" class="font-semibold text-gray-700">Senha Atual</label>
                <Password
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    :feedback="false"
                    toggleMask
                    class="w-full"
                    inputClass="w-full !p-3 shadow-sm"
                    autocomplete="current-password"
                    placeholder="Digite sua senha atual"
                    :invalid="!!form.errors.current_password"
                    @update:modelValue="form.clearErrors('current_password')" />
                <small v-if="form.errors.current_password" class="text-red-500">{{ form.errors.current_password }}</small>
            </div>
            <!-- New Password -->
            <div class="flex flex-col gap-1">
                <label for="password" class="font-semibold text-gray-700">Nova Senha</label>
                <Password
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    toggleMask
                    class="w-full"
                    inputClass="w-full !p-3 shadow-sm"
                    autocomplete="new-password"
                    placeholder="Mínimo 6 caracteres"
                    :invalid="!!form.errors.password"
                    @update:modelValue="form.clearErrors('password')"
                    promptLabel="Escolha uma senha"
                    weakLabel="Fraca"
                    mediumLabel="Média"
                    strongLabel="Forte">
                </Password>
                <small v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</small>
            </div>
            <!-- Confirm New Password -->
            <div class="flex flex-col gap-1">
                <label for="password_confirmation" class="font-semibold text-gray-700">Confirmar Senha</label>
                <Password
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :feedback="false"
                    toggleMask
                    class="w-full"
                    inputClass="w-full !p-3 shadow-sm"
                    autocomplete="new-password"
                    placeholder="Repita a nova senha"
                    :invalid="!!form.errors.password_confirmation"
                    @update:modelValue="form.clearErrors('password_confirmation')" />
                <small v-if="form.errors.password_confirmation" class="text-red-500">{{ form.errors.password_confirmation }}</small>
            </div>
            <!-- Submit Button -->
            <div class="flex items-center gap-4 mt-6">
                <Button label="Salvar Senha" type="submit" :loading="form.processing" />
            </div>
        </form>
    </section>
</template>
