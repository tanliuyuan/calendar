<?php
ini_set("session.cookie_httponly", 1);
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Calendar</title>

<!-- jQuery -->
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<!-- END jQuery -->

<!-- Moment -->
<!-- Credit: http://momentjs.com/ -->
<script src='js/moment-with-locales.min.js'></script>
<!-- END Moment -->

<!-- Bootstrap -->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!-- END Bootstrap -->

<!-- FullCalendar -->
<!-- Credit: fullcalendar.io -->
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.css">
<!-- END FullCalendar -->

<script>
$(document).ready(function () {
    // Initialize calendar
    "use strict";
    $('#calendar').fullCalendar({
        aspectRatio: 1.78,
        fixedWeekCount: false
    });
    // Log in with AJAX
    $('#login').click(function (event) {
        event.preventDefault();
        // Send login info via AJAX
        $.post("server/signup.php", {
            username: $("#username").val(),
            password: $("#password").val()
        }).success(function (data) {
            $('#user_info').html(
                '<div class="navbar-right">' +
                '<h4>Welcome Back, ' +
                data.user_first_name +
                ' ' +
                data.user_last_name +
                '</h4>' +
                '</div>'
            );
            $('#logout').show();
            if (data.error) {
                alert("Error:" + data.error);
            }
        }).fail(function (err) {
            alert("AJAX request failed: " + err.responseJSON.error);
        });
    });
    // Bring up sign up form
    $('#signup').click(function (event) {
        event.preventDefault();
        $('#signup_modal').modal('show');
    });
    $('#logout').hide();
    // Sign up with AJAX
    $('#signup_form').submit(function (event) {
        event.preventDefault();
        // Validate user inputs
        var usernameRegEx = /^[A-Za-z0-9_\-]{3,16}$/;
        var nameRegEx = /[A-Za-z\ \'\-]{1,50}/;
        var passwordRegEx = /^[A-Za-z0-9_\-]{6,18}$/;
        if (!usernameRegEx.test($('#signup_username').val())) {
            alert('Your username is not valid. A valid username is between 3 to 16 characters. Only characters A-Z, a-z, 0-9, "-", and "_" are  acceptable.');
            $('#signup_username').focus();
            return;
        }
        if (!nameRegEx.test($('#signup_first_name').val())) {
            alert('Your first name is not valid. A valid first name is between 1 to 50 characters. Only characters A-Z, a-z, "\'", "-", and " " are  acceptable.');
            $('#signup_first_name').focus();
            return;
        }
        if (!nameRegEx.test($('#signup_last_name').val())) {
            alert('Your last name is not valid. A valid last name is between 1 to 50 characters. Only characters A-Z, a-z, and " " are  acceptable.');
            $('#signup_last_name').focus();
            return;
        }
        if (!passwordRegEx.test($('#signup_password').val())) {
            alert('Your password is not valid. A valid last name is between 6 to 18 characters. Only characters A-Z, a-z, "_", and "-" are  acceptable.');
            $('#signup_password').focus();
            return;
        }
        if ($("#signup_password").val() !== $("#confirm_password").val()) {
            alert('Passwords don\'t match. Please try again!');
            $('#signup_password').focus();
            return;
        }
        // Send signup info via AJAX
        $.post("server/signup.php", {
            username: $("#signup_username").val(),
            first_name: $("#signup_first_name").val(),
            last_name: $("#signup_last_name").val(),
            password: $("#signup_password").val()
        }).success(function (data) {
            $('#user_info').html(
                '<div class="navbar-right">' +
                '<h4>Welcome Back, ' +
                data.user_first_name +
                ' ' +
                data.user_last_name +
                '</h4>' +
                '</div>'
            );
            $('#logout').show();
            if (data.error) {
                alert("Error:" + data.error);
            } else {
                $('#signup_modal').modal('hide');
            }
        }).fail(function (err) {
            alert("AJAX request failed: " + err.responseJSON.error);
        });
    });
    // Logout
    $("#logout").click(function (event) {
        event.preventDefault();
        $.get('server/logout.php').done(function () {
            $('#user_info').html(
                '<form class="navbar-form navbar-right" id="login_form" action="#">' +
                '<div class="form-group">' +
                '<label for="username">Username</label>' +
                '<input type="text" class="form-control" id="username" placeholder="Username">' +
                '<label for="password">Password</label>' +
                '<input type="password" class="form-control" id="password" placeholder="Password">' +
                '</div>' +
                '<button type="submit" class="btn btn-default" id="login">Login</button>' +
                '<button type="button" class="navbar-right btn btn-default" id="signup">Sign Up</button>' +
                '</form>'
            );
        });
    });
});
</script>
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div>
		<!-- Login form -->
		<form class="navbar-form navbar-right" id="login_form" action="#">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" class="form-control" id="username" placeholder="Username">
				<label for="password">Password</label>
				<input type="password" class="form-control" id="password" placeholder="Password">
			</div>
			<button type="submit" class="btn btn-default" id="login">Login</button>
			<button type="button" class="navbar-right btn btn-default" id="signup">Sign Up</button>
		</form>
		<!-- User welcome info -->
		<div class="navbar-right" id="user_info">
        	<h4>Welcome Back, <?php echo($_SESSION['user_first_name']?$_SESSION['user_first_name']:'')?> <?php echo($_SESSION['user_last_name']?$_SESSION['user_last_name']:"");?></h4>
        	<a href="#" class="row navbar-right" id="logout">Log out</a>
        </div>
		<?php
		if (empty($_SESSION))
			echo('<script>$("#user_info").hide();</script>');
        else
        	echo('<script>$("#login_form").hide();</script>');
        ?>
      	</div>
    </div>
</nav>
<div id="calendar">
</div>
<!-- END Navbar -->

<!-- Signup form -->
<div class="container modal fade" id="signup_modal">
	<div class="modal-content col-md-6 col-md-offset-3">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
		</div>
		<div class="modal-body">
			<form id="signup_form" action="#" method="post">
				<div class="form-group">
					<label for="signup_username">Username</label>
					<input type="text" id="signup_username" class="form-control" name="username" placeholder="Username" required>
					<label for="signup_first_name">First Name</label>
					<input type="text" id="signup_first_name" class="form-control" name="first_name" placeholder="First Name" required>
					<label for="signup_first_name">Last Name</label>
					<input type="text" id="signup_last_name" class="form-control" name="last_name" placeholder="Last Name" required>
					<label for="signup_password">Password</label>
					<input type="password" id="signup_password" class="form-control" name="password" placeholder="Password" required>
					<label for="confirm_password">Confirm Password</label>
					<input type="password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-4">
						<button id="signup_submit" class="btn btn-lg btn-primary" type="submit">Sign up</button>
					</div>
				</div>
			</form>
			<div class="modal-footer">
      		</div>
		</div>
	</div>
</div>
<!-- END signup form -->
</body>
</html>