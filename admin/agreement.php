<?php
session_start();

// Page Title
$pageTitle = 'agreement';

// Includes
include 'connect.php';
include 'Includes/functions/functions.php'; 

// Check if user is already logged in
if (isset($_SESSION['username_yahya_car_rental']) && isset($_SESSION['password_yahya_car_rental'])) {
    if (isset($_POST['Confim_reservation_sbmt'])) {
        $reservation_id = $_POST['ID'];
        $StartOdometer = $_POST['StartOdometer'];
        try {
            $stmt = $con->prepare("SELECT 
                Reservation.Id,Reservation.StartDate,  Reservation.EndDate,   Reservation.StartTime, Reservation.EndTime,
                Car.VIN, Car.Color,
                Model.Make, Model.Year, Model.Name,
                PickUpLocation.State AS PickUpCity,  DropOffLocation.State AS DropOffCity, 
                ClassCar.Name AS className,  ClassCar.WeeklyRate,ClassCar.DailyRate,
                Customer.FName AS CustomerFirstName,  Customer.LicenseNumber, Customer.LicenseStateIssued, Customer.Phone,Customer.State, Customer.Street,
                Customer.City, Customer.Zip, Customer.CreditCardType, Customer.CreditCardNumber
            FROM  Reservation
            INNER JOIN  ClassCar ON Reservation.ClassCarName = ClassCar.Name
            INNER JOIN  Car ON Reservation.ClassCarName = Car.ClassCarName
            INNER JOIN  Model ON Reservation.ClassCarName = Model.ClassCarName
            INNER JOIN  Customer ON Reservation.CustomerID = Customer.ID
            INNER JOIN  Location AS PickUpLocation ON Reservation.PickUpLocation = PickUpLocation.ID
            INNER JOIN Location AS DropOffLocation ON Reservation.DropOffLocation = DropOffLocation.ID
            WHERE  Reservation.Id = ?");
            $stmt->execute([$reservation_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $selectStmt = $con->prepare("SELECT ID FROM agreement WHERE ID = ?");
            $selectStmt->execute([$reservation_id]);
            if ($selectStmt->rowCount() === 0) {
              $insertStmt = $con->prepare("INSERT INTO agreement (ID, CNumber, StartDate, RtnDate, StartTime, RtnTime, StartOdometer, EndOdometer, CarVIN, ReservationID, InvoiceID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $insertStmt->execute([  $row['Id'], $row['LicenseNumber'], $row['StartDate'], $row['EndDate'], $row['StartTime'],$row['EndTime'],                  
                  $StartOdometer, null,$row['VIN'], $reservation_id,null]);
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if(isset($_GET['id'])) {
      $reservation_id = $_GET['id'];
      try{
        $stmt = $con->prepare("SELECT 
                Reservation.Id,Reservation.StartDate,  Reservation.EndDate,   Reservation.StartTime, Reservation.EndTime,
                Car.VIN, Car.Color,
                Model.Make, Model.Year, Model.Name,
                agreement.StartOdometer, agreement.EndOdometer,
                PickUpLocation.State AS PickUpCity,  DropOffLocation.State AS DropOffCity, 
                ClassCar.Name AS className,  ClassCar.WeeklyRate,ClassCar.DailyRate,
                Customer.FName AS CustomerFirstName,  Customer.LicenseNumber, Customer.LicenseStateIssued, Customer.Phone,Customer.State, Customer.Street,
                Customer.City, Customer.Zip, Customer.CreditCardType, Customer.CreditCardNumber
            FROM  Reservation
            INNER JOIN agreement on agreement.ID = Reservation.ID
            INNER JOIN  ClassCar ON Reservation.ClassCarName = ClassCar.Name
            INNER JOIN  Car ON Reservation.ClassCarName = Car.ClassCarName
            INNER JOIN  Model ON Reservation.ClassCarName = Model.ClassCarName
            INNER JOIN  Customer ON Reservation.CustomerID = Customer.ID
            INNER JOIN  Location AS PickUpLocation ON Reservation.PickUpLocation = PickUpLocation.ID
            INNER JOIN Location AS DropOffLocation ON Reservation.DropOffLocation = DropOffLocation.ID
            WHERE  Reservation.Id = ?");
            $stmt->execute([$reservation_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
      }
      catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Rental Agreement</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 0 auto;
    }
    h1 {
      text-align: center;
    }
    h2 {
      margin-top: 20px;
    }
    p {
      margin-bottom: 10px;
    }
    .signature {
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Car Rental Agreement</h1>
  
    <h2>1. Vehicle Details:</h2>
    <p>The Rental Company agrees to rent to the Renter the following vehicle:</p>
    <ul>
        <li>VIN: <?php echo $row['VIN']; ?></li>
        <li>Color: <?php echo $row['Color']; ?></li>
        <li>Make: <?php echo $row['Make']; ?></li>
        <li>Year: <?php echo $row['Year']; ?></li>
        <li>Model Name: <?php echo $row['Name']; ?></li>
        <li>Class Name: <?php echo $row['className']; ?></li>
        <li>Customer Name: <?php echo $row['CustomerFirstName'];?></li>
        <li>Contact Info: <?php echo $row['Phone'];?></li>
        <li>License Number: <?php echo $row['LicenseNumber'];?></li>
        <li>License State Issued: <?php echo $row['LicenseStateIssued'];?></li>
        <li>Customer Address: <?php echo $row['Street'];?> , <?php echo $row['City'];?> , <?php echo $row['State'];?> ,  <?php echo $row['Zip'];?> </li>
        <li>Start Odometer Reading : <?php echo isset($row['StartOdometer']) ? $row['StartOdometer'] : $StartOdometer; ?> </li>
        <li>End Odometer Reading : <?php echo isset($row['EndOdometer']) ? $row['EndOdometer'] : "Ride is not ended yet"; ?></li>
    </ul>

    <h2>2. Rental Period:</h2>
    <p>The rental period shall commence on <?php echo $row['StartTime']; ?> at <?php echo $row['StartDate']; ?> and terminate on <?php echo $row['EndTime']; ?> at <?php echo $row['EndDate']; ?>.</p>

    <h2>3. Rental Fee:</h2>
    <p>The Renter agrees to pay a total rental fee of <?php echo $row['WeeklyRate']; ?> and <?php echo $row['DailyRate']; ?> for the rental period, payable in advance. Additional charges may apply for mileage exceeding the agreed-upon limit, fuel refill, late returns, or any damages incurred during the rental period.</p>

    <h2>4. Payment:</h2>
    <p>Payment shall be made by <?php echo $row['CreditCardType']; ?> (<?php echo $row['CreditCardNumber']; ?>) at the time of vehicle pickup. A security deposit of $500 is required and shall be refunded upon the return of the vehicle in its original condition, less any applicable charges.</p>

    <h2>5. Insurance:</h2>
    <p>The Rental Company shall provide insurance coverage for the vehicle as required by law. The Renter is responsible for any deductibles associated with insurance claims.</p>

    <h2>6. Use of Vehicle:</h2>
    <p>The Renter agrees to use the vehicle solely for personal use and not for any commercial or unlawful purposes. The vehicle shall not be driven by any person other than the Renter unless authorized by the Rental Company.</p>

    <h2>7. Maintenance and Repairs:</h2>
    <p>The Renter shall be responsible for all routine maintenance and repairs during the rental period, including but not limited to oil changes and tire replacements. The Rental Company must be notified immediately of any mechanical issues or damages to the vehicle.</p>

    <h2>8. Return of Vehicle:</h2>
    <p>The Renter agrees to return the vehicle to the Rental Company's premises at the end of the rental period in the same condition as received, subject to reasonable wear and tear. Any delays in return shall be communicated to the Rental Company in advance.</p>

    <h2>9. Liability:</h2>
    <p>The Renter acknowledges and agrees that the Rental Company shall not be liable for any damages, losses, or injuries arising from the Renter's use or operation of the vehicle during the rental period.</p>

    <h2>10. Governing Law:</h2>
    <p>This Agreement shall be governed by and construed in accordance with the laws of United State of America, without regard to its conflict of law principles.</p>

    <h2>11. Entire Agreement:</h2>
    <p>This Agreement constitutes the entire understanding between the parties concerning the subject matter herein and supersedes all prior agreements and understandings, whether written or oral.</p>

    <div class="signature">
    <p>Rental Company Signature: ________________________ Date: <?php echo date("Y-m-d"); ?></p>
    <p>Renter Signature: _______________________________ Date: <?php echo date("Y-m-d"); ?></p>
    </div>
    <div class="row justify-content-center mt-3">
      <div class="col-auto">
        <button class="btn btn-primary" onclick="window.print()">Print</button>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php 
} else {
    header('Location: index.php');
    exit();
}
?>
