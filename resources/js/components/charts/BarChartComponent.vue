<script setup lang="ts">
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip,
} from 'chart.js';
import { Bar } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

interface Props {
    data: {
        labels: string[];
        datasets: {
            label: string;
            data: number[];
            backgroundColor: string[];
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
        <Bar :data="data" :options="options" />
    </div>
</template>
