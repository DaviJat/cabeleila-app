<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Menubar, Avatar, Button, Menu, Toast } from 'primevue'; // <-- Toast adicionado aqui
import { router } from '@inertiajs/vue3';

// Lógica para Toast Responsivo
const toastPosition = ref('top-right');

const updateToastPosition = () => {
    // Se a tela for menor que 768px (mobile), fica no centro. Senão, na direita.
    toastPosition.value = window.innerWidth < 768 ? 'top-center' : 'top-right';
};

onMounted(() => {
    updateToastPosition();
    window.addEventListener('resize', updateToastPosition);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateToastPosition);
});

// Definindo os itens do menu de navegação
const items = [
    { label: 'Painel', route: 'dashboard' },
    { label: 'Agenda', route: 'appointments' },
    { label: 'Clientes', route: 'clients' },
    { label: 'Horários', route: 'availabilities.index' },
    { label: 'Serviços', route: 'services.index' },

    // --- Opções exclusivas do mobile (md:hidden) ---
    { separator: true, class: 'md:hidden' }, // Linha divisória
    { isProfileHeader: true, class: 'md:hidden' }, // Bloco de texto com nome e email
    { label: 'Meu Perfil', route: 'profile.edit', icon: 'pi pi-user', class: 'md:hidden' },
    { label: 'Sair', action: 'logout', icon: 'pi pi-sign-out', class: 'md:hidden' },
];

// Ref para o componente Menu Desktop
const profileMenu = ref();

const toggleProfileMenu = (event) => {
    profileMenu.value.toggle(event);
};

// Itens do dropdown de perfil (Desktop)
const profileItems = [
    {
        label: 'Meu Perfil',
        icon: 'pi pi-user',
        command: () => router.get(route('profile.edit')),
    },
    { separator: true },
    {
        label: 'Sair',
        icon: 'pi pi-sign-out',
        command: () => router.post(route('logout')),
    },
];

// Gerencia a navegação
const handleNavigation = (item) => {
    if (item.route) {
        router.get(route(item.route));
    } else if (item.action === 'logout') {
        router.post(route('logout'));
    }
};
</script>

<template>
    <Toast :position="toastPosition" />
    <div class="min-h-screen flex flex-col bg-[#faf8f5]">
        <div class="max-w-7xl w-full mx-auto flex flex-col px-4 sm:px-6 lg:px-8 gap-6 py-4 md:py-6">
            <header>
                <!-- Menu de navegação -->
                <Menubar
                    :model="items"
                    breakpoint="767px"
                    :pt="{
                        root: 'justify-between relative !p-4',
                        button: '!ml-auto !mr-2 !size-12 !rounded-lg !bg-gray-100 flex items-center justify-center',
                        end: '!ml-0 hidden md:flex',
                    }">
                    <template #start>
                        <div class="flex items-center px-2 py-1 md:py-0">
                            <img src="/images/logo-cabeleila.svg" alt="Logo" class="h-10 md:h-12 w-auto" />
                            <div class="hidden md:block h-10 w-px bg-gray-200 ml-4" />
                        </div>
                    </template>
                    <!-- Itens do menu (Desktop e Mobile) -->
                    <template #item="{ item, props }">
                        <div v-if="item.separator" class="border-t border-gray-100 my-2 mx-3 md:hidden"></div>
                        <!-- Bloco de perfil no menu mobile -->
                        <div v-else-if="item.isProfileHeader" class="p-4 flex items-center gap-4 bg-gray-50 rounded-xl mx-1border border-gray-100 md:hidden">
                            <Avatar :label="$page.props.auth.user.name.charAt(0)" size="large" shape="circle" class="!bg-[#5a7253] !text-white font-bold flex-none" />
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-gray-800 text-base leading-tight truncate">
                                    {{ $page.props.auth.user.name }}
                                </span>
                                <span class="text-xs text-gray-500 mt-0.5 truncate">
                                    {{ $page.props.auth.user.email }}
                                </span>
                            </div>
                        </div>
                        <!-- Itens de navegação -->
                        <Button
                            v-else
                            :variant="item.action === 'logout' ? 'text' : 'link'"
                            :severity="item.action === 'logout' ? 'danger' : 'secondary'"
                            @click="handleNavigation(item)"
                            v-bind="props.action"
                            class="w-full !justify-start !no-underline">
                            <i v-if="item.icon" :class="[item.icon, 'mr-2', item.action === 'logout' ? 'text-red-500' : 'text-gray-500']"></i>

                            <span
                                :class="[
                                    'font-medium py-1',
                                    item.route && route().current(item.route) ? 'text-primary !border-b-2 border-primary' : '',
                                    item.action === 'logout' ? 'text-red-500' : 'text-gray-600',
                                ]">
                                {{ item.label }}
                            </span>
                        </Button>
                    </template>
                    <!-- Menu de perfil (Desktop) -->
                    <template #end>
                        <div class="flex items-center">
                            <div class="h-10 w-px bg-gray-200 mr-4" />
                            <!-- Botão do menu de perfil -->
                            <Button @click="toggleProfileMenu" aria-haspopup="true" variant="text" aria-controls="overlay_menu">
                                <div class="text-right hidden lg:block mr-3">
                                    <div class="font-bold text-gray-700 text-sm leading-tight">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>

                                <Avatar :label="$page.props.auth.user.name.charAt(0)" size="large" shape="circle" class="!bg-[#5a7253] !text-white font-bold" />
                                <i class="pi pi-angle-down text-gray-500 text-sm ml-2"></i>
                            </Button>
                            <!-- Menu de perfil -->
                            <Menu ref="profileMenu" id="overlay_menu" :model="profileItems" :popup="true">
                                <template #item="{ item, props }">
                                    <Button
                                        variant="text"
                                        :severity="item.label === 'Sair' ? 'danger' : 'secondary'"
                                        :icon="item.icon"
                                        :label="item.label"
                                        class="w-full !justify-start"
                                        @click="(event) => props.action.onClick(event)" />
                                </template>
                            </Menu>
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
