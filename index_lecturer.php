<?php
include_once "session.php";

if ($user == '') {
  header("location:login.php");
  exit();
}

$query = "SELECT appointments.appointment_id, appointments.datetime, appointments.status, appointments.desc, users.full_name AS student_name
          FROM appointments
          INNER JOIN users ON appointments.student_id = users.user_id
          WHERE appointments.lecturer_id = :lecturer_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':lecturer_id', $user);
$stmt->execute();

// Fetch the results
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['appointmentId']) && isset($_POST['status'])) {

  $appointmentId = $_POST['appointmentId'];
  $status = $_POST['status'];

  // Perform the update in the database
  $query = "UPDATE appointments SET status = :status WHERE appointment_id = :appointmentId";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(':status', $status);
  $stmt->bindParam(':appointmentId', $appointmentId);
  $stmt->execute();

  // Return a response if needed
  echo 'Status updated successfully';
} else if (isset($_POST['deleteAppointment']) && isset($_POST['appointmentId'])) {

  $appointmentId = $_POST['appointmentId'];

  // Perform the deletion in the database
  $query = "DELETE FROM appointments WHERE appointment_id = :appointmentId";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(':appointmentId', $appointmentId);
  $stmt->execute();

  // Return a response if needed
  echo 'Appointment deleted successfully';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Lecturer Dashboard</title>
</head>

<body>

  <?php include "navbar.php" ?>

  <div class="col-lg-8 mx-auto p-4 py-md-5">

    <header class="d-flex align-items-center pt-5 pb-3 mb-5 border-bottom">
      <h1>
        <span style="font-weight: bolder;">
          Hi lecturer! 👋🏼 <br>
        </span>
        <span>
          <?php echo $full_name ?>
        </span>
      </h1>
    </header>

    <main class="pb-5">

      <h2 class="text-body-emphasis">Appointment List</h2>
      <table class="table table-responsive table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Student Name</th>
            <th scope="col">Time & Time</th>
            <th scope="col">Description</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($appointments as $index => $appointment) { ?>
            <tr data-appointment-id="<?= $appointment['appointment_id'] ?>">
              <th scope="row"><?= $index + 1 ?></th>
              <td><?= $appointment['student_name'] ?></td>
              <td><?= date('d/m/Y, H:i', strtotime($appointment['datetime'])) ?></td>
              <td><?= $appointment['desc'] ?></td>
              <td>
                <div class="dropdown">
                  <button class="btn dropdown-toggle btn-sm status-button" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?= $appointment['status'] ?>
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                    <li><a class="dropdown-item" href="#" data-status="Pending">Pending</a></li>
                    <li><a class="dropdown-item" href="#" data-status="Approve">Approve</a></li>
                    <li><a class="dropdown-item" href="#" data-status="Decline">Decline</a></li>
                  </ul>
                </div>
              </td>
              <td>
                <a href="" class="btn btn-danger btn-sm delete-button" role="button" data-appointment-id="<?= $appointment['appointment_id'] ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                  </svg>
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

    </main>

    <footer class="pt-5 text-body-secondary border-top text-center">
      Created by Shazrina for FYP &middot; &copy; 2023
    </footer>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const dropdownButtons = document.querySelectorAll(".status-button");

      dropdownButtons.forEach(function(button) {
        const initialStatus = button.textContent.trim();

        switch (initialStatus) {
          case "Pending":
            button.classList.remove("btn-success", "btn-danger");
            button.classList.add("btn-warning");
            break;
          case "Approve":
            button.classList.remove("btn-warning", "btn-danger");
            button.classList.add("btn-success");
            break;
          case "Decline":
            button.classList.remove("btn-warning", "btn-success");
            button.classList.add("btn-danger");
            break;
        }

        const dropdownItems = button.nextElementSibling.querySelectorAll(".dropdown-item");

        dropdownItems.forEach(function(item) {
          item.addEventListener("click", function() {
            const selectedStatus = this.getAttribute("data-status");

            button.textContent = selectedStatus;

            switch (selectedStatus) {
              case "Pending":
                button.classList.remove("btn-success", "btn-danger");
                button.classList.add("btn-warning");
                break;
              case "Approve":
                button.classList.remove("btn-warning", "btn-danger");
                button.classList.add("btn-success");
                break;
              case "Decline":
                button.classList.remove("btn-warning", "btn-success");
                button.classList.add("btn-danger");
                break;
            }
          });
        });
      });
    });
  </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {

      // Update status when a dropdown option is selected
      $('.dropdown-item').on('click', function() {
        const status = $(this).data('status');
        const appointmentId = $(this).closest('tr').data('appointment-id');

        // Update the table row immediately
        $(this).closest('tr').find('.status-button').text(status);

        // Send an AJAX request to update the status
        $.post('index_lecturer.php', {
          appointmentId: appointmentId,
          status: status
        }, function(response) {
          // Handle the response if needed
        });
      });

      // Delete appointment when the delete button is clicked
      $('.delete-button').on('click', function() {
        const appointmentId = $(this).closest('tr').data('appointment-id');

        // Remove the table row immediately
        $(this).closest('tr').remove();

        // Send an AJAX request to delete the appointment
        $.post('index_lecturer.php', {
          appointmentId: appointmentId,
          deleteAppointment: 1
        }, function(response) {
          // Handle the response if needed
        });
      });
    });
  </script>
</body>

</html>