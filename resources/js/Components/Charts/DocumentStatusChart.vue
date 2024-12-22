<script setup>
import { Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

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
    cutout: '60%'
};
</script>

<template>
    <div style="height: 300px">
        <Doughnut 
            :data="chartData"
            :options="options"
        />
    </div>
</template>