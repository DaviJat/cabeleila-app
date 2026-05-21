<script setup>
import { computed } from 'vue';
import Chart from 'primevue/chart';

const props = defineProps({
    data: {
        type: Object,
        required: true,
        default: () => ({ labels: [], values: [] }),
    },
});

const chartData = computed(() => ({
    labels: props.data.labels,
    datasets: [
        {
            label: 'Agendamentos',
            data: props.data.values,
            backgroundColor: '#576f58',
            borderRadius: 6,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
            labels: { boxWidth: 40, boxHeight: 12, useBorderRadius: true, borderRadius: 2 },
        },
    },
    scales: {
        y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
        x: { grid: { display: false } },
    },
};
</script>

<template>
    <div class="bg-white border border-gray-100 shadow-sm p-6 rounded-xl flex flex-col gap-4">
        <h3 class="text-lg font-bold text-gray-700">Serviços Mais Agendados</h3>
        <div class="h-80 w-full">
            <Chart type="bar" :data="chartData" :options="chartOptions" class="h-full w-full" />
        </div>
    </div>
</template>
