<?php
include_once "session.php";

if ($user == '') {
  header("location:login.php");
} else {
  header("");
}

if ($role === 'student') {
  $href = 'index_student.php';
} elseif ($role === 'lecturer') {
  $href = 'index_lecturer.php';
}
?>

<style>
  .navbar {
    background-color: #19193d;
  }
</style>

<nav class="navbar navbar-expand-md navbar-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand">
      <img src="uis.png" alt="" style="width: 32px; height: 32px; margin-right: 5px; margin-bottom: 3px">
      <span style="font-size: 20px; font-weight: 500;">UIS Appointment System</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
      <ul class="navbar-nav">
        <li class="nav-item">
        <a class="nav-link" href="<?php echo $href; ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="password.php">Change Password</a>
        </li>
      </ul>
      <a class="nav-item btn btn-outline-danger" href="logout.php">Log out</a>
    </div>
  </div>
</nav>