<?php
	session_start();
    include "connect.php";
    include "Includes/templates/header.php";
    include "Includes/templates/navbar.php";
	include "Includes/functions/functions.php";

	if (isset($_POST['reserve_car']) && $_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$_SESSION['car_type'] = test_input($_POST['car_type']);
		$_SESSION['car_model'] = test_input($_POST['car_model']);
		$_SESSION['pickup_location'] = test_input($_POST['pickup_location']);
		$_SESSION['return_location'] = test_input($_POST['return_location']);
		$_SESSION['pickup_date'] = test_input($_POST['pickup_date']);
		$_SESSION['return_date'] = test_input($_POST['return_date']);
	}
?>

<!-- BANNER SECTION -->
<div class = "reserve-banner-section">
	<h2>
		Reserve your car
	</h2>
</div>

<!-- CAR RESERVATION SECTION -->
<section class="car_reservation_section">
	<div class="container">
		<?php
if(isset($_POST['submit_reservation']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_car = $_POST['selected_car'];
    $full_name = test_input($_POST['full_name']);
    $client_email = test_input($_POST['client_email']);
    $client_phonenumber = test_input($_POST['client_phonenumber']);
    $street = test_input($_POST['street']);
    $city = test_input($_POST['city']);
    $state = test_input($_POST['state']);
    $zip = test_input($_POST['zip']);
    $start_time = test_input($_POST['start_time']);
    $end_time = test_input($_POST['end_time']);
    $license_number = test_input($_POST['license_number']);
    $license_state_issued = test_input($_POST['license_state_issued']);
    $credit_card_type = test_input($_POST['credit_card_type']);
    $credit_card_number = test_input($_POST['credit_card_number']);
    $credit_card_month = test_input($_POST['credit_card_month']);
    $credit_card_year = test_input($_POST['credit_card_year']);
    
    $pickup_location = $_SESSION['pickup_location'];
    $return_location = $_SESSION['return_location'];
    $pickup_date = $_SESSION['pickup_date'];
    $return_date = $_SESSION['return_date'];
    
    $con->beginTransaction();

	try {
// Verify if the pickup location exists in the Location table
$stmtPickUpLocation = $con->prepare("SELECT ID FROM Location WHERE State = ?");
$stmtReturnLocation = $con->prepare("SELECT ID FROM Location WHERE State = ?");
$stmtPickUpLocation->execute(array($pickup_location));
$stmtReturnLocation->execute(array($return_location));
$locationExists1 = $stmtPickUpLocation->fetch();
$locationExists2 = $stmtReturnLocation->fetch();

// Get car details including ClassCarName
$stmtCar = $con->prepare("SELECT ClassCarName FROM Car WHERE VIN = ?");
$stmtCar->execute(array($selected_car));
$carDetails = $stmtCar->fetch(PDO::FETCH_ASSOC);
$classCarName = $carDetails['ClassCarName'];

// Inserting Client Details
$stmtClient = $con->prepare("INSERT INTO Customer (FName, LName, Phone, LicenseNumber, LicenseStateIssued, CreditCardType, CreditCardNumber, CreditCardMonth, CreditCardYear, State, Street, City, Zip) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmtClient->execute(array($full_name, '', $client_phonenumber, $license_number, $license_state_issued, $credit_card_type, $credit_card_number, $credit_card_month, $credit_card_year, $state, $street, $city, $zip));

$client_id = $con->lastInsertId(); // Retrieve the last inserted ID

// Inserting Reservation Details
$stmtReservation = $con->prepare("INSERT INTO Reservation (RevType, Status, StartDate, EndDate, StartTime, EndTime, PickUpLocation, DropOffLocation, CustomerID, ClassCarName) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmtReservation->execute(array('Instant', 'Pending', $pickup_date, $return_date, $start_time, $end_time, $locationExists1['ID'], $locationExists2['ID'], $client_id, $classCarName));

echo "<div class='alert alert-success'>";
echo "Great! Your reservation has been created successfully.";
echo "</div>";
	
		$con->commit();
	} catch (Exception $e) {
		// Handle any potential exceptions
		$con->rollBack();
		echo "<div class='alert alert-danger'>";
		echo "Error: " . $e->getMessage();
		echo "</div>";
	}
	

}
			elseif (isset($_SESSION['pickup_date']) && isset($_SESSION['return_date'])) {
				$car_type = $_SESSION['car_type'];
				$car_model = $_SESSION['car_model'];
				$pickup_location = $_SESSION['pickup_location'];
				$return_location = $_SESSION['return_location'];
				$pickup_date = $_SESSION['pickup_date'];
				$return_date = $_SESSION['return_date'];
			
				// Prepare the SQL statement
				$stmt = $con->prepare("SELECT 
				c.VIN,
				m.Make AS Model,
				m.Name AS model_name,
				m.Year,
				c.ClassCarName,
				c.Color,
				cc.WeeklyRate,
				cc.DailyRate
			FROM 
				Car c
			JOIN 
				Model m ON c.ModelID = m.Id
			JOIN 
				ClassCar cc ON c.ClassCarName = cc.Name
			WHERE 
				c.ClassCarName = :car_type");

			
				// Bind parameters
				$stmt->bindParam(':car_type', $car_type);
				$stmt->execute();
			
				// Fetch the results if needed
				$available_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
				?>
					<form action = "reserve.php" method = "POST" id="reservation_second_form" v-on:submit = "checkForm">
						<div class = "row" style = "margin-bottom: 20px;">
							<div class = "col-md-3 reservation_cards">
								<p>
									<i class="fas fa-calendar-alt"></i>
									<span>Pickup Date : </span><?php echo $_SESSION['pickup_date']; ?>
								</p>
							</div>
							<div class = "col-md-3 reservation_cards">
								<p>
									<i class="fas fa-calendar-alt"></i>
									<span>Return Date : </span><?php echo $_SESSION['return_date']; ?>
								</p>
							</div>
							<div class = "col-md-3 reservation_cards">
								<p>
									<i class="fas fa-map-marked-alt"></i>
									<span>Pickup Location : </span><?php echo $_SESSION['pickup_location']; ?>
								</p>
							</div>
							<div class = "col-md-3 reservation_cards">
								<p>
									<i class="fas fa-map-marked-alt"></i>
									<span>Return Location : </span><?php echo $_SESSION['return_location']; ?>
								</p>
							</div>
						</div>
						<div class = "row">
							<div class = "col-md-7">
								<div class="btn-group-toggle" data-toggle="buttons">
									<div class="invalid-feedback" style = "display:block;margin: 10px 0px;font-size: 15px;" v-if="selected_car === null">
										Select your car
									</div>
									<div class="items_tab">
										<?php 
										// Loop through the available cars in groups of three
										for ($i = 0; $i < count($available_cars); $i += 2): ?>
											<div class="row">
												<?php 
												// Iterate over each group of three cars
												for ($j = $i; $j < min($i + 2, count($available_cars)); $j++): 
													$car = $available_cars[$j]; ?>
													<div class="col-md-6">
														<div class="card itemListElement mb-4">
															<div class="card-body">
																<div class="card-text">
																	<div><strong>Model:</strong> <?php echo $car['Model']; ?></div>
																	<div><strong>Model Name:</strong> <?php echo $car['model_name']; ?></div>
																	<div><strong>Year:</strong> <?php echo $car['Year']; ?></div>
																	<div><strong>Class:</strong> <?php echo $car['ClassCarName']; ?></div>
																	<div><strong>Color:</strong> <?php echo $car['Color']; ?></div>
																	<div><strong>Weekly Rate:</strong> <?php echo $car['WeeklyRate']; ?></div>
																	<div><strong>Daily Rate:</strong> <?php echo $car['DailyRate']; ?></div>
																</div>
																<div class="item_select_part mt-3 d-flex justify-content-between align-items-center">
																	<label class="item_label btn btn-secondary active">
																		<input type="radio" class="radio_car_select" name="selected_car" v-model="selected_car" value="<?php echo $car['VIN']; ?>"> Select
																	</label> 
																</div>
															</div>
														</div>
													</div>
												<?php endfor; ?>
											</div>
										<?php endfor; ?>
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="client_details credit-card">
									<div class="form-group">
										<label for="full_name">Full Name</label>
										<input type="text" class="form-control" placeholder="John Doe" name="full_name" v-model="full_name">
										<div class="invalid-feedback" style="display:block" v-if="full_name === null">
											Full name is required
										</div>
									</div>
									<div class="form-group">
										<label for="client_email">E-mail</label>
										<input type="email" class="form-control" name="client_email" placeholder="abc@mail.xyz" v-model="client_email">
										<div class="invalid-feedback" style="display:block" v-if="client_email === null">
											E-mail is required
										</div>
									</div>
									<div class="form-group">
										<label for="client_phonenumber">Phone Number</label>
										<input type="text" name="client_phonenumber" placeholder="0123456789" class="form-control" v-model="client_phonenumber">
										<div class="invalid-feedback" style="display:block" v-if="client_phonenumber === null">
											Phone number is required
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-6">
											<label for="street">Street</label>
											<input type="text" name="street" placeholder="Street" class="form-control" v-model="street">
										</div>
										<div class="form-group col-md-6">
											<label for="city">City</label>
											<input type="text" name="city" placeholder="City" class="form-control" v-model="city">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-6">
											<label for="state">State</label>
											<input type="text" name="state" placeholder="State" class="form-control" v-model="state">
										</div>
										<div class="form-group col-md-6">
											<label for="zip">Zip Code</label>
											<input type="text" name="zip" placeholder="Zip Code" class="form-control" v-model="zip">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-6">
											<label for="start_time">Start Time</label>
											<input type="time" name="start_time" class="form-control" v-model="start_time">
											<div class="invalid-feedback" style="display:block" v-if="start_time === null">
											Start Time is required
										</div>
										</div>
										<div class="form-group col-md-6">
											<label for="end_time">End Time</label>
											<input type="time" name="end_time" class="form-control" v-model="end_time">
											<div class="invalid-feedback" style="display:block" v-if="end_time === null">
											End Time is required
										</div>
										</div>
									</div>
									<div class="form-group">
										<label for="license_number">License Number</label>
										<input type="text" name="license_number" placeholder="License Number" class="form-control" v-model="license_number">
										<div class="invalid-feedback" style="display:block" v-if="license_number === null">
											License Number is required
										</div>
									</div>
									<div class="form-group">
										<label for="license_state_issued">License State Issued</label>
										<input type="text" name="license_state_issued" placeholder="License State Issued" class="form-control" v-model="license_state_issued">
										<div class="invalid-feedback" style="display:block" v-if="license_state_issued === null">
											License Issued State is required
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-4">
											<label for="credit_card_type">Credit Card Type</label>
											<input type="text" name="credit_card_type" placeholder="Credit Card Type" class="form-control" v-model="credit_card_type">
											<div class="invalid-feedback" style="display:block" v-if="credit_card_type === null">
											CreditCard Type is required
										</div>
										</div>
										<div class="form-group col-md-8">
											<label for="credit_card_number">Credit Card Number</label>
											<input type="text" name="credit_card_number" placeholder="Credit Card Number" class="form-control cc-number" v-model="credit_card_number">
											<div class="invalid-feedback" style="display:block" v-if="credit_card_number === null">
											CreditCard number is required
										</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-md-6">
											<label for="credit_card_month">Expiry Month</label>
											<input type="text" name="credit_card_month" placeholder="MM" class="form-control cc-exp-month" v-model="credit_card_month">
											<div class="invalid-feedback" style="display:block" v-if="end_credit_card_monthime === null">
											Month is required
										</div>
										</div>
										<div class="form-group col-md-6">
											<label for="credit_card_year">Expiry Year</label>
											<input type="text" name="credit_card_year" placeholder="YYYY" class="form-control cc-exp-year" v-model="credit_card_year">
											<div class="invalid-feedback" style="display:block" v-if="credit_card_year === null">
											Year is required
										</div>
										</div>
									</div>
									<button type="submit" class="btn sbmt-bttn" name="submit_reservation">Book Instantly</button>
								</div>
							</div>
						</div>
						</div>
					</form>
				<?php
			}
			else
			{
				?>
					<div style = "max-width:500px; margin:auto;">
						<div class = "alert alert-warning">
							Please, select first pickup and return date.
						</div>
						<button class = "btn btn-info" style = "display:block;margin:auto">
							<a href="./#reserve" style = "color:white">Homepage</a>
						</button>
					</div>
				<?php
			}
		?>
	</div>
</section>



<!-- FOOTER BOTTOM -->

<?php include "Includes/templates/footer.php"; ?>


<script>

new Vue({
    el: "#reservation_second_form",
    data: {
        selected_car: '',
        full_name: '',
        client_email: '',
        client_phonenumber: '',
        street: '',
        city: '',
        state: '',
        zip: '',
        start_time: '',
        end_time: '',
        license_number: '',
        license_state_issued: '',
        credit_card_type: '',
        credit_card_number: '',
        credit_card_month: '',
        credit_card_year: ''
    },
    methods: {
        checkForm: function(event) {
            if (this.full_name && this.client_email && this.client_phonenumber && this.start_time && this.end_time &&
                this.license_number && this.license_state_issued && this.credit_card_type && this.credit_card_number &&
                this.credit_card_month && this.credit_card_year) {
                return true;
            }

            if (!this.full_name) {
                this.full_name = null;
            }

            if (!this.client_email) {
                this.client_email = null;
            }

            if (!this.client_phonenumber) {
                this.client_phonenumber = null;
            }

            if (!this.start_time) {
                this.start_time = null;
            }

            if (!this.end_time) {
                this.end_time = null;
            }

            if (!this.license_number) {
                this.license_number = null;
            }

            if (!this.license_state_issued) {
                this.license_state_issued = null;
            }

            if (!this.credit_card_type) {
                this.credit_card_type = null;
            }

            if (!this.credit_card_number) {
                this.credit_card_number = null;
            }

            if (!this.credit_card_month) {
                this.credit_card_month = null;
            }

            if (!this.credit_card_year) {
                this.credit_card_year = null;
            }

            event.preventDefault();
        },
    }
})



</script>
