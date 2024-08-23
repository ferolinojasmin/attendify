function showSection(sectionId) {
  // Hide all sections
  document.querySelectorAll(".content-section").forEach(function (section) {
    section.classList.remove("active");
  });

  // Show the clicked section
  document.getElementById(sectionId).classList.add("active");
}

// Initialize charts when the page is fully loaded
window.onload = function () {
  // Bar Chart
  var ctxBar = document.getElementById("barChart").getContext("2d");
  var barChart = new Chart(ctxBar, {
    type: "bar",
    data: {
      labels: ["January", "February", "March", "April", "May", "June"],
      datasets: [
        {
          label: "Monthly Sales",
          data: [12, 19, 3, 5, 2, 3],
          backgroundColor: "rgba(54, 162, 235, 0.2)",
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });

  // Line Chart
  var ctxLine = document.getElementById("lineChart").getContext("2d");
  var lineChart = new Chart(ctxLine, {
    type: "line",
    data: {
      labels: ["January", "February", "March", "April", "May", "June"],
      datasets: [
        {
          label: "Daily Visitors",
          data: [65, 59, 80, 81, 56, 55],
          fill: false,
          borderColor: "rgba(255, 99, 132, 1)",
          tension: 0.1,
        },
      ],
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
};
