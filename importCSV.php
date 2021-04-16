<?php
function importCSV() {
    global $wpdb;

    // Table name
    $tablename = $wpdb->prefix."volunteer_log";

    // Import CSV
    if(isset($_POST['importcsv'])){

        // File extension
        $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

        // If file extension is 'csv'
        if(!empty($_FILES['import_file']['name']) && $extension == 'csv'){

            $totalInserted = 0;

            // Open file in read mode
            $csvFile = fopen($_FILES['import_file']['tmp_name'], 'r');

            fgetcsv($csvFile); // Skipping header row

            // Read file
            while(($csvData = fgetcsv($csvFile)) !== FALSE){
                $csvData = array_map("utf8_encode", $csvData);

                // Row column length
                $dataLen = count($csvData);

                // Skip row if length != 4
                if( !($dataLen == 9) ) continue;

                // Assign value to variables
                $firstname = trim($csvData[1]);
                $lastname = trim($csvData[2]);
                $username = trim($csvData[3]);
                $email = trim($csvData[4]);
                $volunteer_date = trim($csvData[5]);
                $volunteer_date = date('Y-m-d', strtotime($volunteer_date));
                $task = trim($csvData[6]);
                $start_time = trim($csvData[7]);
                $end_time = trim($csvData[8]);

                // Convert start_time and end_time to HH:MM:SS format
                $start_time = date('H:i:s', strtotime($start_time));
                $end_time = date('H:i:s', strtotime($end_time));

                // Record the total volunteer time in minutes
                $total_time = (strtotime($end_time) - strtotime($start_time)) / 60;
                // Convert total_time to HH:MM format
                $total_time = convertToHoursMins($total_time);

                // Check record already exists or not
                $checkRecord = "SELECT start_time FROM {$tablename} where username='".$username."' AND volunteer_date='".$volunteer_date."'";
                $record = $wpdb->get_var($checkRecord);

                if($record!=$start_time){

                    // Check if variable is empty or not
                    if(!empty($firstname) && !empty($lastname) && !empty($username) && !empty($email) && !empty($volunteer_date) && !empty($task) && !empty($start_time) && !empty($end_time) ) {

                        // Insert Record
                        $wpdb->insert($tablename, array(
                            'firstname' =>$firstname,
                            'lastname' =>$lastname,
                            'username' =>$username,
                            'email' => $email,
                            'volunteer_date' => $volunteer_date,
                            'task' => $task,
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'total_time' => $total_time,
                        ));

                        if($wpdb->insert_id > 0){
                            $totalInserted++;
                        }
                    }
                }
            }
            echo "<h3 style='color: green;'>Total Records Inserted: ".$totalInserted."</h3>";
        }else{
            echo "<h3 style='color: red;'>Invalid Extension</h3>";
        }   
    }
}

function convertToHoursMins($time, $format = '%02d:%02d') {
    if($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}
?>