<template>
  <div>
    <apexchart
      type="bar"
      height="350"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  chartData: {
    type: Array,
    required: true
  }
})

const series = computed(() => [
  {
    name: 'Commandes',
    data: props.chartData.map(item => item.count)
  },
  {
    name: 'CA (MRU)',
    data: props.chartData.map(item => item.total)
  }
])

const chartOptions = computed(() => ({
  chart: {
    type: 'bar',
    height: 350,
    toolbar: {
      show: true
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      endingShape: 'rounded'
    },
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    show: true,
    width: 2,
    colors: ['transparent']
  },
  xaxis: {
    categories: props.chartData.map(item => item.month),
    labels: {
      formatter: function(value) {
        // Format the month display if needed
        return value
      }
    }
  },
  yaxis: [
    {
      title: {
        text: 'Nombre de commandes'
      }
    },
    {
      opposite: true,
      title: {
        text: 'Chiffre d\'affaires (MRU)'
      }
    }
  ],
  fill: {
    opacity: 1
  },
  colors: ['#4f46e5', '#10b981'],
  tooltip: {
    y: {
      formatter: function (val, { seriesIndex }) {
        return seriesIndex === 1
          ? `${val.toLocaleString()} MRU`
          : val.toLocaleString()
      }
    }
  }
}))
</script>
