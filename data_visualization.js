document.addEventListener('DOMContentLoaded', function () {
  let allData;
  let currentChartType = 'bar';
  let dynamicChart;
  const yearSelect = document.getElementById('year');
  const monthFromSelect = document.getElementById('month-from');
  const monthToSelect = document.getElementById('month-to');
  const chartTypeSelect = document.getElementById('chart-type');

  const currentYear = new Date().getFullYear();
  for (let year = currentYear; year >= 2000; year--) {
    const option = document.createElement('option');
    option.value = year;
    option.textContent = year;
    yearSelect.appendChild(option);
  }

  fetchData(currentYear, 1, 12);

  function fetchData(year, startMonth, endMonth) {
    fetch(`data_visualization.php?year=${year}&startMonth=${startMonth}&endMonth=${endMonth}`)
      .then(response => response.json())
      .then(data => {
        allData = data;
        updateCharts(year, startMonth, endMonth);
      })
      .catch(error => console.error('Error:', error));
  }

  function updateCharts(year, startMonth, endMonth) {
    const filteredData = allData.filter(item => item.year == year && item.month >= startMonth && item.month <= endMonth);
    updateDoughnutCharts(filteredData);
    updateDynamicChart(filteredData, currentChartType);
  }

  // Shadow plugin
  const shadowPlugin = {
    id: 'shadowPlugin',
    beforeDatasetsDraw: function(chart, args, options) {
      const ctx = chart.ctx;
      ctx.save();
      ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
      ctx.shadowBlur = 10;
      ctx.shadowOffsetX = 5;
      ctx.shadowOffsetY = 5;
    },
    afterDatasetsDraw: function(chart, args, options) {
      chart.ctx.restore();
    }
  };

  function updateDynamicChart(data, chartType) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const assistanceTypes = [...new Set(data.map(item => item.assistanceType))];
    const datasets = assistanceTypes.map(type => ({
      label: type,
      backgroundColor: getRandomColors(1)[0],
      data: months.map((month, index) => 
          data.filter(item => item.month == index + 1 && item.assistanceType === type)
              .reduce((acc, cur) => acc + parseInt(cur.lgu_count) + parseInt(cur.barangay_count) + parseInt(cur.sk_count), 0)),
      borderRadius: 8,
      borderWidth: 1,
      borderColor: 'rgba(0, 0, 0, 0.1)'
    }));

    const dynamicData = {
      labels: months,
      datasets: datasets
    };

    if (dynamicChart) {
      dynamicChart.destroy();
    }

    const ctx = document.getElementById('dynamicChart').getContext('2d');
    dynamicChart = new Chart(ctx, {
      type: chartType,
      data: dynamicData,
      options: {
        responsive: true,
        aspectRatio: chartType === 'doughnut' ? 1.7 : 2,
        scales: chartType !== 'doughnut' ? {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              callback: function(value) {
                return Number.isInteger(value) ? value : null;
              }
            }
          }
        } : {},
        plugins: {
          title: { display: true, text: 'Assistance by Month' },
          legend: { position: 'top' }
        },
        animation: {
          duration: 1500, // 0.8 secs
          easing: 'easeInOutBounce' // Smooth bounce effect
        }
      },
      plugins: [shadowPlugin]
    });
  }

  function updateDoughnutCharts(data) {
    const assistanceTypes = [...new Set(data.map(item => item.assistanceType))];
    const lguCounts = assistanceTypes.map(type => data.filter(item => item.assistanceType === type).reduce((acc, cur) => acc + parseInt(cur.lgu_count), 0));
    const barangayCounts = assistanceTypes.map(type => data.filter(item => item.assistanceType === type).reduce((acc, cur) => acc + parseInt(cur.barangay_count), 0));
    const skCounts = assistanceTypes.map(type => data.filter(item => item.assistanceType === type).reduce((acc, cur) => acc + parseInt(cur.sk_count), 0));

    const ctx1 = document.getElementById('pieChart1').getContext('2d');
    const ctx2 = document.getElementById('pieChart2').getContext('2d');
    const ctx3 = document.getElementById('pieChart3').getContext('2d');

    function createDoughnutChart(ctx, title, data) {
      return new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: assistanceTypes,
          datasets: [{
            data: data,
            backgroundColor: getRandomColors(assistanceTypes.length),
            hoverBackgroundColor: getRandomColors(assistanceTypes.length)
          }]
        },
        options: {
          responsive: true,
          plugins: {
            title: { display: true, text: title, padding: { top: 70 } },
            legend: { position: 'right' }
          },
          cutout: '40%', // Hollow center for doughnut effect
          animation: {
            animateRotate: true, // Rotate animation for doughnut segments
            animateScale: true, // Scaling effect for doughnut
            duration: 1300,
            easing: 'easeOutElastic' // Elastic effect for doughnut segments
          }
        },
        plugins: [shadowPlugin]
      });
    }

    // Destroy existing charts if they exist
    if (ctx1.chart) ctx1.chart.destroy();
    if (ctx2.chart) ctx2.chart.destroy();
    if (ctx3.chart) ctx3.chart.destroy();

    // Reinitialize the doughnut charts
    ctx1.chart = createDoughnutChart(ctx1, 'LGU Fund', lguCounts);
    ctx2.chart = createDoughnutChart(ctx2, 'Barangay Fund', barangayCounts);
    ctx3.chart = createDoughnutChart(ctx3, 'SK Fund', skCounts);
  }

  function getRandomColors(count) {
    const colors = [];
    for (let i = 0; i < count; i++) {
      colors.push(`#${Math.floor(Math.random() * 16777215).toString(16)}`);
    }
    return colors;
  }

  yearSelect.addEventListener('change', function () {
    const selectedYear = parseInt(this.value);
    const startMonth = parseInt(monthFromSelect.value);
    const endMonth = parseInt(monthToSelect.value);
    fetchData(selectedYear, startMonth, endMonth);
  });

  monthFromSelect.addEventListener('change', function () {
    const selectedYear = parseInt(yearSelect.value);
    const startMonth = parseInt(this.value);
    const endMonth = parseInt(monthToSelect.value);
    fetchData(selectedYear, startMonth, endMonth);
  });

  monthToSelect.addEventListener('change', function () {
    const selectedYear = parseInt(yearSelect.value);
    const startMonth = parseInt(monthFromSelect.value);
    const endMonth = parseInt(this.value);
    fetchData(selectedYear, startMonth, endMonth);
  });

  chartTypeSelect.addEventListener('change', function () {
    currentChartType = this.value;
    const selectedYear = parseInt(yearSelect.value);
    const startMonth = parseInt(monthFromSelect.value);
    const endMonth = parseInt(monthToSelect.value);
    updateDynamicChart(allData.filter(item => item.year == selectedYear && item.month >= startMonth && item.month <= endMonth), currentChartType);
  });
});
