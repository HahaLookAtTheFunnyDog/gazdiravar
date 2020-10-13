<?php
    if(isset($_POST)){
        $response = array();
        $response["success"] = true;
        $response["message"] = "Working";
        echo json_encode($response);
    }
?>