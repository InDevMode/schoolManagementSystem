<template>
    <div ref="chartEl" class="w-full" :style="{ height: height + 'px' }"/>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { useDark } from '@vueuse/core';
import ApexCharts from 'apexcharts';

interface Props {
    series: number[];      // pourcentages 0–100
    labels: string[];
    colors?: string[];
    height?: number;
}

const props = withDefaults(defineProps<Props>(), {
    height: 220,
    colors: () => ['#7C3AED', '#3B82F6', '#10B981', '#F59E0B'],
});

const chartEl = ref<HTMLElement | null>(null);
const isDark  = useDark();
let chart: ApexCharts | null = null;

const buildOptions = () => ({
    chart: {
        type: 'radialBar',
        height: props.height,
        background: 'transparent',
        toolbar: { show: false },
        animations: { enabled: true, easing: 'easeinout', speed: 700 },
    },
    series: props.series,
    labels: props.labels,
    colors: props.colors,
    theme:  { mode: isDark.value ? 'dark' : 'light' },
    plotOptions: {
        radialBar: {
            offsetY: -10,
            startAngle: -135,
            endAngle: 135,
            hollow: {
                size: '40%',
                background: 'transparent',
                margin: 8,
            },
            track: { background: isDark.value ? '#374151' : '#f3f4f6', strokeWidth: '80%', margin: 4 },
            dataLabels: {
                name: {
                    fontSize: '11px',
                    offsetY: -6,
                    color: isDark.value ? '#9ca3af' : '#6b7280',
                },
                value: {
                    fontSize: '22px',
                    fontWeight: 800,
                    offsetY: 8,
                    color: isDark.value ? '#f9fafb' : '#111827',
                    formatter: (v: number) => v + '%',
                },
                total: {
                    show: props.series.length > 1,
                    label: 'Moy.',
                    fontSize: '11px',
                    fontWeight: 600,
                    color: isDark.value ? '#9ca3af' : '#6b7280',
                    formatter: () => Math.round(props.series.reduce((a, b) => a + b, 0) / props.series.length) + '%',
                },
            },
        },
    },
    stroke: { lineCap: 'round' },
    legend: {
        show: true,
        position: 'bottom',
        fontSize: '11px',
        offsetY: 8,
        labels: { colors: isDark.value ? '#9ca3af' : '#6b7280' },
        markers: { width: 10, height: 10, radius: 5 },
        itemMargin: { horizontal: 8, vertical: 4 },
    },
    tooltip: { theme: isDark.value ? 'dark' : 'light', y: { formatter: (v: number) => v + '%' } },
});

onMounted(() => {
    if (!chartEl.value) return;
    chart = new ApexCharts(chartEl.value, buildOptions());
    chart.render();
});

watch([() => props.series, () => props.labels, isDark], () => {
    chart?.destroy();
    if (!chartEl.value) return;
    chart = new ApexCharts(chartEl.value, buildOptions());
    chart.render();
});

onBeforeUnmount(() => chart?.destroy());
</script>
