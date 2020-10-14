<?php
    global $wpdb;
    if(isset($_POST)){
        $response = array();
        $response["success"] = true;
       $wpdb->insert("messages", array(
            "first_name" => $_POST["fname"],
            "last_name" => $_POST["lname"],
            "email" => $_POST["emailAddr"]
        ));
        echo json_encode($response);
    }
?>