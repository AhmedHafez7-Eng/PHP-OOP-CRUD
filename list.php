<?php
// include database connection file
require_once("db.php");
///////// Connection ///////

$connection = new DB();
$conn = $connection->getConnection();

session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>List All</title>
    <style>
    .toast {
        width: fit-content;
        padding: 20px;
        box-shadow: 0 3px 10px rgb(0 0 0 / 20%);
        position: sticky;
        left: 10px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">CRUD SYS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="list.php">All Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="addStudent.php">Add Student</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Switch Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="studentController.php<?php echo "?logout" ?>">Logout</a>
                </li>
            </ul>


            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <!-- Welcome Message -->
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="mr-auto">Logined Successfully</strong>
            <small class="text-muted">
                Joined now
                <!-- <script>
                // 24th hour format
                // var today = new Date();
                // var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                // document.write(time);

                // 12th hour format
                function formatAMPM(date) {
                    var hours = date.getHours();
                    var minutes = date.getMinutes();
                    var ampm = hours >= 12 ? 'pm' : 'am';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    var strTime = hours + ':' + minutes + ' ' + ampm;
                    return strTime;
                }

                document.write(formatAMPM(new Date));
                </script> -->
            </small>
            <!-- <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> -->
        </div>
        <div class="toast-body">
            Welcome, <h3> <?php
                            //IF login_success  
                            if (isset($_SESSION["Name"])) {
                                echo  $_SESSION["Name"];
                            } else {
                                header("location:login.php");
                            }
                            ?>
            </h3>
        </div>
    </div>

    <!-- Students Data -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <br>
                <a href="addStudent.php"><button class="btn btn-primary"> Add New Student</button></a>
                <br><br>
                <div class="table-responsive">
                    <table id="mytable" class="table table-bordred table-striped">
                        <thead>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Phone</th>
                            <th>Show</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </thead>
                        <tbody>
                            <?php
                            //Prepare the query:
                            $query = $connection->getAllRows('students', '*');
                            //Execute the query:
                            $query->execute();
                            //Assign the data which you pulled from the database (in the preceding step) to a variable.
                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                            // For serial number initialization
                            // $cnt = 1;
                            if ($query->rowCount() > 0) {
                                //In case that the query returned at least one record, we can echo the records within a foreach loop:
                                foreach ($results as $result) {
                            ?>

                            <!-- Display Records -->
                            <tr>
                                <td><?php echo htmlentities($result->id); ?></td>
                                <td><?php echo htmlentities($result->fname); ?></td>
                                <td><?php echo htmlentities($result->lname); ?></td>
                                <td><?php echo htmlentities($result->email); ?></td>
                                <td><?php echo  preg_replace("|.|", "*", htmlentities($result->password)); ?></td>
                                <td><?php echo htmlentities($result->phone); ?></td>
                                <td><a href="studentController.php?show=<?php echo htmlentities($result->id); ?>">
                                        <button class="btn btn-primary btn-xs">
                                            <span class="fas fa-eye"></span>
                                        </button>
                                    </a>
                                </td>
                                <td>
                                    <a href="studentController.php?update=<?php echo htmlentities($result->id); ?>">
                                        <button class="btn btn-success btn-xs">
                                            <span class="fas fa-pencil-alt"></span>
                                        </button>
                                    </a>
                                </td>
                                <td>
                                    <a href="studentController.php?del=<?php echo htmlentities($result->id); ?>">
                                        <button class="btn btn-danger btn-xs"
                                            onClick="return confirm('Do you really want to delete');">
                                            <span class="fas fa-trash-alt"></span>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                            <?php
                                    // for serial number increment
                                    // $cnt++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>