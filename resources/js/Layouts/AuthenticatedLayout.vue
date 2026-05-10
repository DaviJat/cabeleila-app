<script setup>
import { ref } from 'vue';
import { Menubar, Avatar, Button, Menu } from 'primevue';
import { router } from '@inertiajs/vue3';

// Definindo os itens do menu de navegação
const items = [
    { label: 'Painel', route: 'dashboard' },
    { label: 'Agenda', route: 'appointments' },
    { label: 'Clientes', route: 'clients' },
    { label: 'Horários', route: 'availabilities.index' },
    { label: 'Serviços', route: 'services.index' },
];

// Ref para o componente Menu
const profileMenu = ref();

// Função para abrir/fechar o menu
const toggleProfileMenu = (event) => {
    profileMenu.value.toggle(event);
};

// Definindo os itens do dropdown de perfil
const profileItems = [
    {
        label: 'Meu Perfil',
        icon: 'pi pi-user',
        command: () => {
            router.get(route('profile.edit'));
        },
    },
    { separator: true },
    {
        label: 'Sair',
        icon: 'pi pi-sign-out',
        command: () => {
            router.post(route('logout'));
        },
    },
];
</script>

<template>
    <!-- Layout principal -->
    <div class="min-h-screen flex flex-col bg-[#faf8f5]">
        <div class="max-w-7xl w-full mx-auto flex flex-col sm:px-6 lg:px-8 gap-6 py-6">
            <!-- Cabeçalho -->
            <header>
                <Menubar
                    :model="items"
                    :pt="{
                        root: 'justify-between',
                        end: '!ml-0',
                    }">
                    <template #start>
                        <div class="flex items-center px-3 py-2">
                            <img src="/images/logo-cabeleila.svg" alt="Logo" class="h-12 w-auto" />
                            <div class="h-10 w-px bg-gray-200 ml-4" />
                        </div>
                    </template>

                    <template #item="{ item, props }">
                        <Button variant="link" @click="() => $inertia.visit(route(item.route))" v-bind="props.action">
                            <span :class="{ 'text-primary border-b-2 border-primary': route().current(item.route) }">
                                {{ item.label }}
                            </span>
                        </Button>
                    </template>

                    <template #end>
                        <div class="flex items-center">
                            <div class="h-10 w-px bg-gray-200 mr-4" />

                            <!-- Área clicável do perfil -->
                            <Button @click="toggleProfileMenu" aria-haspopup="true" variant="text" aria-controls="overlay_menu">
                                <div class="text-right hidden md:block">
                                    <div class="font-bold text-gray-700 text-sm leading-tight">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                                <Avatar :label="$page.props.auth.user.name.charAt(0)" size="large" shape="circle" class="!bg-[#5a7253] !text-white font-bold" />

                                <!-- Ícone de seta para indicar o dropdown -->
                                <i class="pi pi-angle-down text-gray-500 text-sm"></i>
                            </Button>

                            <!-- Componente Menu Popup -->
                            <Menu ref="profileMenu" id="overlay_menu" :model="profileItems" :popup="true" />
                        </div>
                    </template>
                </Menubar>
            </header>

            <!-- Conteúdo principal -->
            <main class="flex-1">
                <slot />
            </main>
        </div>

        <!-- Rodapé -->
        <footer class="w-full border-t border-gray-200/50 bg-white/40 backdrop-blur-md mt-auto relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex justify-center items-center">
                <p class="flex items-center justify-center text-sm text-gray-500 font-medium text-center">
                    &copy; {{ new Date().getFullYear() }} Cabeleila. Todos os direitos reservados.
                </p>
            </div>
        </footer>
    </div>
</template>
