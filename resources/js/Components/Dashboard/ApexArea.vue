<template>
    <div ref="chartEl" class="w-full" :style="{ height: height + 'px' }"/>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { useDark } from '@vueuse/core';
import ApexCharts from 'apexcharts';

interface Series { name: string; data: number[]; color?: string }

interface Props {
    series: Series[];
    categories: string[];
    height?: number;
    colors?: string[];
    smooth?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    height: 220,
    colors: () => ['#7C3AED', '#3B82F6', '#10B981', '#F59E0B'],
    smooth: true,
});

const chartEl = ref<HTMLElement | null>(null);
const isDark  = useDark();
let chart: ApexCharts | null = null;

const buildOptions = () => ({
    chart: {
        type: 'area',
        background: 'transparent',
        toolbar: { show: false },
        animations: { enabled: true, easing: 'easeinout', speed: 600 },
        sparkline: { enabled: false },
    },
    stroke: { curve: props.smooth ? 'smooth' : 'straight', width: 2 },
    fill:   { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
    series:     props.series,
    xaxis: {
        categories: props.categories,
        labels: { style: { colors: isDark.value ? '#9ca3af' : '#6b7280', fontSize: '11px' } },
        axisBorder: { show: false }, axisTicks: { show: false },
    },
    yaxis: { labels: { style: { colors: isDark.value ? '#9ca3af' : '#6b7280', fontSize: '10px' } } },
    colors: props.colors,
    theme:  { mode: isDark.value ? 'dark' : 'light' },
    dataLabels: { enabled: false },
    grid: {
        borderColor: isDark.value ? '#374151' : '#f3f4f6',
        strokeDashArray: 4, yaxis: { lines: { show: true } },
    },
    markers: { size: 4, strokeWidth: 2, hover: { size: 6 } },
    legend: {
        position: 'top', horizontalAlign: 'right', fontSize: '11px',
        labels: { colors: isDark.value ? '#9ca3af' : '#6b7280' },
    },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (v: number) => v.toLocaleString('fr-FR') },
    },
});

onMounted(() => {
    if (!chartEl.value) return;
    chart = new ApexCharts(chartEl.value, buildOptions());
    chart.render();
});

watch([() => props.series, () => props.categories, isDark], () => {
    chart?.destroy();
    if (!chartEl.value) return;
    chart = new ApexCharts(chartEl.value, buildOptions());
    chart.render();
});

onBeforeUnmount(() => chart?.destroy());
</script>
