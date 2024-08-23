document.addEventListener("DOMContentLoaded", () => {
  google.charts.load("current", { packages: ["corechart", "bar"] });
  google.charts.setOnLoadCallback(drawCharts);

  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  const profId = getQueryParam("prof_id");

  if (!profId) {
    console.error("Professor ID is missing from the URL.");
    return;
  }

  function fetchStatisticsData() {
    return fetch(`../php/fetch_statistics.php?prof_id=${profId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.error) {
          throw new Error(data.error);
        }
        return data;
      })
      .catch((error) => {
        console.error("Error fetching statistics data:", error);
        return null;
      });
  }

  function drawCharts() {
    fetchStatisticsData().then((data) => {
      if (!data) return;

      window.chartData = data;

      drawAttendanceChart(data.attendance);
    });
  }

  function drawAttendanceChart(attendanceData) {
    const data = new google.visualization.DataTable();
    data.addColumn("string", "Date");
    data.addColumn("number", "Present");
    data.addColumn("number", "Absent");

    attendanceData.forEach((item) => {
      data.addRow([
        item.date,
        parseInt(item.present_count, 10),
        parseInt(item.absent_count, 10),
      ]);
    });

    const options = {
      title: "Student Attendance",
      hAxis: { title: "Date" },
      vAxis: { title: "Count" },
    };

    const chart = new google.visualization.BarChart(
      document.getElementById("students-attendance-chart")
    );
    chart.draw(data, options);
  }

  function drawGenderChart(genderData) {
    const data = new google.visualization.DataTable();
    data.addColumn("string", "Gender");
    data.addColumn("number", "Count");

    genderData.forEach((item) => {
      data.addRow([item.gender, parseInt(item.count, 10)]);
    });

    const options = {
      title: "Student Gender Distribution",
      pieHole: 0.4,
    };

    const chart = new google.visualization.PieChart(
      document.getElementById("students-gender-chart")
    );
    chart.draw(data, options);
  }

  function drawAgeDistributionChart(ageData) {
    const data = new google.visualization.DataTable();
    data.addColumn("number", "Age");
    data.addColumn("number", "Count");

    ageData.forEach((item) => {
      data.addRow([parseInt(item.age, 10), parseInt(item.count, 10)]);
    });

    const options = {
      title: "Age Distribution of Students",
      hAxis: { title: "Age" },
      vAxis: { title: "Count" },
    };

    const chart = new google.visualization.LineChart(
      document.getElementById("students-age-distribution-chart")
    );
    chart.draw(data, options);
  }

  const backButton = document.getElementById("back_button");

  backButton.addEventListener("click", () => {
    window.location.href = "../works/professor_dashboard.php";
  });

  document.getElementById("show-chart").addEventListener("click", () => {
    const selectedChart = document.getElementById("chart-selector").value;

    document.getElementById("students-attendance-chart").style.display = "none";
    document.getElementById("students-gender-chart").style.display = "none";
    document.getElementById("students-age-distribution-chart").style.display =
      "none";

    switch (selectedChart) {
      case "attendance":
        document.getElementById("students-attendance-chart").style.display =
          "block";
        if (window.chartData && window.chartData.attendance) {
          drawAttendanceChart(window.chartData.attendance);
        }
        break;
      case "gender":
        document.getElementById("students-gender-chart").style.display =
          "block";
        if (window.chartData && window.chartData.genderDistribution) {
          drawGenderChart(window.chartData.genderDistribution);
        }
        break;
      case "age":
        document.getElementById(
          "students-age-distribution-chart"
        ).style.display = "block";
        if (window.chartData && window.chartData.ageDistribution) {
          drawAgeDistributionChart(window.chartData.ageDistribution);
        }
        break;
    }
  });
});
