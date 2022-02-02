<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Add Student </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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

                <li class="nav-item active">
                    <a class="nav-link" href="addStudent.php">Add Student</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
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
    if (isset($_SESSION["data"])) {
        $data = $_SESSION["data"];
    }
    if (isset($_SESSION["dupl_error"])) {
        $duplErr = $_SESSION["dupl_error"];
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <br>
                <h3>Add New Student</h3>
                <hr />
            </div>
        </div>
        <small class="text-danger">
            <?php
            if (isset($errors['allEmpty'])) {
                echo $errors['allEmpty'];
            }
            if (isset($duplErr)) {
                echo $duplErr;
            }
            ?>
        </small>
        <form method="post" action="studentController.php">
            <div class="row">
                <div class="col-md-4 mb-5"><b>First Name</b>
                    <input type="text" name="fname" class="form-control" value="<?php
                                                                                if (isset($data['fname'])) {
                                                                                    echo $data['fname'];
                                                                                }
                                                                                ?>">
                    <small class="text-danger">
                        <?php
                        if (isset($errors['fname'])) {
                            echo $errors['fname'];
                        }
                        ?>
                    </small>
                </div>

                <div class="col-md-4"><b>Last Name</b>
                    <input type="text" name="lname" class="form-control" value="<?php
                                                                                if (isset($data['lname'])) {
                                                                                    echo $data['lname'];
                                                                                }
                                                                                ?>">
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
                <div class="col-md-4 mb-5"><b>Email</b>
                    <input type="text" name="email" class="form-control" value="<?php
                                                                                if (isset($data['email'])) {
                                                                                    echo $data['email'];
                                                                                }
                                                                                ?>">
                    <small class="text-danger">
                        <?php
                        if (isset($errors['email'])) {
                            echo $errors['email'];
                        }
                        ?>
                    </small>
                </div>
                <div class="col-md-4"><b>Password</b>
                    <input type="password" name="pass" class="form-control" value="<?php
                                                                                    if (isset($data['pass'])) {
                                                                                        echo $data['pass'];
                                                                                    }
                                                                                    ?>">
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
                <div class="col-md-4"><b>Phone</b>
                    <input type="text" name="phone" class="form-control" value="<?php
                                                                                if (isset($data['phone'])) {
                                                                                    echo $data['phone'];
                                                                                }
                                                                                ?>">
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
                    <input class="btn btn-primary" type="submit" name="insert" value="Add">
                </div>
            </div>
        </form>
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