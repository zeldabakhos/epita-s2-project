$(document).ready(function() {
    fetch('../Shared/navbar.html')
    .then(response => response.text())
    .then(html => {
        document.getElementById('navbar-placeholder').innerHTML = html;
    });

    loadPopulationCodes();

    $('#signUpForm').on('submit', function(e) {
        e.preventDefault();

        var formData = {
            email: $('#email').val(),
            contactRef: $('#contactRef').val(),
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            address: $('#address').val(),
            city: $('#city').val(),
            country: $('#country').val(),
            birthdate: $('#birthdate').val(),
            populationPeriodRef: $('#populationPeriodRef').val(),
            populationYearRef: $('#populationYearRef').val(),  
            populationCodeRef: $('#populationCodeRef').val()
        };

        $.ajax({
            url: '../../signup.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#signUpMessage').html(response);

                window.location.replace('http://localhost/src/Views/Home');
            },
            error: function(xhr, status, error) {
                $('#signUpMessage').html('Error: ' + error);
            }
        });
    });
});

function loadPopulationCodes() {
    $.ajax({
        url: '../../get_program_assignments.php', 
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var options = '';
            data.forEach(function(item) {
                options += `<option value="${item.program_assignment}" data-year="${item.intake_year}">${item.program_assignment}</option>`;
            });
            $('#populationCodeRef').html(options);

            $('#populationCodeRef').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var intakeYear = selectedOption.data('year');
                $('#populationYearRef').val(intakeYear);  
            });
        },
        error: function(xhr, status, error) {
            $('#signUpMessage').html('Failed to load population codes: ' + error);
        }
    });
}
