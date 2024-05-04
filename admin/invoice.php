<?php
session_start();

// Page Title
$pageTitle = 'agreement';

// Includes
include 'connect.php';
include 'Includes/functions/functions.php'; 

// Check if user is already logged in
if (isset($_SESSION['username_yahya_car_rental']) && isset($_SESSION['password_yahya_car_rental'])) {

  if (isset($_POST['Confim_return_sbmt'])) {
    $reservation_id = $_POST['ID'];
    $EndOdometer = $_POST['EndOdometer'];

    $updateStmt = $con->prepare("UPDATE agreement SET EndOdometer = ? WHERE ReservationID = ?");
    $updateStmt->execute([$EndOdometer, $reservation_id]);

    $stmt = $con->prepare("SELECT Reservation.Id, Customer.FName AS CustomerFirstName, agreement.InvoiceID,
    agreement.StartTime, agreement.RtnTime, agreement.StartDate, agreement.RtnDate,
    ClassCar.WeeklyRate, ClassCar.DailyRate FROM Reservation 
    INNER JOIN Customer ON Reservation.CustomerID = Customer.Id 
    INNER JOIN agreement ON agreement.ReservationID = Reservation.Id 
    INNER JOIN ClassCar ON Reservation.classcarname = ClassCar.Name
    Where Reservation.Id = ?");
    $stmt->execute([$reservation_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();
          $startDate = $row['StartDate'];
          $RtnDate = $row['RtnDate'];
          $StartTime = $row['StartTime'];
          $RtnTime = $row['RtnTime'];
          $weeklyRateString = $row['WeeklyRate'];
          $dailyRateString = $row['DailyRate'];
          
          $weeklyRate = (float)preg_replace('/[^0-9.]/', '', $weeklyRateString);
          $dailyRate = (float)preg_replace('/[^0-9.]/', '', $dailyRateString);

          $startTimestamp = strtotime("$startDate $StartTime");
          $rtnTimestamp = strtotime("$RtnDate $RtnTime");
          
          $timeDiffInSeconds = $rtnTimestamp - $startTimestamp;
          
          $timeDiffInDays = $timeDiffInSeconds / (60 * 60 * 24);
          
          $timeDiffInDays = round($timeDiffInDays, 2);
          
          if ($timeDiffInDays > 7) {
              $weeks = floor($timeDiffInDays / 7);
              $remainingDays = $timeDiffInDays % 7;

              $total_price = $weeks*$weeklyRate + $remainingDays * $dailyRate;
          }else{
            $total_price = $timeDiffInDays * $dailyRate;
          }
  }
  if(isset($_GET['id'])) {
    $reservation_id = $_GET['id'];
    try{
          $stmt = $con->prepare("SELECT Reservation.Id, Customer.FName AS CustomerFirstName, agreement.InvoiceID,
          agreement.StartTime, agreement.RtnTime, agreement.StartDate, agreement.RtnDate,
          ClassCar.WeeklyRate, ClassCar.DailyRate FROM Reservation 
          INNER JOIN Customer ON Reservation.CustomerID = Customer.Id 
          INNER JOIN agreement ON agreement.ReservationID = Reservation.Id 
          INNER JOIN ClassCar ON Reservation.classcarname = ClassCar.Name
          Where Reservation.Id = ?");
          $stmt->execute([$reservation_id]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          $newStmt = $con->prepare("SELECT * FROM Invoice Where ID = ?");
          $newStmt->execute([$row['InvoiceID']]);
          $Newrow = $newStmt->fetch(PDO::FETCH_ASSOC);
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
  <title>Invoice Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
    }
    th {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <h1>Invoice Details</h1>
  <form id="invoiceForm">
    <table>
      <tr>
        <th>Field</th>
        <th>Value</th>
      </tr>
      <tr>
        <td>Registration Id:</td>
        <td><?php echo $row['Id']; ?></td>
        <input type="hidden" name="Id" value="<?php echo $row['Id']; ?>"></td>
      </tr>
      <tr>
        <td>Customer Name:</td>
        <td> <?php echo $row['CustomerFirstName']; ?></td>
      </tr>
      <tr>
    <td>Invoice Date:</td>
    <td><?php echo (isset($Newrow['Date'])) ? $Newrow['Date'] : date("Y-m-d"); ?></td>
</tr>
<tr>
    <td>Amount Due:</td>
    <td><input type="text" id="amount" name="amount" oninput="calculateTotal()" value="<?php echo (isset($Newrow['Total'])) ? ($Newrow['Total'] - $Newrow['Taxes']) : $total_price; ?>"></td>
</tr>
<tr>
    <td>Status:</td>
    <td><input type="text" name="status" placeholder="Paid/Unpaid" value="<?php echo (isset($Newrow['Status'])) ? $Newrow['Status'] : "Paid"; ?>"></td>
</tr>
<tr>
    <td>Taxes (10%):</td>
    <td><input type="text" id="taxes" name="taxes" oninput="calculateTotal()" value="<?php echo (isset($Newrow['Taxes'])) ? $Newrow['Taxes'] : ($total_price * 10)/100; ?>"></td>
</tr>
<tr>
    <td>Total:</td>
    <td><input type="text" id="total" name="total" readonly value="<?php echo (isset($Newrow['Total'])) ? $Newrow['Total'] : ($total_price + (($total_price*10)/100)); ?>"></td>
</tr>
      
<?php if (!empty($Newrow['Total'])): ?>
    <tr>
        <td colspan="2"><button type="button" onclick="printPage()">Print</button></td>
    </tr>
<?php else: ?>
    <tr>
        <td colspan="2"><button type="button" onclick="submitForm()">Generate Invoice</button></td>
    </tr>
<?php endif; ?>

    </table>
  </form>

  <script>
    function submitForm() {
    // Get form data
    var formData = new FormData(document.getElementById("invoiceForm"));

    // Send AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "insert_invoice.php", true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Upon successful insertion, print the page
          window.print();
        } else {
          console.error("Error:", xhr.statusText);
        }
      }
    };
    xhr.send(formData);
  }
    function calculateTotal() {
      var amount = parseFloat(document.getElementById("amount").value);
      var taxes = parseFloat(document.getElementById("taxes").value);
      var total = amount + taxes;
      document.getElementById("total").value = total.toFixed(2);
    }
    function printPage() {
        window.print();
    }
  </script>
</body>
</html>
<?php 
} else {
    header('Location: index.php');
    exit();
}
?>