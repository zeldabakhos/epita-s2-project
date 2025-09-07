$(document).ready(function() {
    loadPopulationCodes();

    
    $('#newCourseForm').on('submit', function(e) {
        e.preventDefault(); 

        var formData = {
            courseName: $('#courseName').val(),
            sessionCount: $('#sessionCount').val(),
            teacherName: $('#teacherName').val(),
            populationPeriodRef: $('#populationPeriodRef').val(),  
            populationYearRef: $('#populationYearRef').val(),
            populationCodeRef: $('#populationCodeRef').val()
        };

        $('#courseMessage').empty();

        $.ajax({
            url: '../../new_courses.php', 
            type: 'POST',
            data: formData,
            dataType: 'json', 
            success: function(response) {
                if (response.status === 'success') {
                    $('#courseMessage').html('<span class="text-success">' + response.message + '</span>');
                    $('#newCourseForm')[0].reset();
                } else {
                    $('#courseMessage').html('<span class="text-danger">' + response.message + '</span>');
                }
            },
            error: function(xhr, status, error) {
                $('#courseMessage').html('<span class="text-danger">Error: ' + error + '</span>');
            }
        });
    });

    $('#add-course-btn').on('click', function() {
        window.location.replace('http://localhost/src/Views/Population/index.html');
    });
});

function loadPopulationCodes() {
    $.ajax({
        url: '../../get_course_code.php', 
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var options = '';
            data.forEach(function(item) {
                options += `<option value="${item.course_code}">${item.course_code}</option>`;
            });
            $('#populationCodeRef').html(options);
        },
        error: function(xhr, status, error) {
            $('#courseMessage').html('Failed to load population codes: ' + error);
        }
    });
}
