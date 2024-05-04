<?php
// Assuming you have a PDO database connection established in connect.php
include 'connect.php';

// Check if the reservation ID is provided
if(isset($_POST['id'])) {
    $reservation_id = $_POST['id'];
    
    // Perform a database query to check if an agreement exists for the provided reservation ID
    $query = "SELECT * FROM agreement WHERE ID = :reservation_id";
    $statement = $con->prepare($query);
    $statement->bindValue(':reservation_id', $reservation_id);
    $statement->execute();
    
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($result && count($result) > 0) {
        // Agreement exists
        echo 'exists';
    } else {
        // Agreement does not exist
        echo 'not_exists';
    }
} else {
    // No reservation ID provided
    echo 'invalid_request';
}
?>
