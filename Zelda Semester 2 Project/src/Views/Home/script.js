$(document).ready(function() {
    fetch('../../auth_check.php').then(isAuthenticated => {
        if (isAuthenticated) {
            console.log('User is authenticated');
            fetch('../Shared/navbar.html')
                .then(response => response.text())
                .then(html => {
                    console.log('Navbar loaded');
                    document.getElementById('navbar-placeholder').innerHTML = html;
                })
                .then(() => {
                    console.log('Calling fetchData...');
                    fetchData();
                })
                .catch(error => console.error('Error loading navbar:', error));
        } else {
            console.log('User is not authenticated.');
        }
    }).catch(error => console.error('Authentication check failed:', error));
});

const fetchData = () => {
    $('.loader').removeClass('d-none');
    $.ajax({
        url: '../../get_population_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const studentData = response.students;
            const attendanceData = response.attendance;

            $('.list-group').empty();

            $.each(studentData, function(index, item) {
                console.log (item);


                const content = `${item.student_population_code_ref} - ${item.student_population_period_ref}${item.student_population_year_ref} (${item.student_count})`;
                $('<li>', {
                    'class': 'list-group-item',
                    'text': content,
                    'click': () => {
                        window.location.replace(`http://localhost/src/Views/Population/?discipline=${item.student_population_code_ref}&period=${item.student_population_period_ref}&year=${item.student_population_year_ref}`);
                      }
                }).appendTo($('.list-group'));
            });

            displayPieChart(studentData);
            displayAttendance(attendanceData);
            displayBarChart(attendanceData);

            
            $('.loader').addClass('d-none');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching data:', textStatus, errorThrown);
            $('.loader').addClass('d-none');
        }
    });
};

const displayPieChart = (toPlot) => {
    const labels = toPlot.map(item => `${item.student_population_code_ref} - ${item.student_population_period_ref}${item.student_population_year_ref}`);
    const data = toPlot.map(item => item.student_count);

    const ctx = document.getElementById('myPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Student Population',
                data: data,
                backgroundColor: [
                    '#ff6384', 
                    '#36a2eb', 
                    '#ffcd56', 
                    '#4bc0c0', 
                    '#9966ff', 
                    '#ff9f40', 
                    '#ff6b6b', 
                    '#51cf66', 
                    '#ffe066', 
                    '#748ffc'  
                ],
                borderColor: [
                    '#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0',
                    '#9966ff', '#ff9f40', '#ff6b6b', '#51cf66',
                    '#ffe066', '#748ffc'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
};


const displayAttendance = (attendance) => {
    const attendanceList = $('.overall-attendance');
    attendanceList.empty();

    $.each(attendance, function(index, item) {
        const content = `${item.program_assignment} - (${item.PresencePercentage}%)`;
        $('<li>', {
            'class': 'list-group-item',
            'text': content
        }).appendTo(attendanceList);
    });
};

const displayBarChart = (attendance) => {
    const labels = attendance.map(item => `${item.program_assignment}`);
    const data = attendance.map(item => item.PresencePercentage);

    const ctx = document.getElementById('myBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Attendance Percentage',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
};
