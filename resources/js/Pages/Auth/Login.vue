<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { InputText, Password, Checkbox, Button, Dialog } from 'primevue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showDialog = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// Animation for page load
const isLoaded = ref(false);
onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 50);
});
</script>

<template>
    <Head title="Entrar" />
    <!-- Main content -->
    <div class="min-h-screen flex font-sans text-gray-800 overflow-hidden bg-[#FAF8F5]/80 backdrop-blur-[2px]">
        <div
            class="absolute top-8 left-6 sm:left-12 xl:left-24 z-30 transition-all duration-1000 ease-out delay-500"
            :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
            <Link href="/" class="flex items-center text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar ao site
            </Link>
        </div>
        <!-- Login form section -->
        <div
            class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 lg:flex-none lg:w-1/2 xl:px-24 relative z-10 transition-all duration-1000 ease-out bg-[url('/images/background-hero.png')] bg-cover bg-center lg:bg-none lg:bg-[#FAF8F5]"
            :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <div class="mx-auto w-full max-w-sm lg:w-96 mt-12 lg:mt-0 relative z-20">
                <div class="flex items-center gap-3 mb-10">
                    <img src="/images/logo-cabeleila.svg" alt="Logo Cabeleila" class="h-10 w-auto object-contain" />
                </div>
                <!-- Login heading -->
                <h2 class="text-3xl font-bold leading-tight">Bem-vinda de volta</h2>
                <p class="mt-2 text-sm text-gray-600 mb-8">Por favor, insira seus dados para acessar sua conta.</p>
                <!-- Login Form -->
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <!-- E-mail -->
                    <div class="flex flex-col gap-1">
                        <label for="email" class="font-semibold text-sm">E-mail</label>
                        <InputText
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="w-full !p-3 shadow-sm"
                            placeholder="exemplo@email.com"
                            :invalid="!!form.errors.email"
                            @update:modelValue="form.clearErrors('email')"
                            required />
                        <small v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</small>
                    </div>
                    <!-- Password -->
                    <div class="flex flex-col gap-1">
                        <label for="password" class="font-semibold text-sm">Senha</label>
                        <Password
                            id="password"
                            v-model="form.password"
                            :feedback="false"
                            toggleMask
                            class="w-full"
                            inputClass="w-full !p-3 shadow-sm"
                            placeholder="••••••••"
                            :invalid="!!form.errors.password"
                            @update:modelValue="form.clearErrors('password')"
                            required />
                        <small v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</small>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <!-- Remember me -->
                        <div class="flex items-center gap-2">
                            <Checkbox v-model="form.remember" inputId="remember" binary />
                            <label for="remember" class="text-sm font-medium text-gray-700 cursor-pointer">Lembrar-me</label>
                        </div>
                        <!-- Forgot Password -->
                        <Button @click="showDialog = true" class="!p-0 hover:underline" variant="link">Esqueceu a senha?</Button>
                    </div>
                    <!-- Submit Button -->
                    <Button type="submit" label="Entrar na Conta" class="w-full !rounded-full !py-3.5 !font-bold shadow-md mt-2" :loading="form.processing" />
                </form>
            </div>
        </div>
        <div
            class="hidden lg:block relative w-0 flex-1 bg-gray-900 transition-all duration-1000 delay-200 ease-out"
            :class="isLoaded ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'">
            <!-- Background image with gradient overlay -->
            <img class="absolute inset-0 h-full w-full object-cover opacity-90" src="/images/foto-salao-login.png" alt="Ambiente do salão" />
            <!-- Image overlay for gradient effect -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#547558]/80 via-transparent to-transparent"></div>
            <!-- Quote -->
            <div class="absolute bottom-12 left-12 right-12 text-white">
                <blockquote class="space-y-4">
                    <p class="text-2xl font-bold leading-tight">"O cuidado que você merece, a autoestima que você conquista."</p>
                    <footer class="text-sm font-medium text-white/80">Equipe Cabeleila</footer>
                </blockquote>
            </div>
        </div>
        <!-- Dialog for password recovery -->
        <Dialog v-model:visible="showDialog" modal header="Recuperação de Acesso" class="mx-4">
            <div class="flex flex-col gap-4">
                <p class="text-gray-600 text-justify leading-relaxed">Para redefinir sua senha, por favor entre em contato com o <strong>desenvolvedor do projeto</strong>.</p>
                <div class="flex justify-end mt-2">
                    <Button label="Entendi" @click="showDialog = false" class="!px-6 !rounded-full" />
                </div>
            </div>
        </Dialog>
    </div>
</template>
