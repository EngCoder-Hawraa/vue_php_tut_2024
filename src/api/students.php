<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_OFF); // Disable mysqli exception throwing

$result = array('error' => false, 'is_db_connected' => false, 'connection_msg' => "");

// Database connection details
$host_name = "localhost"; // Example host
$host_user = "root";      // Example user
$host_pass = "";          // Example password
$host_db = "vue_php_tut_2024"; // Example DB (adjust as needed)

// Attempt to connect to the database server
$connect = @mysqli_connect($host_name, $host_user, $host_pass);

if ($connect) {
    // Server connection successful; attempt to select the database
    $connect_db = @mysqli_select_db($connect, $host_db);
    if ($connect_db) {
        // Database selected successfully
        $result["is_db_connected"] = true;
        $result["connection_msg"] = "Successfully Connected!";
    } else {
        // Database name is incorrect or not found
        $result["is_db_connected"] = false;
        $result["connection_msg"] = "Err: DB name is not found!";
    }
} else {
    // Server connection failed - likely due to incorrect host, user, or password
    $result["is_db_connected"] = false;
    $result["connection_msg"] = "Err: Connection setup is not correct! Check host, username, and password.";
}

//actions
$action = "";
if(isset($_GET['action'])){
    $action = $_GET['action'];
}

if ($action == "read") {
    // Ensure $connect is defined and connected
    if ($connect) {
        $sql = mysqli_query( $connect,"SELECT * FROM `students`"); // Add $connect as the first argument
        $sqlNumOfRows = mysqli_num_rows($sql);
        if($sqlNumOfRows < 1){
            $result["error"] = true;
            $result['message'] = "No Student Added....";
        }
        $stdArr = array();

        // Fetch rows as an associative array
        while ($rows = mysqli_fetch_assoc($sql)) {
            array_push($stdArr, $rows);
        }

        $result["students"] = $stdArr;
    } else {
        $result["error"] = true;
        $result["message"] = "Database connection error";
    }
}

if($action == "create"){
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $sql_verify = mysqli_query($connect,"select * from `students` where email = '$email'");
    $find_sql_verify_num_of_rows = mysqli_num_rows($sql_verify);
    if($find_sql_verify_num_of_rows < 1){
        $sql = mysqli_query($connect,"insert into `students` (name, email, phone) 
                                    VALUES ('$name', '$email', '$phone')");
        if($sql){
            $result["message"] = "Successfully Added This Student's Record => $name!";
        }else{
            $result["error"]= true;
            $result["message"] = "Failed Adding This Student => $name!";
        }
    }else{
        $result["error"]= true;
        $result["message"] = "This Email $email Does Exist Choose another E-mail.!";
    }
}

if($action == "update"){
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $sql_verify = mysqli_query($connect,"select * from `students` where email = '$email' and id != '$id'");
    $find_sql_verify_num_of_rows = mysqli_num_rows($sql_verify);
    if($find_sql_verify_num_of_rows < 1){
        $sql = mysqli_query($connect,"update `students` 
                                   Set name = '$name', email = '$email', phone = '$phone'
                                   WHERE id = '$id'");
        if($sql){
            $result["message"] = "Successfully Updated This Student's Record => $name!";
        }else{
            $result["error"]= true;
            $result["message"] = "Failed Updating This Student => $name!";
        }
    }else{
        $result["error"]= true;
        $result["message"] = "This Email $email Does Exist Choose another E-mail. For User Id $id!";
    }
}

if($action == "delete"){
    $id = $_POST["id"];
    $name = $_POST["name"];
    $sql_verify = mysqli_query($connect,"select * from `students` where id = '$id'");
    $find_sql_verify_num_of_rows = mysqli_num_rows($sql_verify);
    if($find_sql_verify_num_of_rows >= 1){
        $sql = mysqli_query( $connect,"Delete FROM `students` where id = '$id'"); // Add $connect as the first argument
        if($sql){
            $result["message"] = "Successfully Deleted This Student's Record => $name!";
        }else{
            $result["error"]= true;
            $result["message"] = "Failed Deleting This Student => $name!";
        }
    }else{
        $result["error"]= true;
        $result["message"] = "This Record Does Not Exist!";
    }
}

if($action == "deleteAll"){
    $sql = mysqli_query( $connect,"Delete FROM `students`"); // Add $connect as the first argument
    if($sql){
        $result["message"] = "Successfully Deleted All Students!";
    }else{
        $result["error"]= true;
        $result["message"] = "Failed Deleting All Students!";
    }
}

// Output the result as JSON
// Output the result as JSON and ensure mysqli connection is closed
echo json_encode($result);
// Close the connection only if it's valid
if ($connect) {
    mysqli_close($connect);
}
?>
