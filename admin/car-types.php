<?php
    session_start();

    //Page Title
    $pageTitle = 'Car Types';

    //Includes
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';

    //Check If user is already logged in
    if(isset($_SESSION['username_yahya_car_rental']) && isset($_SESSION['password_yahya_car_rental']))
    {
?>
        <!-- Begin Page Content -->
        <div class="container-fluid">
    
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Car Types</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i>
                    Generate Report
                </a>
            </div>

            <!-- ADD NEW TYPE SUBMITTED -->
            <?php
                if (isset($_POST['add_type_sbmt']) && $_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    $name = test_input($_POST['type_name']);
                    $weekly = test_input($_POST['weekly_rate']);
                    $daily = test_input($_POST['daily_rate']);

                    
                    try
                    {
                        $stmt = $con->prepare("insert into classcar(Name,WeeklyRate, DailyRate) values(?,?,?) ");
                        $stmt->execute(array($name,$weekly, $daily));
                        echo "<div class = 'alert alert-success'>";
                            echo 'New Car Type has been inserted successfully';
                        echo "</div>";
                    }
                    catch(Exception $e)
                    {
                        echo "<div class = 'alert alert-danger'>";
                            echo 'Error occurred: ' .$e->getMessage();
                        echo "</div>";
                    }
                }
                if (isset($_POST['delete_type_sbmt']) && $_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    $name = $_POST['Name'];
                    try
                    {
                        $stmt = $con->prepare("DELETE FROM classcar where Name = ?");
                        $stmt->execute(array($name));
                        echo "<div class = 'alert alert-success'>";
                            echo 'Car Type has been deleted successfully';
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

            <!-- Car Types Table -->
            <?php
                $stmt = $con->prepare("SELECT * FROM classcar");
                $stmt->execute();
                $rows_types = $stmt->fetchAll(); 
            ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Car Types</h6>
                </div>
                <div class="card-body">

                    <!-- ADD NEW TYPE BUTTON -->
                    <button class="btn btn-success btn-sm" style="margin-bottom: 10px;" type="button" data-toggle="modal" data-target="#add_new_type" data-placement="top">
                        <i class="fa fa-plus"></i> 
                        Add New Type
                    </button>

                    <!-- Add New Type Modal -->
                    <div class="modal fade" id="add_new_type" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Type</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="car-types.php" method = "POST" v-on:submit = "checkForm">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="type_label">Name</label>
                                            <input type="text" id="type_label" class="form-control" placeholder="Luxury" name="type_name" v-model = "Name">
                                            <div class="invalid-feedback" style = "display:block" v-if="Name === null">
                                                Type Name is required
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="weekly_rate">WeeklyRate</label>
                                            <input id="weekly_rate" class="form-control" name="weekly_rate" placeholder="$1200/Weekly" v-model = "WeeklyRate"></input>
                                            <div class="invalid-feedback" style = "display:block" v-if="WeeklyRate === null">
                                            WeeklyRate is required
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="daily_rate">DailyRate</label>
                                            <input type="text" id="daily_rate" class="form-control" placeholder="$350/daily" name="daily_rate" v-model = "DailyRate">
                                            <div class="invalid-feedback" style = "display:block" v-if="DailyRate === null">
                                                DailyRate is required
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-info" name="add_type_sbmt">Add Type</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Types Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Weekly Rate</th>
                                    <th>Monthly Rate</th>
                                    <th>Manage</th>
                                </tr>
                            </thead> 
                            <tbody>
                                <?php
                                foreach($rows_types as $type)
                                {
                                    echo "<tr>";
                                        echo "<td>";
                                            echo $type['Name'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $type['WeeklyRate'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $type['DailyRate'];
                                        echo "</td>";
                                        echo "<td>";
                                            $delete_data = "delete_".$type["Name"];
                                            ?>
                                            <!-- DELETE BUTTON -->
                                            <ul>
                                                <li class="list-inline-item" data-toggle="tooltip" title="Delete">
                                                    <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $delete_data; ?>" data-placement="top"><i class="fa fa-trash"></i></button>
                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="<?php echo $delete_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $delete_data; ?>" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="car-types.php" method = "POST">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Delete Type</h5>
                                                                        <input type="hidden" value = "<?php echo $type['Name']; ?>" name = "Name">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete this type "<?php echo $type['Name']; ?>"?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" name = "delete_type_sbmt" class="btn btn-danger">Delete</button>
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
    el: "#add_new_type",
    data: {
        type_label: '',
        type_description: ''
    },
    methods:{
        checkForm: function(event){
            if( this.Name && this.WeeklyRate && this.DailyRate)
            {
                return true;
            }

            if (!this.Name)
            {
                this.Name = null;
            }
            
            if (!this.WeeklyRate)
            {
                this.WeeklyRate = null;
            }

            if (!this.DailyRate)
            {
                this.DailyRate = null;
            }
            
            event.preventDefault();
        },
    }
})


</script>