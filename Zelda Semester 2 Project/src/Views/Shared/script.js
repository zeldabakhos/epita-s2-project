$(document).ready(function() {
    $('#logout-btn').click(function() {
        $.ajax({
            url: 'http://localhost/src/logout.php',
            type: 'POST',
            success: function(response) {
                alert('Logout successful!');
                window.location.href = 'http://localhost/src/Views/Login/';
            },
            error: function(xhr, status, error) {
                alert('Logout failed: ' + error);
            }
        });
    });
});
