<script setup lang="ts">
import {
    CategoryScale,
    Chart as ChartJS,
    Filler,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

interface Props {
    data: {
        labels: string[];
        datasets: {
            label: string;
            data: number[];
            borderColor: string;
            backgroundColor: string;
            tension: number;
        }[];
    };
}

defineProps<Props>();

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            mode: 'index' as const,
            intersect: false,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0,
            },
        },
    },
};
</script>

<template>
    <div class="h-[300px]">
        <Line :data="data" :options="options" />
    </div>
</template>
