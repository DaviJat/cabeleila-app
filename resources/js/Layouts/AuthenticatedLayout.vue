<script setup>
import { Menubar, Avatar, Button } from 'primevue';

// Definindo os itens do menu
const items = [
    {
        label: 'Painel',
        route: 'dashboard',
    },
    {
        label: 'Agenda',
        route: 'appointments',
    },
    {
        label: 'Clientes',
        route: 'clients',
    },
    {
        label: 'Horários',
        route: 'availabilities.index',
    },
    {
        label: 'Serviços',
        route: 'services.index',
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
                        root: '!py-4 !px-6 justify-between',
                        end: '!ml-0',
                    }">
                    <template #start>
                        <div class="flex items-center">
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
                            <div class="text-right mr-4 hidden md:block">
                                <div class="font-bold text-gray-700 text-sm leading-tight">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                            <Avatar :label="$page.props.auth.user.name.charAt(0)" size="large" shape="circle" class="!bg-[#5a7253] !text-white font-bold" />
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
