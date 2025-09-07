$(document).ready(function() {
    fetch('../Shared/navbar.html')
    .then(response => response.text())
    .then(html => {
        document.getElementById('navbar-placeholder').innerHTML = html;
    })
    fetchData();  
});

function fetchData() {
    console.log("Fetching data...");
  
    $('.loader').removeClass('d-none');
  
    $.ajax({
        url: '../../get_all_students.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.students) {
                updateStudentsTable(response.students);  
            } else {
                console.error('No students data received.');
            }
            $('.loader').addClass('d-none');     
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching data:', textStatus, errorThrown);
            $('.loader').addClass('d-none');    
        }
    });
}

function updateStudentsTable(students) {
    const tbody = $('.table-responsive:first .table tbody');
    tbody.empty();

    students.forEach(function(student) {
        const newRow = `
            <tr>
                <td>${student.student_epita_email || 'UnknownEmail'}</td>
                <td>${student.contact_first_name || 'UnknownFirstName'}</td>
                <td>${student.contact_last_name || 'UnknownLastName'}</td>
                <td>${student.student_enrollment_status || 'UnknownStatus'}</td>
                <td>${student.student_population_period_ref || 'UnknownPeriod'}</td>
                <td>${student.student_population_year_ref || 'UnknownYear'}</td>
                <td>${student.student_population_code_ref || 'UnknownCode'}</td>
                <td>${student.contact_address || 'UnknownAddress'}</td>
                <td>${student.contact_city || 'UnknownCity'}</td>
                <td>${student.contact_country || 'UnknownCountry'}</td>
                <td>${student.contact_birthdate ? new Date(student.contact_birthdate).toLocaleDateString() : 'UnknownDOB'}</td>
            </tr>
        `;

        tbody.append(newRow);
    });
}


function calculatePassStatus(student) {
    return student.passed ? 'Yes' : 'No';  
}
