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
<!-- Moment -->
<script src='js/moment-with-locales.min.js'></script>
<!-- Bootstrap -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!-- FullCalendar -->
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.css">
<!-- Date/time picker -->
<script src="js/DateTimePicker.js"></script>
<link rel="stylesheet" type="text/css" href="css/DateTimePicker.css">
<!-- My own JS functions -->
<script src="js/calendar.js"></script>
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
		<!-- User info -->
		<div class="navbar-right" id="user_info">
        	<h4>Welcome Back, <span id="user_first_name"><?php echo(isset($_SESSION['user_first_name'])?$_SESSION['user_first_name']:'')?></span> <span id="user_last_name"><?php echo(isset($_SESSION['user_last_name'])?$_SESSION['user_last_name']:"");?></span></h4>
        	<div class="row navbar-right">
        		<button type="button" class="btn btn-sm btn-default" id="add_event">
  					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Event
				</button>
        		<a href="#" id="logout">Log out</a>
        	</div>
        </div>
		<?php
		// If user is logged in and session is set, hide login form, otherwise hide user info
		if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'])
			echo('<script>$("#login_form").hide();</script>');
        else
        	echo('<script>$("#user_info").hide();</script>');
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
					<div class="col-md-6 col-md-offset-3">
						<button id="signup_submit" class="btn btn-lg btn-primary center-block" type="submit">Sign up</button>
					</div>
				</div>
			</form>
			<div class="modal-footer">
      		</div>
		</div>
	</div>
</div>
<!-- END Signup form -->

<!-- Add event form -->
<div class="container modal fade" id="add_event_modal">
	<div class="modal-content col-md-6 col-md-offset-3">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
		</div>
		<div class="modal-body">
			<form id="add_event_form" action="#" method="post">
				<div class="form-group">
					<label for="add_event_title">Title</label>
					<input type="text" id="add_event_title" class="form-control" name="title" placeholder="Title" required>
					<label for="add_event_start_time">Start Time</label>
					<input type="text" class="form-control" id="add_event_start_time" name="start_time" data-field="datetime" readonly required>
					<div class="add_event_datetime"></div>
					<label for="add_event_end_time">End Time</label>
					<input type="text" class="form-control" id="add_event_end_time" name="end_time" data-field="datetime" readonly required>
					<div class="add_event_datetime"></div>
					<div id="admin_options">
						<label for="is_public">Is this a public event?</label>
						<select class="form-control" name="is_public" id="is_public">
  							<option value="0" selected="selected">No</option>
  							<option value="1">Yes</option>
						</select>
					</div>
					<?php
					// If an admin is logged in, show the admin options. Otherwise hide them.
					if(!isset($_SESSION['admin_logged_in']) || (isset($_SESSION['admin_logged_in']) && !$_SESSION['admin_logged_in']))
						echo('<script>$("#admin_options").hide();</script>');
					?>
					<input type="hidden" class="token" id="add_event_token" name="token" value="<?php echo(isset($_SESSION['token'])?$_SESSION['token']:'')?>">
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<button id="add_event_submit" class="btn btn-lg btn-primary center-block" type="submit">Add Event</button>
					</div>
				</div>
			</form>
			<div class="modal-footer">
      		</div>
		</div>
	</div>
</div>
<!-- END Add event form -->

<!-- Edit/delete event options -->
<div class="container modal fade" id="edit_delete_event_modal">
	<div class="modal-content col-md-4 col-md-offset-4">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="block-center">
					<button id="edit_event" class="btn btn-lg btn-primary" type="button">Edit Event</button>
					<button id="delete_event" class="btn btn-lg btn-primary" type="button">Delete Event</button>
				</div>
			</div>
			<div class="modal-footer">
      		</div>
		</div>
	</div>
</div>
<!-- END Edit/delete event options -->

<!-- Edit event form -->
<div class="container modal fade" id="edit_event_modal">
	<div class="modal-content col-md-6 col-md-offset-3">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
		</div>
		<div class="modal-body">
			<form id="edit_event_form" action="#" method="post">
				<div class="form-group">
					<input type="hidden" id="edit_event_id" name="id">
					<label for="edit_event_title">Title</label>
					<input type="text" id="edit_event_title" class="form-control" name="title" placeholder="Title" required>
					<label for="edit_event_start_time">Start Time</label>
					<input type="text" class="form-control" id="edit_event_start_time" name="start_time" data-field="datetime" readonly required>
					<div class="edit_event_datetime"></div>
					<script>
					// Initialize date/time picker for event start and end times
                    $('.datetime').datetimepicker({
                        dateTimeFormat: 'yyyy-MM-dd hh:mm'
                    });
                    </script>
					<label for="edit_event_end_time">End Time</label>
					<input type="text" class="form-control" id="edit_event_end_time" name="end_time" data-field="datetime" readonly required>
					<div class="edit_event_datetime"></div>
					<script>
					// Initialize date/time picker for event start and end times
					$('.datetime').datetimepicker({
                        dateTimeFormat: 'yyyy-MM-dd hh:mm'
                    });
                    </script>
					<input type="hidden" class="token" id="edit_event_token" name="token" value="<?php echo(isset($_SESSION['token'])?$_SESSION['token']:'')?>">
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<button id="edit_event_submit" class="btn btn-lg btn-primary center-block" type="submit">Edit Event</button>
					</div>
				</div>
			</form>
			<div class="modal-footer">
      		</div>
		</div>
	</div>
</div>
<!-- END Edit event form -->
</body>
</html>