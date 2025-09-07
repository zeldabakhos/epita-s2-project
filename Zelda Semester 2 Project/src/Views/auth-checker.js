$.ajax({
    url: '../../auth_check.php',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
        if(!response.success){}
        window.location.replace('http://localhost/src/Views/Login');
    },
    error: function(jqXHR, textStatus, errorThrown) {
        window.location.replace('http://localhost/src/Views/Login');
    }
});