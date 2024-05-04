<?php
    session_start();

    //Page Title
    $pageTitle = 'Cars';

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
                <h1 class="h3 mb-0 text-gray-800">Cars</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i>
                    Generate Report
                </a>
            </div>

            <!-- ADD NEW CAR SUBMITTED -->

            <!-- Cars Table -->
            <?php
                $stmt = $con->prepare("SELECT 
                m.Make AS Car_Make,
                m.Name AS Car_Model,
                m.Year AS Car_Year,
                c.Color AS Car_Color,
                c.ClassCarName AS Car_Type
            FROM 
                Car c
            JOIN 
                Model m ON c.ModelID = m.Id
            JOIN 
                ClassCar cc ON c.ClassCarName = cc.Name;"
            );
                $stmt->execute();
                $rows_cars = $stmt->fetchAll();

            ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cars</h6>
                </div>
                <div class="card-body">

                    <!-- Cars Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Car Brand</th>
                                    <th>Car Type</th>
                                    <th>Color</th>
                                    <th>Car Model</th>
                                    <th>Car Year</th>
                                </tr>
                            </thead> 
                            <tbody>
                                <?php
                                foreach($rows_cars as $car)
                                {
                                    echo "<tr>";
                                        echo "<td>";
                                            echo $car['Car_Make'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $car['Car_Type'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $car['Car_Color'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $car['Car_Model'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $car['Car_Year'];
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