<?php


function showVolunteerSignup() { ?>
    <form method='post' id="VolunteerSignup" action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
        <div class="row">
            <div class="colSignup">
                <br>
                <label>First Name</label><br>
                <input type="text" id="txtFirstName" name="fname" required>
                <br><br>
                <label>Last Name</label><br>
                <input type="text" id="txtLastName" name="lname" required>
                <br><br>
                <label>Username</label><br>
                <input type="text" id="txtUsername" name="uname" required>
                <br><br>
                <label>Email</label><br>
                <input type="text" id="txtEmail" name="email" required>
                <br><br>
                <label>Date</label><br>
                <input type="date" id="selDate" name="date" required>
                <br><br><br>
                <input type="submit" name="VolunteerSignup" value="Submit">
            </div>
            <div class="colSignup">
                <br>
                <label>Select Task</label><br>
                <select name="tasks" id="tasks" form="VolunteerSignup">
                    <option value="AM General Volunteer">AM General Volunteer</option>
                    <option value="AM Maintenance">AM Maintenance</option>
                    <option value="Volunteer Orientation">Volunteer Orientation</option>
                    <option value="PM Maintenance">PM Maintenance</option>
                    <option value="PM General Volunteer">PM General Volunteer</option>
                </select>
                <br><br>
                <label>Start Time</label><br>
                <input type="time" id="txtStartTime" name="stime" required>
                <br><br>
                <label>End Time</label><br>
                <input type="time" id="txtEndTime" name="etime" required>		
            </div>
        </div>
    </form>
<?php
global $wpdb;

$tablename = $wpdb->prefix."volunteer_log";
if(isset($_POST['VolunteerSignup'])){
    $firstname = $_REQUEST['fname'];
    $lastname = $_REQUEST['lname'];
    $username = $_REQUEST['uname'];
    $email = $_REQUEST['email'];
    $date = $_REQUEST['date'];
    $date = date('Y-m-d', strtotime($date));
    $task = $_REQUEST['tasks'];
    $start = $_REQUEST['stime'];
    $end = $_REQUEST['etime'];

    //$start = strtotime($start);
    //$end = strtotime($end);

    $total = (strtotime($end) - strtotime($start)) / 60;
    $total = convertToHoursMins($total);

    // Check record already exists or not
    $checkRecord = "SELECT start_time FROM {$tablename} where username='".$username."' AND volunteer_date='".$date."'";
    $record = $wpdb->get_var($checkRecord);

    if($record!=$start.":00"){

    $wpdb->insert($tablename, array(
        'firstname' => $firstname,
        'lastname' => $lastname,
        'username' => $username,
        'email' => $email,
        'volunteer_date' => $date,
        'task' => $task,
        'start_time' => $start,
        'end_time' => $end,
        'total_time' => $total,
    ));
    echo "<h3 style='color: green;'>Record Successfully Added!</h3>";
    echo $record;
    echo $start;
    } else {
        echo "<h3 style='color: red;'>Duplicate Record Could Not Be Entered!</h3>";
    }
}
}
?>