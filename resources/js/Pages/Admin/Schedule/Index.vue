<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { DatePicker, Panel, Button, Tag, ConfirmDialog, useConfirm } from 'primevue';
import { formatTime } from '@/Utils/formatters';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SlotManagerDialog from '@/Pages/Admin/Schedule/Partials/SlotManagerDialog.vue';

const confirm = useConfirm();

const displayDialog = ref(false);
const selectedAvailability = ref(null);

const openDialog = (availability = null) => {
    selectedAvailability.value = availability; // If availability is null, it will open the dialog in create mode; otherwise, it will be in edit mode
    displayDialog.value = true;
};

const props = defineProps({
    availabilities: { type: Array, required: true },
    clients: { type: Array, required: true },
    services: { type: Array, required: true },
});

// Confirm Dialog for deleting an availability slot
const deleteAvailability = (id) => {
    confirm.require({
        header: 'Confirmar Exclusão',
        message: 'Tem certeza que deseja excluir este horário?',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, Excluir',
        rejectLabel: 'Cancelar',
        rejectProps: { severity: 'secondary', variant: 'outlined' },
        acceptProps: { severity: 'danger' },
        accept: () => {
            router.delete(route('availabilities.destroy', id), {
                preserveScroll: true,
            });
        },
    });
};

// State for selected date in the DatePicker
const selectedDate = ref(new Date());

// Used to disable Add Availability Button for past dates
const isPastDate = computed(() => {
    if (!selectedDate.value) return true;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const selected = new Date(selectedDate.value);
    selected.setHours(0, 0, 0, 0);
    return selected < today;
});

// Converts selectedDate to 'YYYY-MM-DD' format for comparison with slot dates
const selectedDateStr = computed(() => {
    if (!selectedDate.value) return null;
    const offset = selectedDate.value.getTimezoneOffset() * 60000;
    const localDate = new Date(selectedDate.value.getTime() - offset);
    return localDate.toISOString().split('T')[0];
});

// Filters availabilities to show only those matching the selected date
const dailySlots = computed(() => {
    if (!selectedDateStr.value) return [];
    return props.availabilities.filter((slot) => slot.date.startsWith(selectedDateStr.value));
});
</script>

<template>
    <Head title="Agenda" />
    <AuthenticatedLayout>
        <ConfirmDialog class="mx-4" />
        <Panel>
            <template #header>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full px-2 gap-4">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Agenda Geral</h2>
                        <p class="text-sm text-gray-500">Gerencie a agenda e os agendamentos do salão</p>
                    </div>
                </div>
            </template>
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 sm:p-4 md:p-6 sm:border rounded-lg">
                <!-- Date Picker -->
                <div class="flex-none flex justify-center w-full lg:w-auto">
                    <DatePicker v-model="selectedDate" inline class="border-none shadow-sm w-full sm:w-auto" />
                </div>
                <!-- Schedule Slots -->
                <div class="flex-1 w-full">
                    <div class="pb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex flex-col items-start">
                            <h3 class="text-xl font-semibold text-gray-700">Horários ({{ selectedDate ? selectedDate.toLocaleDateString('pt-BR') : 'selecione uma data' }})</h3>
                            <p class="text-sm text-gray-500">Clique no card para gerenciar o horário e fazer agendamentos.</p>
                        </div>
                        <!-- Add Availability Button -->
                        <Button label="Adicionar Horário" icon="pi pi-plus" class="w-full sm:w-auto" @click="openDialog()" :disabled="isPastDate" />
                    </div>
                    <!-- No Slots Message -->
                    <div
                        v-if="dailySlots.length === 0"
                        class="text-gray-500 flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-4 bg-gray-50 rounded-md border border-dashed text-center sm:text-left">
                        <i class="pi pi-calendar-times text-2xl sm:text-xl mb-1 sm:mb-0"></i>
                        <span>Nenhum horário cadastrado para este dia.</span>
                    </div>
                    <!-- Daily Slots -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div
                            v-for="slot in dailySlots"
                            :key="slot.id"
                            @click="openDialog(slot)"
                            class="relative border rounded-lg p-4 flex flex-col items-center justify-center gap-3 transition-colors h-full min-h-[140px] cursor-pointer"
                            :class="slot.status.is_blocked ? 'bg-gray-50 border-gray-200 opacity-90' : 'bg-white hover:border-primary-400'">
                            <Button
                                v-if="slot.is_available"
                                icon="pi pi-times"
                                severity="secondary"
                                variant="text"
                                rounded
                                aria-label="Excluir"
                                class="!absolute top-2 right-2 !w-8 !h-8 !p-0 text-gray-400 hover:text-red-500"
                                @click.stop="deleteAvailability(slot.id)" />
                            <div class="flex flex-col items-center justify-center w-full mt-2">
                                <span class="text-3xl font-bold text-gray-700">
                                    {{ formatTime(slot.hour) }}
                                </span>
                                <Tag v-if="slot.status" :severity="slot.status.severity" :value="slot.status.label" rounded class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Panel>
        <!-- Dialog for create/edit availability slots -->
        <SlotManagerDialog
            :visible="displayDialog"
            :date="selectedDate"
            :availability="selectedAvailability"
            :clients="clients"
            :services="services"
            @close="displayDialog = false" />
    </AuthenticatedLayout>
</template>
