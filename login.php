<?php
session_start();
include_once "database.php";

try {
  if (isset($_POST['login'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
      $message = "<label>All fields are required</label>";
    } else {
      $query = "SELECT * FROM users WHERE email = :email AND password = :password";
      $stmt = $conn->prepare($query);
      $stmt->execute(
        array(
          "email" => $_POST['email'],
          "password" => $_POST['password']
        )
      );
      $count = $stmt->rowCount();

      if ($count == 0) {
        $message = "<label>Invalid email or password! Please try again.</label>";
      } elseif ($count > 0) {
        $user = $stmt->fetch();

        $_SESSION['user'] = $user['user_id'];
        $role = $user['role'];

        if ($role === 'student') {
          header('Location: index_student.php');
          exit();
        } elseif ($role === 'lecturer') {
          header('Location: index_lecturer.php');
          exit();
        }
      }
    }
  }
} catch (PDOException $error) {
  $message = $error->getMessage();
}
?>

<!-- script separator -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>UIS Appointment System: Login</title>
  <link rel="icon" type="image/png" href="uis.png">
  <style>
    body {
      background-color: rgb(231, 232, 250);
    }

    h1 {
      color: rgba(50, 49, 151, 255);
    }

    p {
      color: rgba(50, 49, 151, 255);
    }

    form {
      box-shadow: 5px 5px 50px rgba(50, 49, 151, 0.5);
      backdrop-filter: blur(25px);
    }

    .form-floating>.form-control-plaintext~label,
    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label,
    .form-floating>.form-select~label {
      color: #aaa9e5;
    }

    .form-control {
      color: rgba(50, 49, 151, 255);
      border-color: #aaa9e5;
    }

    .sign {
      text-align: center;
    }

    .btn {
      background-color: rgb(112, 112, 234);
      color: white;
    }

    .btn:hover {
      background-color: rgb(84, 83, 203);
      color: white;
    }

    label[for="floatingInput"] {
      color: rgba(50, 49, 151, 255);
    }

    label[for="floatingPassword"] {
      color: rgba(50, 49, 151, 255);
    }
  </style>
</head>

<body>
  <div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row align-items-center g-lg-5 py-5">
      <div class="col-lg-7 text-center text-lg-start">
        <h1 class="display-4 fw-bold lh-1 mb-3">UIS Appointment System</h1>
        <p class="col-lg-10 fs-4">Ready, Set, Book: Streamlined Student-Lecturer Appointments for You!</p>
      </div>
      <div class="col-md-10 mx-auto col-lg-5">
        <form method="post" action="login.php" class="p-4 p-md-5 border rounded-3 bg-body-tertiary">
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="floatingInput" name="email" placeholder="User Email">
            <label for="floatingInput">Email address</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
            <label for="floatingPassword">Password</label>
          </div>
          <button class="w-100 btn btn-lg" type="submit" name="login">Sign in</button>
          <hr class="my-4">
          <p class="sign" style="color: <?php echo ($message === "Sign in to get started.") ? 'red' : 'red'; ?>">
            <?php
            echo isset($message) ? $message : "Sign in to get started.";
            ?>
          </p>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>