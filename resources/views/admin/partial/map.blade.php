<script src="{{ asset('js/pages/apexcharts-mixed.init.js') }}"></script>
<script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  if (!window.chartData) return console.warn("ChartData not defined");

  Object.entries(window.chartData).forEach(([chartId, cfg]) => {
    const el = document.getElementById(chartId);
    if (!el) return console.warn(`Chart element #${chartId} not found`);

    const opts = {
      chart: {
        type: cfg.type,
        height: 350,
        stacked: cfg.type === 'bar'
      },
      labels: cfg.labels,
      xaxis: cfg.type !== 'pie' ? { categories: cfg.labels } : undefined,
      plotOptions: cfg.type === 'bar' ? {
        bar: { horizontal: false, borderRadius: 5 }
      } : {},
      fill: { opacity: 1 },
      legend: { position: 'top', horizontalAlign: 'start' },
      responsive: [{
        breakpoint: 480,
        options: { legend: { position: 'bottom' } }
      }]
    };

    if (cfg.type === 'pie') {
      opts.series = cfg.series;
    } else {
      opts.series = cfg.series;
    }

    new ApexCharts(el, opts).render();
  });
});




</script>
