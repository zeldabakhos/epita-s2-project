$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault(); 

        var email = $('#email').val();
        var password = $('#password').val();

        $.ajax({
            url: '../../login.php', 
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                try {
                    if (response.status === 'success') {
                        window.location.replace('http://localhost/src/Views/Home');
                    } 
                } catch (error) {
                    $('#loginMessage').html(error);
                }
            },
            error: function(xhr, status, error) {
                $('#loginMessage').html('Error: ' + error);
            }
        });
    });
});
