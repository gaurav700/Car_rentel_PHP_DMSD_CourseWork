<?php 
	session_start();

	//Check If user is already logged in
	if(isset($_SESSION['username_yahya_car_rental']) && isset($_SESSION['password_yahya_car_rental']))
	{
        //Page Title
        $pageTitle = 'Dashboard';

        //Includes
        include 'connect.php';
        include 'Includes/functions/functions.php'; 
        include 'Includes/templates/header.php';

?>
        <!-- Begin Page Content -->
        <div class="container-fluid">
            
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i>
                    Generate Report
                </a>
            </div>

            <!-- Cancel Reservation Button Submitted -->
            <?php
                if (isset($_POST['cancel_reservation_sbmt']) && $_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    $reservation_id = $_POST['ID'];
                    try
                    {
                        $stmt = $con->prepare('UPDATE reservation set Status = "Cancelled" where Id = ?');
                        $stmt->execute(array($reservation_id));
                        echo "<div class = 'alert alert-success'>";
                            echo 'Reservation has been canceled succssefully!';
                        echo "</div>";
                    }
                    catch(Exception $e)
                    {
                        echo "<div class = 'alert alert-danger'>";
                            echo 'Error occurred: ' .$e->getMessage();
                        echo "</div>";
                    }
                }
                if (isset($_POST['Confim_reservation_sbmt']) && $_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    $reservation_id = $_POST['ID'];
                    try
                    {
                        $stmt = $con->prepare('UPDATE reservation set Status = "Confirmed" where Id = ?');
                        $stmt->execute(array($reservation_id));
                        echo "<div class = 'alert alert-success'>";
                            echo 'Reservation has been Confirmed succssefully!';
                        echo "</div>";
                    }
                    catch(Exception $e)
                    {
                        echo "<div class = 'alert alert-danger'>";
                            echo 'Error occurred: ' .$e->getMessage();
                        echo "</div>";
                    }
                }
            ?>

            <!-- Content Row -->
            <div class="row">

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Clients
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countItems("Id","customer")?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bs bs-boy fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Car Brands
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countItems("Id","model")?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bs bs-scissors-1 fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Cars
                                    </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo countItems("VIN","car")?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bs bs-man fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Reservations
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo countItems("Id","reservation")?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header tab" style="padding: 0px !important;background: #36b9cc!important">
                    <button class="tablinks active" onclick="openTab(event, 'Pending')">
                        Pending Reservations
                    </button>
                    <button class="tablinks" onclick="openTab(event, 'Confirmed')">
                        Confirmed Reservations
                    </button>
                    <button class="tablinks" onclick="openTab(event, 'Canceled')">
                        Canceled Reservations
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered tabcontent" id="Pending" style="display:table" width="100%" cellspacing="0">
                        <thead>
                                    <tr>
                                        <th>
                                            Reservation Type
                                        </th>
                                        <th>
                                            Reservation Status
                                        </th>
                                        <th>
                                            Pickup Date
                                        </th>
                                        <th>
                                            Pickup Location
                                        </th>
                                        <th>
                                            Return Date
                                        </th>
                                        <th>
                                            Return Location
                                        </th>
                                        <th>
                                            Selected Car
                                        </th>
                                        <th>
                                            Client
                                        </th>
                                        <th>
                                            Cancel
                                        </th>
                                        <th>
                                            Confirmed
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                        $stmt = $con->prepare("SELECT 
                                        Reservation.Id,
                                        Reservation.RevType, 
                                        Reservation.Status,
                                        Reservation.StartDate, 
                                        Reservation.EndDate, 
                                        PickUpLocation.State AS PickUpCity, 
                                        DropOffLocation.State AS DropOffCity, 
                                        ClassCar.Name AS className, 
                                        Customer.FName AS CustomerFirstName, 
                                        Customer.LName AS CustomerLastName
                                      FROM 
                                        Reservation
                                      INNER JOIN 
                                        ClassCar ON Reservation.ClassCarName = ClassCar.Name
                                      INNER JOIN 
                                        Customer ON Reservation.CustomerID = Customer.ID
                                      INNER JOIN 
                                        Location AS PickUpLocation ON Reservation.PickUpLocation = PickUpLocation.ID
                                      INNER JOIN 
                                        Location AS DropOffLocation ON Reservation.DropOffLocation = DropOffLocation.ID
                                    WHERE 
                                        Reservation.Status = 'pending' OR Reservation.Status = 'Pending';
                                        ");
                                        $stmt->execute();
                                        $rows = $stmt->fetchAll();
                                        $count = $stmt->rowCount();
                                        
                                        if($count == 0)
                                        {

                                            echo "<tr>";
                                                echo "<td colspan='5' style='text-align:center;'>";
                                                    echo "List of your upcoming reservations will be presented here";
                                                echo "</td>";
                                            echo "</tr>";
                                        }
                                        else
                                        {

                                        foreach($rows as $row)
                                        {
                                            echo "<tr>";
                                                echo "<td>";
                                                echo $row['RevType'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['Status'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['StartDate'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['PickUpCity'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['EndDate'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['DropOffCity'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['className'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['CustomerFirstName'], ' ', $row['CustomerLastName'];
                                                echo "</td>";
                                                echo "<td>";
                                                    $cancel_data = "cancel_reservation_".$row["Id"];
                                                    ?>
                                                        <ul class="list-inline m-0">

                                                            <!-- CANCEL BUTTON -->

                                                            <li class="list-inline-item" data-toggle="tooltip" title="Cancel Reservation">
                                                                <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $cancel_data; ?>" data-placement="top">
                                                                    <i class="fas fa-calendar-times"></i>
                                                                </button>

                                                                <!-- CANCEL MODAL -->
                                                                <div class="modal fade" id="<?php echo $cancel_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $cancel_data; ?>" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <form action = "dashboard.php" method = "POST">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Cancel Reservation</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>Are you sure you want to cancel this reservation?</p>
                                                                                    <input type="hidden" value = "<?php echo $row['Id']; ?>" name = "ID">
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                                                    <button type="submit" name = "cancel_reservation_sbmt"  class="btn btn-danger">Yes, Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                            </li>
                                                        </ul>

                                                        <?php
                                                    echo "</td>";
                                                    echo "<td>";
                                                    $confirm_data = "confirm_reservation_".$row["Id"];
                                                    ?>
                                                        <ul class="list-inline m-0">

                                                            <!-- CANCEL BUTTON -->

                                                            <li class="list-inline-item" data-toggle="tooltip" title="Confirm Reservation">
                                                                <button class="btn btn-success btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $confirm_data; ?>" data-placement="top">
                                                                    <i class="fas fa-solid fa-check"></i>
                                                                </button>

                                                                <!-- CANCEL MODAL -->
                                                                <div class="modal fade" id="<?php echo $confirm_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $confirm_data; ?>" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <form action = "dashboard.php" method = "POST">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Confirm Reservation</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>Are you sure you want to Confirmed this reservation?</p>
                                                                                    <input type="hidden" value = "<?php echo $row['Id']; ?>" name = "ID">
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                                                    <button type="submit" name = "Confim_reservation_sbmt"  class="btn btn-success">Yes, Confirm</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                            </li>
                                                        </ul>

                                                        <?php
                                                    echo "</td>";
                                                echo "</tr>";
                                            }
                                        }

                                    ?>

                                </tbody>
                        </table>
                        <table class="table table-bordered tabcontent" id="Confirmed" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                <th>
                                            Reservation Status
                                        </th>
                                        <th>
                                            Pickup Date
                                        </th>
                                        <th>
                                            Pickup Location
                                        </th>
                                        <th>
                                            Return Date
                                        </th>
                                        <th>
                                            Return Location
                                        </th>
                                        <th>
                                            Selected Car
                                        </th>
                                        <th>
                                            Client
                                        </th>
                                        <th>
                                            Agreement
                                        </th>
                                        <th>
                                            Invoice
                                        </th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                    $stmt = $con->prepare("SELECT 
                                    Reservation.Id,
                                    Reservation.Status,
                                    Reservation.StartDate, 
                                    Reservation.EndDate, 
                                    PickUpLocation.State AS PickUpCity, 
                                    DropOffLocation.State AS DropOffCity, 
                                    ClassCar.Name AS className, 
                                    Customer.FName AS CustomerFirstName, 
                                    Customer.LName AS CustomerLastName
                                FROM 
                                    Reservation
                                INNER JOIN 
                                    ClassCar ON Reservation.ClassCarName = ClassCar.Name
                                INNER JOIN 
                                    Customer ON Reservation.CustomerID = Customer.ID
                                INNER JOIN 
                                    Location AS PickUpLocation ON Reservation.PickUpLocation = PickUpLocation.ID
                                INNER JOIN 
                                    Location AS DropOffLocation ON Reservation.DropOffLocation = DropOffLocation.ID
                                WHERE 
                                    Reservation.Status = 'Confirmed';");
                                    $stmt->execute(array());
                                    $rows = $stmt->fetchAll();
                                    $count = $stmt->rowCount();

                                    if($count == 0)
                                    {

                                        echo "<tr>";
                                            echo "<td colspan='5' style='text-align:center;'>";
                                                echo "List of your Confirmed reservations will be presented here";
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                    else
                                    {

                                        foreach($rows as $row)
                                        {
                                            echo "<tr>";
                                            echo "<td>";
                                            echo $row['Status'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['StartDate'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['PickUpCity'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['EndDate'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['DropOffCity'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['className'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['CustomerFirstName'], ' ', $row['CustomerLastName'];
                                            echo "</td>";
                                            
                                            echo "<td>";
                                            $agreement_data = "agreement_data".$row["Id"];
                                            ?>
                                                <ul class="list-inline m-0">
                                                    <!-- CANCEL BUTTON -->
                                                    <li class="list-inline-item" data-toggle="tooltip" title="agreement_data">
                                                        <button class="btn btn-success btn-sm rounded-0" type="button" onclick="checkAgreement(<?php echo $row['Id']; ?>)" data-toggle="modal" data-target="#<?php echo $agreement_data; ?>" data-placement="top">
                                                        <i class="fas fa-solid fa-lock"></i>
                                                        </button>
                                                        <!-- CANCEL MODAL -->
                                                        <div class="modal fade" id="<?php echo $agreement_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $agreement_data; ?>" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <form action = "agreement.php" method = "POST">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Agreeement Of Car</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Start Odometer Reading</p>
                                                                            <input type="text"  name = "StartOdometer">
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Are you sure you want to Make the agreement this reservation?</p>
                                                                            <input type="hidden" value = "<?php echo $row['Id']; ?>" name = "ID">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                                            <button type="submit" name = "Confim_reservation_sbmt"  class="btn btn-success">Yes, Confirm</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>

                                                    </li>
                                                </ul>

                                                <?php
                                            echo "</td>";
                                        
                                       
    
                                                    echo "<td>";
                                                    $return = "return_reservation_".$row["Id"];
                                                    ?>
                                                        <ul class="list-inline m-0">

                                                            <!-- CANCEL BUTTON -->

                                                            <li class="list-inline-item" data-toggle="tooltip" title="Return Reservation">
                                                                <button class="btn btn-success btn-sm rounded-0" type="button"  onclick="checkInvoice(<?php echo $row['Id']; ?>)" data-toggle="modal" data-target="#<?php echo $return; ?>" data-placement="top">
                                                                <i class="fas fa-solid fa-lock"></i>
                                                                </button>

                                                                <!-- CANCEL MODAL -->
                                                                <div class="modal fade" id="<?php echo $return; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $return; ?>" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <form action = "invoice.php" method = "POST">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Returning Car</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>End Odometer Reading</p>
                                                                                    <input type="text"  name = "EndOdometer">
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>Are you sure you want to Return this reservation? <?php echo $row['Id']; ?></p>
                                                                                    <input type="hidden" value = "<?php echo $row['Id']; ?>" name = "ID">
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                                                    <button type="submit" name = "Confim_return_sbmt"  class="btn btn-success">Yes, Confirm</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                            </li>
                                                        </ul>

                                                        <?php
                                                    echo "</td>";
                                            echo "</tr>";
                                        }
                                    }

                                ?>

                            </tbody>
                        </table>




                        <table class="table table-bordered tabcontent" id="Canceled" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                <th>
                                            Reservation Status
                                        </th>
                                        <th>
                                            Pickup Date
                                        </th>
                                        <th>
                                            Pickup Location
                                        </th>
                                        <th>
                                            Return Date
                                        </th>
                                        <th>
                                            Return Location
                                        </th>
                                        <th>
                                            Selected Car
                                        </th>
                                        <th>
                                            Client
                                        </th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                    $stmt = $con->prepare("SELECT 
                                    Reservation.Id,
                                    Reservation.Status,
                                    Reservation.StartDate, 
                                    Reservation.EndDate, 
                                    PickUpLocation.State AS PickUpCity, 
                                    DropOffLocation.State AS DropOffCity, 
                                    ClassCar.Name AS className, 
                                    Customer.FName AS CustomerFirstName, 
                                    Customer.LName AS CustomerLastName
                                FROM 
                                    Reservation
                                INNER JOIN 
                                    ClassCar ON Reservation.ClassCarName = ClassCar.Name
                                INNER JOIN 
                                    Customer ON Reservation.CustomerID = Customer.ID
                                INNER JOIN 
                                    Location AS PickUpLocation ON Reservation.PickUpLocation = PickUpLocation.ID
                                INNER JOIN 
                                    Location AS DropOffLocation ON Reservation.DropOffLocation = DropOffLocation.ID
                                WHERE 
                                    Reservation.Status = 'Cancelled';");
                                    $stmt->execute(array());
                                    $rows = $stmt->fetchAll();
                                    $count = $stmt->rowCount();

                                    if($count == 0)
                                    {

                                        echo "<tr>";
                                            echo "<td colspan='5' style='text-align:center;'>";
                                                echo "List of your canceled reservations will be presented here";
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                    else
                                    {

                                        foreach($rows as $row)
                                        {
                                            echo "<tr>";
                                            echo "<td>";
                                            echo $row['Status'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['StartDate'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['PickUpCity'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['EndDate'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['DropOffCity'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['className'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo $row['CustomerFirstName'], ' ', $row['CustomerLastName'];
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
<script>
    function checkAgreement(id) {
                console.log(id);
            
                $.ajax({
                    url: 'check_agreement.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        console.log("getting response");
                console.log(response);
                console.log("Response length:", response.length);
                console.log("Response trimmed:", response.trim());
                console.log("Response characters:", response.split(''));
                        if (response.trim() == "exists") {
                            console.log("Condition met: exists");
                    // Redirect to agreement.php if agreement exists
                    window.location.href = 'agreement.php?id=' + id;
                } else {
                    console.log("Condition not met: response does not equal 'exists'");
                    // Show modal if agreement doesn't exist
                    console.log(id);
                    openModal();
                        }
                    } 
                });
            };

        function openModal(){
            $('<?php echo $agreement_data; ?>').modal('show');
        }

        function checkInvoice(id) {
                console.log(id);
            
                $.ajax({
                    url: 'check_invoice.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        console.log("getting response");
                console.log(response);
                console.log("Response length:", response.length);
                console.log("Response trimmed:", response.trim());
                console.log("Response characters:", response.split(''));
                        if (response.trim() == "exists") {
                            console.log("Condition met: exists");
                    // Redirect to agreement.php if agreement exists
                    window.location.href = 'invoice.php?id=' + id;
                } else {
                    console.log("Condition not met: response does not equal 'exists'");
                    // Show modal if agreement doesn't exist
                    console.log(id);
                    openModal();
                        }
                    } 
                });
            };

        function openModal(){
            $('<?php echo $agreement_data; ?>').modal('show');
        }
</script>

<?php
        
	    //Include Footer
	    include 'Includes/templates/footer.php';
	}
	else
    {
    	header('Location: index.php');
        exit();
    }

?>
