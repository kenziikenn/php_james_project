$(document).ready(function(){
    $("#login").on("click", function(event) {
        event.preventDefault();
        
        var un = document.getElementById('un').value.trim();
        var pw = document.getElementById('pw').value.trim();
        
        $.ajax({
            url: 'function/actions.php?login',
            data: {
                un: un,
                pw: pw
            },
            type: 'POST',
            success: function(response) {
                if (response === "admin") {
                    window.location.href = "pages/hp.php";
                } else if (response === "tabulator") {
                    window.location.href = "pages/tabulator.php";
                } else {
                    // Show error message above login button
                    $('#error-message').show();
                }
            }
        });
    });

    // Hide error message when typing
    $('#un, #pw').on('input', function() {
        $('#error-message').hide();
    });
    $("#submit").on("click", function(event) {
        event.preventDefault();
        
        var fn = document.getElementById('fn').value;
        var ln = document.getElementById('ln').value;
        var un = document.getElementById('un').value;
        var pw = document.getElementById('pw').value;
    
        $.ajax({
            url: 'function/actions.php?submit',
            data: {
                fn: fn,
                ln: ln,
                un: un,
                pw: pw,
            },
            type: 'POST',
            success: function(response) {
                if (response.includes("exists=yes")) {
                    alert("Username already exists. Please choose a different username.");
                    // Do not redirect
                } else if (response.includes("saved=ok")) {
                    window.location.href = "register.php?saved"; // Redirect to homepage on success
                } else {
                    alert("An error occurred. Please try again.");
                    // Do not redirect
                }
            },
            error: function() {
                alert("An error occurred while processing your request. Please try again.");
            }
        });
    });
        
        
    });
