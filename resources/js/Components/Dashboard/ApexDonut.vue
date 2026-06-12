<template>
    <div ref="chartEl" class="w-full" :style="{ height: height + 'px' }"/>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { useDark } from '@vueuse/core';
import ApexCharts from 'apexcharts';

interface Props {
    series: number[];
    labels: string[];
    colors?: string[];
    height?: number;
    centerLabel?: string;
    centerValue?: string | number;
}

const props = withDefaults(defineProps<Props>(), {
    height: 220,
    colors: () => ['#7C3AED', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4', '#F472B6'],
});

const chartEl  = ref<HTMLElement | null>(null);
const isDark   = useDark();
let chart: ApexCharts | null = null;

const buildOptions = () => ({
    chart: {
        type: 'donut',
        background: 'transparent',
        toolbar: { show: false },
        animations: { enabled: true, easing: 'easeinout', speed: 600 },
    },
    series:  props.series,
    labels:  props.labels,
    colors:  props.colors,
    theme:   { mode: isDark.value ? 'dark' : 'light' },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '72%',
                labels: {
                    show: true,
                    name:  { show: true,  fontSize: '12px', offsetY: -4 },
                    value: { show: true,  fontSize: '20px', fontWeight: 700, offsetY: 4,
                             formatter: (v: string) => Number(v).toLocaleString('fr-FR') },
                    total: {
                        show: true,
                        label: props.centerLabel ?? 'Total',
                        fontSize: '11px',
                        formatter: () => props.centerValue !== undefined
                            ? String(props.centerValue)
                            : props.series.reduce((a, b) => a + b, 0).toLocaleString('fr-FR'),
                    },
                },
            },
        },
    },
    stroke:  { width: 2, colors: [isDark.value ? '#1f2937' : '#ffffff'] },
    legend:  {
        show: true, position: 'bottom', fontSize: '11px',
        labels: { colors: isDark.value ? '#9ca3af' : '#6b7280' },
        markers: { width: 10, height: 10, radius: 5 },
        itemMargin: { horizontal: 8, vertical: 4 },
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

watch([() => props.series, () => props.labels, isDark], () => {
    chart?.destroy();
    if (!chartEl.value) return;
    chart = new ApexCharts(chartEl.value, buildOptions());
    chart.render();
});

onBeforeUnmount(() => chart?.destroy());
</script>
