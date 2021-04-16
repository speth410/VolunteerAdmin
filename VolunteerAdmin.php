<?php
/*
Plugin Name: Volunteer Admin
Plugin URI: https://github.com/speth410
Description: Plugin facilitate the importing of CSV files into a Wordpress database
Version: 1
Author: Nick Speth
Author URI: https://github.com/speth410
*/

include('importCSV.php');
include('ServiceHours.php');

// Create a new table
function createDatabase(){

   global $wpdb;
   $charset_collate = $wpdb->get_charset_collate();

   $tablename = $wpdb->prefix."volunteer_log";

   $sql = "CREATE TABLE $tablename (
     id bigint(20) NOT NULL AUTO_INCREMENT,
     firstname varchar(80) NOT NULL,
     lastname varchar(80) NOT NULL,
     username varchar(80) NOT NULL,
     email varchar(80) NOT NULL,
     volunteer_date date NOT NULL,
     task varchar(80) NOT NULL,
     start_time time NOT NULL,
     end_time time NOT NULL,
     PRIMARY KEY (id)
   ) $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

}
//register_activation_hook( __FILE__, 'main' );

// Add menu
function plugin_menu() {

   add_menu_page("Volunteer Admin", "Volunteer Admin","manage_options", "myplugin", "displayList",plugins_url('/VolunteerAdmin/img/icon.png'));

}

function displayList(){
   include "displaylist.php";
}

function main() { 
    $currentUser = wp_get_current_user();
    $userRoles = $currentUser->roles;

    if(in_array("administrator", $userRoles)) {
        createDatabase();
        importCSV();
        ?>

        <!-- Form -->
        <form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
            <input type="file" name="import_file" >
            <input type="submit" name="importcsv" value="Import">
        </form>

        <!-- Record List -->
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for names">
        <table id="volunteerLog" width='100%' border='1' style='border-collapse: collapse;'>
            <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Date</th>
                <th>Task</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            </thead>
            <tbody>
            <?php
                global $wpdb;

                // Table name
                $tablename = $wpdb->prefix."volunteer_log";
                // Fetch records
                $entriesList = $wpdb->get_results("SELECT * FROM ".$tablename." order by id desc");
                if(count($entriesList) > 0){
                    $count = 0;
                    foreach($entriesList as $entry){
                        $id = $entry->id;
                        $firstname = $entry->firstname;
                        $lastname = $entry->lastname;
                        $username = $entry->username;
                        $email = $entry->email;
                        $volunteer_date = $entry->volunteer_date;
                        $task = $entry->task;
                        $start_time = $entry->start_time;
                        $end_time = $entry->end_time;

                        echo "<tr>
                        <td>".++$count."</td>
                        <td>".$firstname."</td>
                        <td>".$lastname."</td>
                        <td>".$username."</td>
                        <td>".$email."</td>
                        <td>".$volunteer_date."</td>
                        <td>".$task."</td>
                        <td>".$start_time."</td>
                        <td>".$end_time."</td>
                        </tr>
                        ";
                    }
                }else{
                    echo "<tr><td colspan='9'>No records found</td></tr>";
                }
            ?>
            </tbody>
        </table>
        <script>
            function searchTable() {
                // Declare variables
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("searchInput");
                filter = input.value.toUpperCase();
                table = document.getElementById("volunteerLog");
                tr = table.getElementsByTagName("tr");

                // Loop through all table rows, and hide those who don't match the search query
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[1];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            }
        </script>
    <?php
    } else {
        echo "<h3 style='color: red;'>You Must Be Logged In As An Administrator To View This Page!</h3>";
    }
}
?>
<?php
add_shortcode('importcsv', 'main');
add_shortcode('servicehours', 'showServiceHours');
add_action("admin_menu", "plugin_menu");
?>
