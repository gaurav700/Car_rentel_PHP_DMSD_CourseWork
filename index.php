<?php 
    #PHP INCLUDES
    include "connect.php";
    include "Includes/templates/header.php";
    include "Includes/templates/navbar.php";

    try {
        // Fetch car types from the database
        $sql_car_types = "SELECT * FROM classcar";
        $result_car_types = $con->query($sql_car_types);
    
        // Fetch car models from the database
        $sql_car_models = "SELECT * FROM model";
        $result_car_models = $con->query($sql_car_models);

                // Fetch location from the database
                $sql_locations = "SELECT * FROM location";
                $result_locations = $con->query($sql_locations);
    } catch (PDOException $ex) {
        echo "An error occurred while fetching data: " . $ex->getMessage();
    }
?>
<!-- Home Section -->
<section class = "home_section">
    <div class="section-header">
        <div class="section-title" style = "font-size:50px; color:white">
            Find Best Car & Limousine
        </div>
        <hr class="separator">
		<div class="section-tagline">
            From as low as $10 per day with limited time offer discounts
		</div>					
	</div>
</section>

<!-- Our Brands Section -->
<section class = "our-brands" id = "brands">
    <div class = "container">
        <div class="section-header">
            <div class="section-title">
                First Class Car Rental & Limousine Services
            </div>
            <hr class="separator">
            <div class="section-tagline">
                We offer professional car rental & limousine services in our range of high-end vehicles
            </div>
        </div>
        <div class = "car-brands">
            <div class = "row">
            <?php

                $stmt = $con->prepare("Select * from car_brands");
                $stmt->execute();
                $car_brands = $stmt->fetchAll();

                foreach($car_brands as $car_brand)
                {
                    $car_brand_img = "admin/Uploads/images/".$car_brand['brand_image'];
                    ?>
                    <div class = "col-md-4">
                        <div class = "car-brand" style = "background-image: url(<?php echo $car_brand_img ?>);">
                            <div class = "brand_name">
                                <h3>
                                    <?php echo $car_brand['brand_name']; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <?php
                }

            ?>
            </div>
        </div>
    </div>
</section>

<!-- CAR RESERVATION SECTION -->
<section class="reservation_section" style = "padding:50px 0px" id = "reserve">
	<div class="container">
		<div class = "row">
			<div class = "col-md-12">
				<form method="POST" action = "reserve.php" class = "car-reservation-form" id = "reservation_form" v-on:submit = "checkForm">
					<div class="text_header">
						<span>
							Find your car
						</span>
					</div>
					<div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="car_type">Car Type</label>
                                <input type="text" class="form-control" name="car_type" v-model="car_type" list="carTypeOptions" @input="loadModels" placeholder="Hatchback">
                                <datalist id="carTypeOptions">
                                    <?php
                                    if ($result_car_types->rowCount() > 0) {
                                        foreach ($result_car_types as $row) {
                                            echo "<option value='" . $row['Name'] . "'>";
                                        }
                                    }
                                    ?>
                                </datalist>
                                <div class="invalid-feedback" style = "display:block" v-if="car_type === null">
								Car Type is required
							</div>
                            </div>
                        </div>
                        <div class="row">
                        <div class = "form-group col-md-6">
                            <label for="pickup_location">Pickup Location</label>
                            <input type="text" class="form-control" name="pickup_location" v-model="pickup_location" list="pickuplocation" @input="loadModels" placeholder = "Califronia" > 
                            <datalist id="pickuplocation">
                            <?php
                            if ($result_locations->rowCount() > 0) {
                                foreach ($result_locations as $row) {
                                    echo "<option value='" . $row['State'] . "'>";
                                }
                            }
                            ?>
                        </datalist>
                            <div class="invalid-feedback" style = "display:block" v-if="pickup_location === null">
                                Pickup location is required
                            </div>
                        </div>
                        
                        <div class = "form-group col-md-6">
                        <label for="pickup_location">Return Location</label>
                            <input type="text" class="form-control" name="return_location" v-model="return_location" list="pickuplocation" @input="loadModels" placeholder = "Texas">
                            <datalist id="pickuplocation">
                            <?php
                            if ($result_locations->rowCount() > 0) {
                                foreach ($result_locations as $row) {
                                    echo "<option value='" . $row['State'] . "'>";
                                }
                            }
                            ?>
                            </datalist>
                            <div class="invalid-feedback" style = "display:block" v-if="return_location === null">
                                Return location is required
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class = "form-group col-md-6">
                            <label for="pickup_date">Pickup Date</label>
                            <input type = "date" min = "<?php echo date('Y-m-d', strtotime("+1 day"))?>" name = "pickup_date" class = "form-control" v-model = 'pickup_date'>
                            <div class="invalid-feedback" style = "display:block" v-if="pickup_date === null">
                                Pickup date is required
                        </div>
                    </div>
						<div class = "form-group col-md-6">
							<label for="return_date">Return Date</label>
							<input type = "date" min = "<?php echo date('Y-m-d', strtotime("+2 day"))?>" name = "return_date"  class = "form-control" v-model = 'return_date'>
							<div class="invalid-feedback" style = "display:block" v-if="return_date === null">
								Return date is required
							</div>
						</div>
                        </div>
						<!-- Submit Button -->
						<button type="submit" class="btn sbmt-bttn" name = "reserve_car">Book Instantly</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>


<!-- Footer Section -->
<section class="widget_section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="footer_widget">
                    <a class="navbar-brand" href="">
                        Yah<span style = "color:#04DBC0">Ya</span>&nbsp;CarRental
                    </a>
                    <p>
                        Getting dressed up and traveling with good friends makes for a shared, unforgettable experience.
                    </p>
                    <ul class="widget_social">
                        <li><a href="#" data-toggle="tooltip" title="Facebook"><i class="fab fa-facebook-f fa-2x"></i></a></li>
                        <li><a href="#" data-toggle="tooltip" title="Twitter"><i class="fab fa-twitter fa-2x"></i></a></li>
                        <li><a href="#" data-toggle="tooltip" title="Instagram"><i class="fab fa-instagram fa-2x"></i></a></li>
                        <li><a href="#" data-toggle="tooltip" title="LinkedIn"><i class="fab fa-linkedin fa-2x"></i></a></li>
                        <li><a href="#" data-toggle="tooltip" title="Google+"><i class="fab fa-google-plus-g fa-2x"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="footer_widget">
                    <h3>Contact Info</h3>
                    <ul class = "contact_info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>962 Fifth Avenue, 3rd Floor New York, NY10022
                        </li>
                        <li>
                            <i class="far fa-envelope"></i>contact@barbershop.com
                        </li>
                        <li>
                            <i class="fas fa-mobile-alt"></i>+123 456 789 101
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="footer_widget">
                    <h3>Newsletter</h3>
                    <p style = "margin-bottom:0px">Don't miss a thing! Sign up to receive daily deals</p>
                    <div class="subscribe_form">
                        <form action="#" class="subscribe_form" novalidate="true">
                            <input type="email" name="EMAIL" id="subs-email" class="form_input" placeholder="Email Address...">
                            <button type="submit" class="submit">SUBSCRIBE</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BOTTOM FOOTER -->
<?php include "Includes/templates/footer.php"; ?>


<script>

new Vue({
    el: "#reservation_form",
    data: {
        car_type: '',
        car_model: '',
        pickup_location: '',
        return_location: '',
        pickup_date: '',
		return_date: ''
    },
    methods:{
        checkForm: function(event){
            if( this.pickup_location && this.return_location && this.pickup_date && this.return_date && this.car_type)
            {
                return true;
            }
            
            if (!this.pickup_location)
            {
                this.pickup_location = null;
            }

            if (!this.return_location)
            {
                this.return_location = null;
            }

            if (!this.pickup_date)
            {
                this.pickup_date = null;
            }

			if (!this.return_date)
            {
                this.return_date = null;
            }
            
            if (!this.car_type)
            {
                this.car_type = null;
            }

            event.preventDefault();
        },
    }
})


</script>