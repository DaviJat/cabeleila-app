<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { InputText, Button, Dialog } from 'primevue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    // Wait for the dialog to open before focusing the password input
    nextTick(() => passwordInput.value.$el.focus());
};

const deleteUser = () => {
    form.delete(route('perfil.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.$el.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">Excluir Conta</h2>
            <p class="mt-1 text-sm text-gray-600">
                Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Antes de excluir sua conta, faça o download de quaisquer dados ou
                informações que você deseja reter.
            </p>
        </header>
        <Button label="Excluir Conta" severity="danger" @click="confirmUserDeletion" />
        <!-- Delete User Confirmation Dialog -->
        <Dialog v-model:visible="confirmingUserDeletion" modal header="Tem certeza de que deseja excluir sua conta?" :style="{ width: '35rem' }" @hide="closeModal">
            <p class="text-sm text-gray-600 mb-6">
                Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Por favor, digite sua senha para confirmar que você gostaria de
                excluir permanentemente sua conta.
            </p>
            <!-- Password -->
            <div class="flex flex-col gap-2">
                <label for="password" class="sr-only">Senha</label>
                <InputText id="password" ref="passwordInput" v-model="form.password" type="password" class="w-full" placeholder="Sua senha" @keyup.enter="deleteUser" />
                <small v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</small>
            </div>
            <!-- Footer -->
            <template #footer>
                <div class="flex justify-end gap-3 mt-4">
                    <!-- Cancel Button -->
                    <Button label="Cancelar" severity="secondary" text @click="closeModal" />
                    <!-- Delete Button -->
                    <Button label="Excluir Conta" severity="danger" :loading="form.processing" @click="deleteUser" />
                </div>
            </template>
        </Dialog>
    </section>
</template>
