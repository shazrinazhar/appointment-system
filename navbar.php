<?php
include_once "session.php";

if($user==''){
  header("location:login.php");
} else {
  header("");
}
?>

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">UIS Appointment System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
      <a class="nav-item btn btn-outline-danger" href="logout.php">Log out</a>
    </div>
  </div>
</nav>