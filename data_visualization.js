document.addEventListener('DOMContentLoaded', function () {
  let allData;
  const yearSelect = document.getElementById('year');
  const monthFromSelect = document.getElementById('month-from');
  const monthToSelect = document.getElementById('month-to');

  // Populate year dropdown with a range of years
  const currentYear = new Date().getFullYear();
  for (let year = currentYear; year >= 2000; year--) {
    const option = document.createElement('option');
    option.value = year;
    option.textContent = year;
    yearSelect.appendChild(option);
  }

  function fetchData(year, startMonth, endMonth) {
    fetch(`data_visualization.php?year=${year}&startMonth=${startMonth}&endMonth=${endMonth}`)
      .then(response => response.json())
      .then(data => {
        allData = data;
        updateCharts(year, startMonth, endMonth);
      })
      .catch(error => console.error('Error:', error));
  }

  // Fetch initial data for the current year and all months
  fetchData(currentYear, 1, 12);

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

  function updateCharts(year, startMonth, endMonth) {
    const filteredData = allData.filter(item => item.year == year && item.month >= startMonth && item.month <= endMonth);
    updatePieCharts(filteredData);
    updateBarChart(filteredData);
  }

  // Functions for updating the charts
  function updatePieCharts(data) {
    const assistanceTypes = [...new Set(data.map(item => item.assistanceType))];
    const lguCounts = assistanceTypes.map(type => data.filter(item => item.assistanceType === type).reduce((acc, cur) => acc + parseInt(cur.lgu_count), 0));
    const barangayCounts = assistanceTypes.map(type => data.filter(item => item.assistanceType === type).reduce((acc, cur) => acc + parseInt(cur.barangay_count), 0));
    const skCounts = assistanceTypes.map(type => data.filter(item => item.assistanceType === type).reduce((acc, cur) => acc + parseInt(cur.sk_count), 0));

    const pieData1 = {
      labels: assistanceTypes,
      datasets: [{
        data: lguCounts,
        backgroundColor: getRandomColors(assistanceTypes.length),
        hoverBackgroundColor: getRandomColors(assistanceTypes.length)
      }]
    };

    const pieData2 = {
      labels: assistanceTypes,
      datasets: [{
        data: barangayCounts,
        backgroundColor: getRandomColors(assistanceTypes.length),
        hoverBackgroundColor: getRandomColors(assistanceTypes.length)
      }]
    };

    const pieData3 = {
      labels: assistanceTypes,
      datasets: [{
        data: skCounts,
        backgroundColor: getRandomColors(assistanceTypes.length),
        hoverBackgroundColor: getRandomColors(assistanceTypes.length)
      }]
    };

    const ctx1 = document.getElementById('pieChart1').getContext('2d');
    const ctx2 = document.getElementById('pieChart2').getContext('2d');
    const ctx3 = document.getElementById('pieChart3').getContext('2d');

    if (ctx1.chart) ctx1.chart.destroy();
    if (ctx2.chart) ctx2.chart.destroy();
    if (ctx3.chart) ctx3.chart.destroy();

    ctx1.chart = new Chart(ctx1, {
      type: 'pie',
      data: pieData1,
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'LGU Fund',
            padding: { top: 70 }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.label || '';
                if (label) label += ': ';
                label += context.raw;
                return label;
              }
            }
          },
          legend: { position: 'right' }
        },
        layout: { padding: { left: -100 } }
      }
    });
    ctx2.chart = new Chart(ctx2, {
      type: 'pie',
      data: pieData2,
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Barangay Fund',
            padding: { top: 70 }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.label || '';
                if (label) label += ': ';
                label += context.raw;
                return label;
              }
            }
          },
          legend: { position: 'right' }
        },
        layout: { padding: { left: -100 } }
      }
    });

    ctx3.chart = new Chart(ctx3, {
      type: 'pie',
      data: pieData3,
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'SK Fund',
            padding: { top: 70 }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.label || '';
                if (label) label += ': ';
                label += context.raw;
                return label;
              }
            }
          },
          legend: { position: 'right' }
        },
        layout: { padding: { left: -100 } }
      }
    });
  }

  function updateBarChart(data) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const assistanceTypes = [...new Set(data.map(item => item.assistanceType))];
    const datasets = assistanceTypes.map(type => ({
      label: type,
      backgroundColor: getRandomColors(1)[0],
      borderColor: getRandomColors(1)[0],
      borderWidth: 1,
      hoverBackgroundColor: getRandomColors(1)[0],
      hoverBorderColor: getRandomColors(1)[0],
      data: months.map((month, index) => data.filter(item => item.month == index + 1 && item.assistanceType === type).reduce((acc, cur) => acc + parseInt(cur.lgu_count) + parseInt(cur.barangay_count) + parseInt(cur.sk_count), 0))
    }));

    const barData = {
      labels: months,
      datasets: datasets
    };

    const ctx4 = document.getElementById('barChart').getContext('2d');
    if (ctx4.chart) ctx4.chart.destroy();

    ctx4.chart = new Chart(ctx4, {
      type: 'bar',
      data: barData,
      options: {
        responsive: true,
        scales: { y: { beginAtZero: true } },
        plugins: {
          title: { display: true, text: 'Assistance by Month' },
          legend: { position: 'top' }
        }
      }
    });
  }

  function getRandomColors(count) {
    const colors = [];
    for (let i = 0; i < count; i++) {
      colors.push(`#${Math.floor(Math.random() * 16777215).toString(16)}`);
    }
    return colors;
  }
});
