<?php
include_once "session.php";

// Initialize an empty variable to store messages
$updateMessage = '';

if (isset($_POST['update_password'])) {
    try {
        $user_id = $user;
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if new password and confirm password match
        if ($new_password !== $confirm_password) {
            $updateMessage = "Password do not match! Please try again.";
            throw new Exception($updateMessage);
        }

        // Authenticate the old password
        $stmt_authenticate = $conn->prepare("SELECT password FROM users WHERE user_id = :user_id");
        $stmt_authenticate->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt_authenticate->execute();
        $stored_password = $stmt_authenticate->fetchColumn();

        if (!$stored_password) {
            $updateMessage = "User not found.";
            throw new Exception($updateMessage);
        }

        // Verify old password
        if ($old_password !== $stored_password) {
            $updateMessage = "Old password is incorrect! Please try again.";
            throw new Exception($updateMessage);
        }

        // If authentication is successful, update the password
        $stmt_update_password = $conn->prepare("UPDATE users SET password = :new_password WHERE user_id = :user_id");
        $stmt_update_password->bindParam(':new_password', $new_password, PDO::PARAM_STR);
        $stmt_update_password->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt_update_password->execute();

        $updateMessage = "Password update success! Please login again. Logging out...";
				header("refresh:5;url=logout.php");
    } catch (Exception $e) {
        $updateMessage = "Error: " . $e->getMessage();
    }
}
?>

<!-- script separator -->

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<title>UIS Appointment System: Update Password</title>
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
			color: #9898e0;
		}

		.btn.btn-lg {
			background-color: rgb(112, 112, 234);
			color: white;
		}

		.btn.btn-lg:hover {
			background-color: rgb(84, 83, 203);
			color: white;
		}

		label {
			color: rgba(50, 49, 151, 255);
		}
	</style>
</head>

<body>

	<!-- <?php include "navbar.php" ?> -->

	<div class="container col-xl-10 col-xxl-8 px-4 py-5">
		<div class="row align-items-center g-lg-5 py-5">
			<div class="col-md-10 mx-auto col-lg-5 py-5">
				<form method="post" action="password.php" class="p-4 p-md-5 border rounded-3 bg-body-tertiary">
					
				<!-- Input for Old Password -->
					<div class="form-floating mb-3">
						<input type="password" class="form-control" id="floatingOldPassword" name="old_password" placeholder="Old Password" required>
						<label for="floatingOldPassword">Old Password</label>
					</div>

					<!-- Input for New Password -->
					<div class="form-floating mb-3">
						<input type="password" class="form-control" id="floatingNewPassword" name="new_password" placeholder="New Password" required>
						<label for="floatingNewPassword">New Password</label>
					</div>

					<!-- Input to Re-type New Password -->
					<div class="form-floating mb-3">
						<input type="password" class="form-control" id="floatingConfirmPassword" name="confirm_password" placeholder="Re-type New Password" required>
						<label for="floatingConfirmPassword">Re-type New Password</label>
					</div>

					<!-- Button to Trigger Password Update -->
					<button class="w-100 btn btn-lg" type="submit" name="update_password">Update Password</button>

					<hr class="my-4">
					<p class="sign" style="color: <?php echo ($updateMessage === "Password update success! Please login again. Logging out...") ? 'green' : 'red'; ?>">
    <?php echo $updateMessage; ?>
</p>

				</form>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>