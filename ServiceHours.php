<?php
function showServiceHours(){ ?>
    <style>
    <?php include(plugins_url('styles/style.css')); ?>
    </style>
    <table id="serviceHours" border="1">
        <tr class="header">
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date</th>
            <th>Task</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Total Time</th>
        </tr>

        <?php

            global $wpdb;
            $current_user = wp_get_current_user();   
            $totalVolunteerHours = date('H:i:s', strtotime("00:00:00"));

            $result = $wpdb->get_results( "SELECT * FROM wp_fn44q8_volunteer_log WHERE username=" . "\"" . $current_user->user_login . "\"");
            
            foreach ( $result as $print )   { 
                $totalVolunteerHours = strtotime($totalVolunteerHours) + strtotime($print->total_time) - strtotime('00:00:00');
                $totalVolunteerHours = date('H:i:s', $totalVolunteerHours);

                ?>
            <tr>
                    <td><?php echo $print->firstname; ?></td>
                    <td><?php echo $print->lastname; ?></td>
                    <td><?php echo $print->volunteer_date; ?></td>
                    <td><?php echo $print->task; ?></td>
                    <td><?php echo $print->start_time; ?></td>
                    <td><?php echo $print->end_time; ?></td>
                    <td><?php echo $print->total_time; ?></td>
            </tr>
                <?php }
                
                echo "<h3 style='color: green;'>Your Total Volunteer Hours: ".$totalVolunteerHours."</h3>";
        ?>

    </table>
<?php
}?>