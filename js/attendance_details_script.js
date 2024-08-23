document.addEventListener('DOMContentLoaded', function() {
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    const form = document.getElementById('date-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const date = document.getElementById('date').value;
        fetchAttendanceData(date);
    });

    function fetchAttendanceData(date) {
        fetch('../works/attendance_details.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `date=${encodeURIComponent(date)}`
        })
        .then(response => response.json())
        .then(data => {
            console.log('Data received:', data);

            if (data && data.total_attendance) {
                drawChart(data.total_attendance);
                displayClassDetails(data.attendance);
            } else {
                console.error('Invalid data received:', data);
                drawChart({}); 
                displayClassDetails({});
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            drawChart({});
            displayClassDetails({});
        });
    }

    function drawChart(attendanceData) {
        const data = new google.visualization.DataTable();
        data.addColumn('string', 'Status');
        data.addColumn('number', 'Count');

        const presentCount = attendanceData?.Present || 0;
        const absentCount = attendanceData?.Absent || 0;

        data.addRows([
            ['Present', presentCount],
            ['Absent', absentCount]
        ]);

        const options = {
            title: 'Attendance Summary',
            hAxis: { title: 'Status' },
            vAxis: { title: 'Count' },
            legend: { position: 'none' }
        };

        const chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }

    function displayClassDetails(attendanceData) {
        const detailsDiv = document.getElementById('details_div');
        detailsDiv.innerHTML = ''; 

        if (Object.keys(attendanceData).length === 0) {
            detailsDiv.innerHTML = '<p>No class details available.</p>';
            return;
        }

        for (const classNo in attendanceData) {
            const classDetails = attendanceData[classNo];
            const detailsHtml = `
                <div>
                    <h3>Class No: ${classNo}</h3>
                    <p>Present: ${classDetails.Present || 0}</p>
                    <p>Absent: ${classDetails.Absent || 0}</p>
                </div>
                <hr>
            `;
            detailsDiv.innerHTML += detailsHtml;
        }
    }
});
