<script setup>
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

defineProps({
    chartData: {
        type: Object,
        required: true
    }
});

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                usePointStyle: true,
                padding: 20
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    return '$' + value.toLocaleString();
                }
            }
        }
    }
};
</script>

<template>
    <div style="height: 300px">
        <Bar 
            :data="chartData"
            :options="options"
        />
    </div>
</template>
