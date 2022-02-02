<?php
// include database connection file
require_once("db.php");
///////// Connection ///////

$connection = new DB();
$conn = $connection->getConnection();

session_start();

//======================================= Student Login
if (isset($_POST["login"])) {
    try {
        if (empty($_POST["email"]) || empty($_POST["password"])) {
            $_SESSION['errMessage'] = $connection->errMessage("All fields are required");
            header("Location:login.php");
        } else {
            $_SESSION['errMessage'] = '';
            $query = "SELECT * FROM students WHERE email = :email AND password = :password";

            $statement = $conn->prepare($query);

            $statement->execute(
                array(
                    'email'        =>     $_POST["email"],
                    'password'     =>     $_POST["password"]
                )
            );

            $count = $statement->rowCount();
            if ($count > 0) {
                $_SESSION['errMessage'] = '';
                $_SESSION["email"] = $_POST["email"];
                $rowData = $statement->fetch();
                $_SESSION["Name"] = $rowData["fname"] . " " . $rowData["lname"];
                header("location:list.php");
            } else {
                $_SESSION["email"] = '';
                $_SESSION["Name"] = '';
                $_SESSION['errMessage'] = $connection->errMessage("Email or Password Invalid!");
                header("Location:login.php");
            }
        }
    } catch (PDOException $error) {
        echo "Errrrrror " . $connection->errMessage($error->getMessage());
    }
}
//======================================== Add Student
else if (isset($_POST['insert'])) {
    $error_array = [];
    $data = [];
    // Posted Values
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $phone = $_POST['phone'];
    // Patterns
    $email_pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";
    $mobileno = strlen($_POST["phone"]);
    $passlen = strlen($_POST["pass"]);
    // Start Validation
    if (
        empty($fname) && empty($lname) &&
        empty($email) && empty($pass) &&
        empty($phone)
    ) {
        $error_array["allEmpty"] = 'All fields are required';
    }

    // fname validation
    if (!preg_match("/^[a-zA-z]*$/", $fname)) {
        $error_array["fname"] = "Only alphabets and whitespace are allowed.";
    } else if (empty($fname)) {
        $error_array["fname"] = "This Field is Required..";
    } else {
        $data["fname"] = $fname;
    }
    // lname validation
    if (!preg_match("/^[a-zA-z]*$/", $lname)) {
        $error_array["lname"] = "Only alphabets and whitespace are allowed.";
    } else if (empty($lname)) {
        $error_array["lname"] = "This Field is Required..";
    } else {
        $data["lname"] = $lname;
    }
    // phone validation
    if (empty($phone)) {
        $error_array["phone"] = "This Field is Required..";
        $error_array["pholen"] = "";
    } else if (!preg_match("/^[0-9]*$/", $phone)) {
        $error_array["phone"] = "Only numeric value is allowed.";
    } else if ($mobileno !== 10) {
        $error_array["pholen"] = "Mobile must have 10 digits.";
    } else {
        $data["phone"] = $phone;
    }
    // password validation
    if (empty($pass)) {
        $error_array["passlen"] = "This Field is Required..";
    } else if ($passlen < 8 || $passlen > 15) {
        $error_array["passlen"] = "Min password is 8 characters & Max is 15";
    } else {
        $data["pass"] = $pass;
    }
    // email validation
    if (empty($email)) {
        $error_array["email"] = "This Field is Required..";
    } else if (!preg_match($email_pattern, $email)) {
        $error_array["email"] = "Email is not valid.";
    } else {
        $data["email"] = $email;
    }
    // Check if there're errors
    if (sizeof($data) > 0) {
        $_SESSION["data"] = $data;
    }
    if (sizeof($error_array) > 0) {
        $_SESSION["error_array"] = $error_array;
        header("Location:addStudent.php");
    } else {
        try {
            $_SESSION["dupl_error"] = '';

            $query = $connection->insertRow("students", "fname,lname,email,password,phone", ":fn,:ln,:eml,:pas,:pho");
            // Bind the parameters
            $query->bindParam(':fn', $fname, PDO::PARAM_STR);
            $query->bindParam(':ln', $lname, PDO::PARAM_STR);
            $query->bindParam(':eml', $email, PDO::PARAM_STR);
            $query->bindParam(':pas', $pass, PDO::PARAM_STR);
            $query->bindParam(':pho', $phone, PDO::PARAM_STR);
            // Query Execution
            $query->execute();

            // Check that the insertion really worked. If the last inserted id is greater than zero, the insertion worked.
            $lastInsertId = $conn->lastInsertId();
            if ($lastInsertId) {
                // Message for successfull insertion
                header("Location: list.php");
            } else {
                // Message for unsuccessfull insertion
                //echo "<script>alert('Something went wrong. Please try again');</script>";
                header("Location: addStudent.php");
            }
            $_SESSION["error_array"] = '';
            $_SESSION["data"] = '';
        } catch (PDOException $e) {
            // Check for duplicates
            if ($e->errorInfo[1] == 1062) {
                foreach ($e->errorInfo as $item) {
                    if (str_contains($item, 'email')) {
                        $dupl_error = 'Email already exists.';
                    }
                    if (str_contains($item, 'password')) {
                        $dupl_error = 'Password already exists.';
                    }
                }
                if (sizeof($error_array) > 0) {
                    $_SESSION["error_array"] = $error_array;
                } else {
                    $_SESSION["error_array"] = '';
                }
                if (sizeof($data) > 0) {
                    $_SESSION["data"] = $data;
                } else {
                    $_SESSION["data"] = '';
                }
                $_SESSION["dupl_error"] = $dupl_error;
                header("Location:addStudent.php");
            }
        }
    }
}
//============================================= Show Student
else if (isset($_GET['show'])) {

    $query = $connection->showRow('students', "where id = {$_GET['show']}");
    //Execute the query:
    $query->execute();

    //Assign the data which you pulled from the database (in the preceding step) to a variable.
    $result = $query->fetch(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {

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

    <title>My Info</title>
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
                <li class="nav-item">
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
    <!-- Students Data -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <br>
                <div class="table-responsive">
                    <table id="mytable" class="table table-bordred table-striped">
                        <thead>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Phone</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </thead>
                        <tbody>

                            <!-- Display Student Data -->
                            <tr>
                                <td><?php echo htmlentities($result->id); ?></td>
                                <td><?php echo htmlentities($result->fname); ?></td>
                                <td><?php echo htmlentities($result->lname); ?></td>
                                <td><?php echo htmlentities($result->email); ?></td>
                                <td><?php echo htmlentities($result->password); ?></td>
                                <td><?php echo htmlentities($result->phone); ?></td>
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
                            } else {
                                echo '<td colspan="8" style="text-align: center;
                                    font-size:22px; font-weight:bold; padding:20px">Student Not Found</td>';
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
<!--  //========================================= Update Student -->
<?php
    }
    //====== Get  data in  HTML Form 

    else if (isset($_GET['update'])) {
        $_SESSION['stuId'] = $_GET['update'];
        $query = $connection->getAllRows("students", '*', "where id={$_GET['update']}");

        $query->execute();
        //Assign the data which you pulled from the database (in the preceding step) to a variable.
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
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

    <title>Edit Info</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="list.php">All Students</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="addStudent.php">Add Student</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
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
    <br>
    <?php
                if (isset($_SESSION["error_array"])) {
                    $errors = $_SESSION["error_array"];
                }
                if (isset($_SESSION["dupl_error"])) {
                    $duplErr = $_SESSION["dupl_error"];
                }
                ?>
    <div class="container">
        <br>
        <h2>Update Your Data</h2>
        <br>
        <hr>
        <small class="text-danger">
            <?php
                        if (isset($errors['allEmpty'])) {
                            echo $errors['allEmpty'];
                        }
                        if (isset($duplErr)) {
                            echo $duplErr;
                        }
                        if (isset($_GET['update'])) {
                            $stuID = $_GET['update'];
                        }

                        ?>
        </small>
        <form method="post" action="studentController.php">
            <div class="row">
                <div class="col-md-4"><b>First Name</b>
                    <input type="text" name="fname" value="<?php echo htmlentities($result->fname); ?>"
                        class="form-control">
                    <small class="text-danger">
                        <?php
                                    if (isset($errors['fname'])) {
                                        echo $errors['fname'];
                                    }
                                    ?>
                    </small>
                </div>
                <div class="col-md-4"><b>Last Name</b>
                    <input type="text" name="lname" value="<?php echo htmlentities($result->lname); ?>"
                        class="form-control">
                    <small class="text-danger">
                        <?php
                                    if (isset($errors['lname'])) {
                                        echo $errors['lname'];
                                    }
                                    ?>
                    </small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4"><b>Email</b>
                    <input type="text" name="email" value="<?php echo htmlentities($result->email); ?>"
                        class="form-control">
                    <small class="text-danger">
                        <?php
                                    if (isset($errors['email'])) {
                                        echo $errors['email'];
                                    }
                                    ?>
                    </small>
                </div>
                <div class="col-md-4"><b>Password</b>
                    <input type="password" name="pass" value="<?php echo htmlentities($result->password); ?>"
                        class="form-control">
                    <small class="text-danger">
                        <?php
                                    if (isset($errors['passlen'])) {
                                        echo $errors['passlen'];
                                    }
                                    ?>
                    </small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8"><b>Phone</b>
                    <input type="text" name="phone" class="form-control"
                        value="<?php echo htmlentities($result->phone); ?>"></input>
                    <small class="text-danger">
                        <?php
                                    if (isset($errors['pholen'])) {
                                        echo $errors['pholen'];
                                    }
                                    ?>
                    </small>
                    <small class="text-danger">
                        <?php
                                    if (isset($errors['phone'])) {
                                        echo $errors['phone'];
                                    }
                                    ?>
                    </small>
                </div>
            </div>

            <div class="row" style="margin-top:1%">
                <div class="col-md-8">
                    <input class="btn btn-primary" type="submit" name="updateBtn" value="Update">
                </div>
            </div>
        </form>
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

<?php

        }
    } else if (isset($_POST['updateBtn'])) {
        $error_array = [];
        // Posted Values
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $phone = $_POST['phone'];
        // Patterns
        $email_pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";
        $mobileno = strlen($_POST["phone"]);
        $passlen = strlen($_POST["pass"]);
        // Start Validation
        if (
            empty($fname) && empty($lname) &&
            empty($email) && empty($pass) &&
            empty($phone)
        ) {
            $error_array["allEmpty"] = 'All fields are required';
        }

        // fname validation
        if (!preg_match("/^[a-zA-z]*$/", $fname)) {
            $error_array["fname"] = "Only alphabets and whitespace are allowed.";
        } else if (empty($fname)) {
            $error_array["fname"] = "This Field is Required..";
        }
        // lname validation
        if (!preg_match("/^[a-zA-z]*$/", $lname)) {
            $error_array["lname"] = "Only alphabets and whitespace are allowed.";
        } else if (empty($lname)) {
            $error_array["lname"] = "This Field is Required..";
        }
        // phone validation
        if (empty($phone)) {
            $error_array["phone"] = "This Field is Required..";
            $error_array["pholen"] = "";
        } else if (!preg_match("/^[0-9]*$/", $phone)) {
            $error_array["phone"] = "Only numeric value is allowed.";
        } else if ($mobileno !== 10) {
            $error_array["pholen"] = "Mobile must have 10 digits.";
        }
        // password validation
        if (empty($pass)) {
            $error_array["passlen"] = "This Field is Required..";
        } else if ($passlen < 8 || $passlen > 15) {
            $error_array["passlen"] = "Min password is 8 characters & Max is 15";
        }
        // email validation
        if (empty($email)) {
            $error_array["email"] = "This Field is Required..";
        } else if (!preg_match($email_pattern, $email)) {
            $error_array["email"] = "Email is not valid.";
        }
        // Check if there're errors
        if (sizeof($error_array) > 0) {
            $_SESSION["error_array"] = $error_array;
            header("Location:studentController.php?update={$_SESSION['stuId']}");
        } else {
            try {
                echo "Session Third <br>";
                var_dump($_SESSION);
                $_SESSION["dupl_error"] = '';
                // Query for Updation

                $query = $connection->updateRow("students", "fname=:fn,lname=:ln,email=:eml,password=:pass,phone=:pho", "where id={$_SESSION['stuId']}");

                // Bind the parameters
                $query->bindParam(':fn', $fname, PDO::PARAM_STR);
                $query->bindParam(':ln', $lname, PDO::PARAM_STR);
                $query->bindParam(':eml', $email, PDO::PARAM_STR);
                $query->bindParam(':pass', $pass, PDO::PARAM_STR);
                $query->bindParam(':pho', $phone, PDO::PARAM_STR);
                // Query Execution
                $query->execute();

                // Code for redirection
                header("Location: list.php");
                $_SESSION["error_array"] = '';
            } catch (PDOException $e) {

                echo "Session Fourth <br>";
                var_dump($_SESSION);
                // Check for duplicates
                if ($e->errorInfo[1] == 1062) {
                    foreach ($e->errorInfo as $item) {
                        if (str_contains($item, 'email')) {
                            $dupl_error = 'Email already exists.';
                        } else if (str_contains($item, 'password')) {
                            $dupl_error = 'Password already exists.';
                        }
                    }
                    if (sizeof($error_array) > 0) {
                        $_SESSION["error_array"] = $error_array;
                    } else {
                        $_SESSION["error_array"] = '';
                    }
                    $_SESSION["dupl_error"] = $dupl_error;
                    header("Location:studentController.php?update={$_SESSION['stuId']}");
                }
            }
        }
    }
    //========================================== Delete Student
    else if (isset($_GET['del'])) {
        //Get row id
        // $uid = intval($_GET['del']);

        $query = $connection->deleteRow("students", "WHERE  id={$_GET['del']}");
        // Query Execution
        $query->execute();
        // Code for redirection
        if ($count <= 0) {
            session_destroy();
            header("location: login.php");
        } else {
            header("Location: list.php");
        }
    }

    //============================================ Student Logout
    else if (isset($_GET['logout'])) {

        session_destroy();
        header("location: login.php");
    }
    ?>