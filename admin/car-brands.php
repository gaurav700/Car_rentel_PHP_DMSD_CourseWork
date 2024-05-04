<?php
    session_start();

    //Page Title
    $pageTitle = 'Car Brands';

    //Includes
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';

    //Check If user is already logged in
    if(isset($_SESSION['username_yahya_car_rental']) && isset($_SESSION['password_yahya_car_rental']))
    {
        $sql_car_types = "SELECT * FROM classcar";
        $result_car_types = $con->query($sql_car_types);

        $sql_location = "SELECT * FROM location";
        $result_locations = $con->query($sql_location);
?>
        <!-- Begin Page Content -->
        <div class="container-fluid">
    
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Car Brands</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i>
                    Generate Report
                </a>
            </div>
            <!-- ADD NEW BRAND SUBMITTED -->
            <?php
                if (isset($_POST['add_brand_sbmt']) && $_SERVER['REQUEST_METHOD'] === 'POST')
                try {
                    $car_VIN = test_input($_POST['VIN']);
                    $location = test_input($_POST['location']);
                    $car_type = test_input($_POST['car_type']);
                    $make = test_input($_POST['make']);
                    $model = test_input($_POST['model']);
                    $color = test_input($_POST['color']);
                    $year = test_input($_POST['year']);
                    $brand_image = rand(0, 100000) . '_' . $_FILES['brand_image']['name'];
                    move_uploaded_file($_FILES['brand_image']['tmp_name'], "Uploads/images//" . $brand_image);
                
                    // Insert data into the Model table
                    $stmt_model = $con->prepare("INSERT INTO Model (Make, Year, Name, ClassCarName) VALUES (?, ?, ?, ?)");
                    $stmt_model->execute([$make, $year, $model, $car_type]);
                
                    
                    
                    // Get the ID of the inserted model
                    $model_id_stmt = $con->query("SELECT LAST_INSERT_ID()");
                    $model_id = $model_id_stmt->fetchColumn();
                    // Insert data into the Car table

                    $stmt_location = $con->prepare("SELECT Id FROM location WHERE state = ?");
                    $stmt_location->execute([$location]); // Assuming $state is the variable holding the state value
                    $location_id = $stmt_location->fetchColumn();



                    $stmt_car = $con->prepare("INSERT INTO Car (VIN, ModelID, Color, ClassCarName, brand_image, LocationID) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt_car->execute([$car_VIN, $model_id, $color, $car_type, $brand_image, $location_id]);
                
                    echo "<div class='alert alert-success'>";
                    echo 'New Car Brand has been inserted successfully';
                    echo "</div>";
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>";
                    echo 'Error occurred: ' . $e->getMessage();
                    echo "</div>";
                }
                
                if (isset($_POST['delete_brand_sbmt']) && $_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    $brand_id = $_POST['Id'];
                    try
                    {
                        $stmt_delete_cars = $con->prepare("DELETE FROM car WHERE ModelId = ?");
                        $stmt_delete_cars->execute([$brand_id]); // Assuming $modelId is the ID of the model to be deleted

                        // 2. Delete the model
                        $stmt_delete_model = $con->prepare("DELETE FROM model WHERE Id = ?");
                        $stmt_delete_model->execute([$brand_id]);
                        echo "<div class = 'alert alert-success'>";
                            echo 'Car Brand has been deleted successfully';
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
            <!-- Car Brands Table -->
                            <?php
                $stmt = $con->prepare("SELECT m.Make, m.Year, m.Name AS Model_Name, c.Color, m.Id
                                    FROM Car c
                                    JOIN Model m ON c.ModelID = m.Id");
                $stmt->execute();
                $rows = $stmt->fetchAll();
                ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Car Brands</h6>
                </div>
                <div class="card-body">

                    <!-- ADD NEW BRAND BUTTON -->
                    <button class="btn btn-success btn-sm" style="margin-bottom: 10px;" type="button" data-toggle="modal" data-target="#add_new_brand" data-placement="top">
                        <i class="fa fa-plus"></i> 
                        Add New Brand
                    </button>

                    <!-- Add New Brand Modal -->
                    <div class="modal fade" id="add_new_brand" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Brand</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="car-brands.php" method = "POST" @submit="checkForm"  enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="brand_name">VIN</label>
                                            <input type="text" id="brand_name_input" class="form-control" placeholder="VN45 342" name="VIN" v-model="VIN">
                                            <div class="invalid-feedback" style = "display:block" v-if="VIN === null">
                                                Vehicle indentifaction Number is required
                                            </div>
                                        </div>
                                        <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" class="form-control" name="location" v-model="location" list="locationOptions" @input="loadModels" placeholder="location">
                                        <datalist id="locationOptions">
                                                <?php
                                                if ($result_locations->rowCount() > 0) {
                                                    foreach ($result_locations as $row) {
                                                        echo "<option value='" . $row['State'] . "'>";
                                                    }
                                                }
                                                ?>
                                        </datalist>
                                            <div class="invalid-feedback" style = "display:block" v-if="car_type === null">
                                                CarType name is required
                                            </div>
                                        </div>
                                        <div class="form-group">
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
                                                CarType name is required
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="brand_name">Make</label>
                                            <input type="text" id="brand_name_input" class="form-control" placeholder="BMW" name="make" v-model="make">
                                            <div class="invalid-feedback" style = "display:block" v-if="make === null">
                                                Brand Name is required
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="brand_name">Model</label>
                                            <input type="text" id="brand_name_input" class="form-control" placeholder="7 series" name="model" v-model="model">
                                            <div class="invalid-feedback" style = "display:block" v-if="model === null">
                                            Model name is required
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="brand_name">Color</label>
                                            <input type="text" id="brand_name_input" class="form-control" placeholder="Yellow" name="color" v-model="color">
                                            <div class="invalid-feedback" style = "display:block" v-if="color === null">
                                            Color name is required
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="brand_name">Year</label>
                                            <input type="text" id="brand_name_input" class="form-control" placeholder="2018" name="year" v-model="year">
                                            <div class="invalid-feedback" style = "display:block" v-if="year === null">
                                            Year name is required
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-info" name = "add_brand_sbmt">Add Brand</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Brands Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Make</th>
                                    <th>Year</th>
                                    <th>Model Name</th>
                                    <th>Color</th>
                                    <th>Manage</th>
                                </tr>
                            </thead> 
                            <tbody>
                                <?php
                                foreach($rows as $row)
                                {
                                    echo "<tr>";
                                    echo "<td>";
                                    echo $row['Make']; 
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['Year']; 
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['Model_Name']; 
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['Color']; 
                                    echo "</td>";
                                        echo "<td>";
                                            $delete_data = "delete_".$row["Id"];
                                            ?>
                                            <!-- DELETE BUTTON -->
                                            <ul>
                                                <li class="list-inline-item" data-toggle="tooltip" title="Delete">
                                                    <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $delete_data; ?>" data-placement="top"><i class="fa fa-trash"></i></button>
                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="<?php echo $delete_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $delete_data; ?>" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="car-brands.php" method = "POST">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Delete Brand</h5>
                                                                        <input type="hidden" value = "<?php echo $row['Id']; ?>" name = "Id">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete this Brand "<?php echo $row['Model_Name']; ?>"?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" name = "delete_brand_sbmt" class="btn btn-danger">Delete</button>
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
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
  
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


<script>

new Vue({
    el: "#add_new_brand",
    data: {
        errors: [],
        brand_name: '',
        brand_image: ''
    },
    methods:{
        checkForm: function(event){
            if(this.brand_image)
            {
                return true;
            }

            this.errors = [];

            
            if( !this.brand_image)
            {
                this.errors.push("Brand image is required");
                this.brand_name = null;
            }            


            event.preventDefault();
        },
        onFileChange(e) {
            const file = e.target.files[0];
            this.brand_image = URL.createObjectURL(file);
        }
    }
})


</script>