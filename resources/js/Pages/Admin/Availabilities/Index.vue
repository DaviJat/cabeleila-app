<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { DatePicker, Panel, Button, Tag } from 'primevue';
import { formatTime } from '@/Utils/formatters';

const props = defineProps({
    availabilities: {
        type: Array,
        required: true,
    },
});

// Controla a data selecionada no calendário (inicia no dia atual)
const selectedDate = ref(new Date());

// Computa a data selecionada para o formato do banco (YYYY-MM-DD)
const selectedDateStr = computed(() => {
    if (!selectedDate.value) return null;

    // O offset previne bugs de fuso horário onde o JS subtrai 1 dia na conversão para ISO
    const offset = selectedDate.value.getTimezoneOffset() * 60000;
    const localDate = new Date(selectedDate.value.getTime() - offset);

    return localDate.toISOString().split('T')[0];
});

// Filtra o array de disponibilidades retornando apenas os horários do dia selecionado
const dailySlots = computed(() => {
    if (!selectedDateStr.value) return [];

    // Filtra onde a data do slot (ex: 2026-05-01T...) começa com a string YYYY-MM-DD computada acima
    return props.availabilities.filter((slot) => slot.date.startsWith(selectedDateStr.value));
});
</script>

<template>
    <Head title="Horários" />
    <AuthenticatedLayout>
        <Panel>
            <template #header>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full px-2 gap-4">
                    <div class="flex flex-col items-start">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Horários</h2>
                        <p class="text-sm text-gray-500">Gerencie seus horários disponíveis</p>
                    </div>
                </div>
            </template>
            <!-- Layout dividido: Calendário na esquerda, Horários na direita -->
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 p-4 md:p-6 border rounded-lg">
                <!-- Calendário Inline -->
                <div class="flex-none flex justify-center w-full lg:w-auto">
                    <DatePicker v-model="selectedDate" inline class="border-none shadow-sm w-full sm:w-auto" />
                </div>
                <!-- Lista de Horários do Dia -->
                <div class="flex-1 w-full">
                    <!-- Cabeçalho da seção com o botão à direita -->
                    <div class="pb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h3 class="text-xl font-semibold text-gray-700">Horários para {{ selectedDate ? selectedDate.toLocaleDateString('pt-BR') : 'selecione uma data' }}</h3>
                        <Button label="Adicionar Horário" icon="pi pi-plus" class="w-full sm:w-auto" @click="openDialog()" />
                    </div>
                    <!-- Estado Vazio -->
                    <div
                        v-if="dailySlots.length === 0"
                        class="text-gray-500 flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-4 bg-gray-50 rounded-md border border-dashed text-center sm:text-left">
                        <i class="pi pi-calendar-times text-2xl sm:text-xl mb-1 sm:mb-0"></i>
                        <span>Nenhum horário cadastrado para este dia.</span>
                    </div>
                    <!-- Grid de Horários -->
                    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                        <div
                            v-for="slot in dailySlots"
                            :key="slot.id"
                            class="border rounded-lg p-3 sm:p-4 flex flex-col items-center justify-center gap-2 sm:gap-3 transition-colors"
                            :class="slot.is_available ? 'bg-white hover:border-primary-400 cursor-pointer' : 'bg-gray-100 opacity-75 cursor-not-allowed'">
                            <span class="text-xl sm:text-2xl font-bold text-gray-700">
                                {{ formatTime(slot.hour) }}
                            </span>
                            <Tag :severity="slot.is_available ? 'success' : 'danger'" :value="slot.is_available ? 'Disponível' : 'Ocupado'" rounded />
                        </div>
                    </div>
                </div>
            </div>
        </Panel>
    </AuthenticatedLayout>
</template>
