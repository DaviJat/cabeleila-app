<script setup>
import { Menubar } from 'primevue';
import { Link } from '@inertiajs/vue3';

const items = [
    {
        label: 'Painel',
        to: 'admin.dashboard',
    },
    {
        label: 'Agenda',
        to: 'admin.appointments',
    },
    {
        label: 'Clientes',
        to: 'admin.clients',
    },
    {
        label: 'Horários',
        to: 'admin.availabilities',
    },
    {
        label: 'Serviços',
        to: 'admin.services',
    },
];
</script>

<template>
    <div class="min-h-screen bg">
        <div class="max-w-7xl mx-auto flex flex-col sm:px-6 lg:px-8 gap-6 py-6 bg-red-500">
            <header>
                <Menubar :model="items">
                    <template #start></template>
                    <template #item="{ item }">
                        <Link :href="route(item.to)">{{ item.label }}</Link>
                    </template>
                    <template #end></template>
                </Menubar>
            </header>
            <main class="flex-1 bg-green-500">
                <slot />
            </main>
        </div>
        <footer class="bg-blue-500">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">footer</div>
        </footer>
    </div>
</template>
