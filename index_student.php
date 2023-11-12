<?php
include_once "session.php";

if ($user == '') {
  header("location:login.php");
  exit();
}

$query = "SELECT appointments.appointment_id, appointments.datetime, appointments.status, appointments.desc, users.full_name AS lecturer_name
          FROM appointments
          INNER JOIN users ON appointments.lecturer_id = users.user_id
          WHERE appointments.student_id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $user);
$stmt->execute();

// Fetch the results
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Student Dashboard</title>
</head>

<body>

  <?php include "navbar.php" ?>

  <div class="col-lg-8 mx-auto p-4 py-md-5">

    <header class="d-flex align-items-center pt-5 pb-3 mb-5 border-bottom">
      <h1>
        <span style="font-weight: bolder;">
          Hi student! 👋🏼 <br>
        </span>
        <span>
          <?php echo $full_name ?>
        </span>
      </h1>
    </header>

    <main>

      <section class="pb-2 mb-4">
        <h2 class="text-body-emphasis">Appointment List</h2>
        <table class="table table-responsive table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Lecturer Name</th>
              <th scope="col">Date & Time</th>
              <th scope="col">Description</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($appointments as $index => $appointment) { ?>
              <tr data-appointment-id="<?= $appointment['appointment_id'] ?>">
                <th scope="row"><?= $index + 1 ?></th>
                <td><?= $appointment['lecturer_name'] ?></td>
                <td><?= date('d/m/Y, H:i', strtotime($appointment['datetime'])) ?></td>
                <td><?= $appointment['desc'] ?></td>
                <td class="status-td"><?= $appointment['status'] ?></td>
                <td>
                  <a href="" class="btn btn-outline-secondary btn-sm" role="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                      <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                    </svg>
                  </a>
                  <a href="" class="btn btn-danger btn-sm" role="button">
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
      </section>

      <section class="pb-4">
        <h2 class="text-body-emphasis">Create new appointment or edit existing appointments</h2>
        <form action="" method="post" class="row justify-content-start" id="appointment-form">
          <div class="col-md-6">
            <div class="form-floating mb-2 mt-4">
              <!-- Use a dropdown to select a lecturer's name -->
              <select name="lecturer" class="form-select" id="lecturer" required>
                <option value="" disabled selected>Select a lecturer</option>
                <?php foreach ($lecturers as $lecturer) { ?>
                  <option value="<?= $lecturer['lecturer_id'] ?>"><?= $lecturer['lecturer_name'] ?></option>
                <?php } ?>
              </select>
              <label for="lecturer">Lecturer name</label>
            </div>
            <div class="form-floating mb-2">
              <!-- Use a datetime picker for date and time -->
              <input name="time" type="datetime-local" class="form-control" id="time" required>
              <label for="time">Date and time</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating mb-2 mt-4">
              <input name="desc" type="text" class="form-control" id="desc" placeholder="Enter appointment description" required>
              <label for="desc">Description</label>
            </div>
            <div class="d-flex justify-content-end">
              <!-- Show "Update" or "Create" button based on the form content -->
              <button type="reset" class="btn btn-outline-danger btn-lg me-2" id="resetButton">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                  <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                </svg>
              </button>
              <button class="btn btn-success btn-lg" type="submit" name="update" id="updateButton">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                  <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                </svg>
              </button>
              <button class="btn btn-primary btn-lg" type="submit" name="create" id="createButton">
                Create
              </button>
            </div>
          </div>
        </form>
      </section>

    </main>

    <footer class="pt-5 text-body-secondary border-top text-center">
      Created by Shazrina for FYP &middot; &copy; 2023
    </footer>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.status-td').each(function() {
        const status = $(this).text().trim();
        switch (status) {
          case 'Pending':
            $(this).css('color', 'orange');
            break;
          case 'Approve':
            $(this).css('color', 'green');
            break;
          case 'Decline':
            $(this).css('color', 'red');
            break;
            // You can add more cases for other statuses if needed
        }
      });
    });
  </script>
  <script>
    $(document).ready(function() {
      const appointmentForm = $('#appointment-form');
      const updateButton = $('#updateButton');
      const createButton = $('#createButton');

      // Check if form is empty to determine which button to display
      function toggleButtons() {
        if (appointmentForm[0].checkValidity()) {
          updateButton.show();
          createButton.hide();
        } else {
          updateButton.hide();
          createButton.show();
        }
      }

      // Initial button state
      toggleButtons();

      // Handle form changes
      appointmentForm.on('input', toggleButtons);
      appointmentForm.on('reset', toggleButtons);
    });
  </script>

</body>

</html>