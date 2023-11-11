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

			if ($count > 0) {
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
	<title>Document</title>
</head>

<body>
	<div class="container col-xl-10 col-xxl-8 px-4 py-5">
		<div class="row align-items-center g-lg-5 py-5">
			<div class="col-lg-7 text-center text-lg-start">
				<h1 class="display-4 fw-bold lh-1 text-body-emphasis mb-3">UIS Appointment System</h1>
				<p class="col-lg-10 fs-4">UIS student-lecturer appointment system presented as Final Year Project.</p>
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
					<button class="w-100 btn btn-lg btn-dark" type="submit" name="login">Sign in</button>
					<hr class="my-4">
					<small class="text-body-secondary">Sign in to get started.</small>
				</form>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>