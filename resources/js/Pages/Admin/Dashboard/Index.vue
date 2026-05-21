<script setup>
import { onMounted, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Panel, DatePicker, Button } from 'primevue';
import { getWeeklyRange, getMonthlyRange, getYearlyRange, getTodayDate, getAllTimeRange } from '@/Utils/datePresets';
import SummaryCards from './Partials/SummaryCards.vue';
import RevenueEvolutionChart from './Partials/RevenueEvolutionChart.vue';
import TopServicesChart from './Partials/TopServicesChart.vue';

const props = defineProps({
    summaryData: { type: Object, required: true },
    revenueTimeline: { type: Object, required: true },
    topServices: { type: Object, required: true },
});

const dateRange = ref(getWeeklyRange()); // Default to weekly range

onMounted(() => {
    dateRange.value = getWeeklyRange();
});

watch(dateRange, (newRange) => {
    // Ensure the range array exists before extracting parameters
    if (newRange) {
        let start_date = null;
        let end_date = null;

        // If a start date is chosen, parse and map the boundaries safely
        if (newRange[0]) {
            const startDateObj = newRange[0];
            const endDateObj = newRange[1] ? newRange[1] : newRange[0];

            const formattedRange = [startDateObj, endDateObj].map((date) => {
                const offset = date.getTimezoneOffset() * 60000;
                return new Date(date.getTime() - offset).toISOString().split('T')[0];
            });

            start_date = formattedRange[0];
            end_date = formattedRange[1];
        }

        // Send the formatted date range to the server to fetch the updated dashboard data
        router.get(route('dashboard'), { start_date, end_date }, { preserveState: true, replace: true, preserveScroll: true });
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Painel" />
        <!-- Date Range Selector -->
        <Panel
            :pt="{
                header: '!pb-0',
            }">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="w-full md:w-auto px-2">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-bold leading-tight text-gray-600">Painel</h2>
                        <p class="text-sm text-gray-500">Selecione o período desejado para visualizar os dados</p>
                    </div>
                </div>
                <!-- Date range buttons and picker -->
                <div class="flex flex-col sm:flex-row flex-wrap md:justify-end items-stretch sm:items-center gap-3 w-full md:w-auto">
                    <div class="grid grid-cols-3 sm:flex sm:flex-row gap-2">
                        <Button variant="outlined" label="Tudo" size="small" @click="dateRange = getAllTimeRange()" class="sm:px-3" />
                        <Button variant="outlined" label="Hoje" size="small" @click="dateRange = getTodayDate()" class="sm:px-3" />
                        <Button variant="outlined" label="Semana" size="small" @click="dateRange = getWeeklyRange()" class="sm:px-3" />
                        <Button variant="outlined" label="Mês" size="small" @click="dateRange = getMonthlyRange()" class="sm:px-3" />
                        <Button variant="outlined" label="Ano" size="small" @click="dateRange = getYearlyRange()" class="sm:px-3" />
                    </div>
                    <div class="hidden sm:block h-8 w-px bg-gray-200 mx-1" />
                    <DatePicker v-model="dateRange" selectionMode="range" dateFormat="dd/mm/yy" showIcon class="w-full sm:w-64" />
                </div>
            </div>
        </Panel>
        <!-- Summary Cards -->
        <SummaryCards :data="props.summaryData" />
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">
            <RevenueEvolutionChart :data="props.revenueTimeline" />
            <TopServicesChart :data="props.topServices" />
        </div>
    </AuthenticatedLayout>
</template>
