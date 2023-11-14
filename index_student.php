<?php
include_once "session.php";

if ($user == '') {
  header("location:login.php");
  exit();
}

// List appointment
$query = "SELECT appointments.appointment_id, appointments.datetime, appointments.status, appointments.desc, users.full_name AS lecturer_name
          FROM appointments
          INNER JOIN users ON appointments.lecturer_id = users.user_id
          WHERE appointments.student_id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $user);
$stmt->execute();

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Edit button
if (isset($_GET['edit'])) {
  try {
    $query_edit = "SELECT appointments.appointment_id, appointments.datetime, appointments.status, appointments.desc, users.full_name AS lecturer_name
                   FROM appointments
                   INNER JOIN users ON appointments.lecturer_id = users.user_id
                   WHERE appointments.appointment_id = :appointment_id";
    $stmt_edit = $conn->prepare($query_edit);
    $stmt_edit->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
    $appointment_id = $_GET['edit'];
    $stmt_edit->execute();
    $editresult = $stmt_edit->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Update
if (isset($_POST['update'])) {
  try {
    // Convert the datetime format from HTML input format to the database format
    $datetime = date('Y-m-d H:i:s', strtotime($_POST['time']));
    $desc = $_POST['desc'];
    $appointment_id = $_POST['appointment_id'];
    $stmt_update = $conn->prepare("UPDATE appointments
                                   SET `datetime` = :d_time, `desc` = :descr
                                   WHERE appointment_id = :appointment_id");
    $stmt_update->bindParam(':d_time', $datetime, PDO::PARAM_STR);
    $stmt_update->bindParam(':descr', $desc, PDO::PARAM_STR);
    $stmt_update->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
    $stmt_update->execute();
    header("location:index_student.php");
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Create
if (isset($_POST['create'])) {
  try {
    $insert_stmt = $conn->prepare("INSERT INTO appointments (student_id, lecturer_id, `datetime`, `desc`, `status`)
                                   VALUES (:student_id, :lect, :d_time, :descr, 'Pending')");
    $insert_stmt->bindParam(':student_id', $user, PDO::PARAM_STR);
    $insert_stmt->bindParam(':lect', $lecturer_full_name, PDO::PARAM_STR);
    $insert_stmt->bindParam(':d_time', $datetime, PDO::PARAM_STR);
    $insert_stmt->bindParam(':descr', $desc, PDO::PARAM_STR);

    // Convert the datetime format from HTML input format to the database format
    $datetime = date('Y-m-d H:i:s', strtotime($_POST['time']));
    $desc = $_POST['desc'];
    $lecturer_full_name = $_POST['lecturer'];


    $insert_stmt->execute();
    header("location:index_student.php");
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Delete
if (isset($_GET['delete'])) {
  try {
    $appointment_id = $_GET['delete'];
    $stmt_delete = $conn->prepare("DELETE FROM appointments WHERE appointment_id = :appointment_id");
    $stmt_delete->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
    $stmt_delete->execute();
    header("location: index_student.php");
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>UIS Appointment System: Student Dashboard</title>
  <link rel="icon" type="image/png" href="uis.png">
  <style>
    body {
      background-color: rgb(231, 232, 250);
    }

    header {
      border-bottom: 1px solid #aaa9e5;
    }

    h1 {
      color: rgba(50, 49, 151, 255);
    }

    h2 {
      font-size: 2em;
      color: rgba(50, 49, 151, 255);
    }

    table {
      text-align: center;
      color: rgba(50, 49, 151, 255);
      justify-content: center;
      box-shadow: 5px 5px 30px rgba(50, 49, 151, 0.2);
    }

    .table>tbody {
      vertical-align: middle;
    }

    th {
      background-color: white !important;
      color: rgba(50, 49, 151, 255) !important;
    }

    td {
      background-color: white !important;
    }

    td.color {
      color: rgba(50, 49, 151, 255) !important;
    }

    .status-td {
      font-weight: 500;
    }

    .form-select, .form-control {
      color: rgba(50, 49, 151, 255);
    }

    .form-floating>label {
      color: #aaa9e5;
    }

    .form-floating>.form-control-plaintext~label, .form-floating>.form-control:focus~label, .form-floating>.form-control:not(:placeholder-shown)~label, .form-floating>.form-select~label {
      color: #aaa9e5;
    }

    footer {
      border-top: 1px solid #aaa9e5;
      color: #7675d5;
    }
  </style>
</head>

<body>

  <?php include "navbar.php" ?>

  <div class="col-lg-8 mx-auto p-4 py-md-5">

    <header class="d-flex align-items-center pt-5 pb-3 mb-5">
      <h1 style="font-weight: bolder;">Welcome back, <?php echo $full_name ?> 👋🏼</h1>
    </header>

    <main>

      <section class="pb-2 mb-4">
        <h2 class="mb-4 text-center">Scheduled Appointment List</h2>
        <table class="table table-responsive table-hover">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col">Lecturer Name</th>
              <th scope="col">Date & Time</th>
              <th scope="col">Description</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody class="table-group-divider">
            <?php foreach ($appointments as $index => $appointment) { ?>
              <tr data-appointment-id="<?= $appointment['appointment_id'] ?>">
                <th scope="row"><?= $index + 1 ?></th>
                <td class="color"><?= $appointment['lecturer_name'] ?></td>
                <td class="color"><?= date('d/m/Y, h:i A', strtotime($appointment['datetime'])) ?></td>
                <td class="color"><?= $appointment['desc'] ?></td>
                <td class="status-td"><?= $appointment['status'] ?></td>
                <td>
                  <a href="index_student.php?edit=<?= $appointment['appointment_id']; ?>" class="btn btn-outline-secondary btn-sm" role="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 20">
                      <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                    </svg>
                    Update
                  </a>
                  <a href="index_student.php?delete=<?= $appointment['appointment_id']; ?>" class="btn btn-danger btn-sm" role="button">
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
        <h2 class="text-center"><?php echo isset($_GET['edit']) ? 'Update Appointment' : 'Schedule New Appointment'; ?></h2>

        <form action="index_student.php" method="post" class="row justify-content-start" id="appointment-form">
          <div class="col-md-6">
            <div class="form-floating mb-2 mt-4">
              <!-- Use a dropdown to select a lecturer's name -->
              <select name="lecturer" class="form-select" id="lecturer" required <?php if (isset($_GET['edit'])) echo 'disabled'; ?>>
                <?php if (isset($_GET['edit'])) { ?>
                  <option value="<?= $editresult['lecturer_name'] ?>" selected>
                    <?= $editresult['lecturer_name'] ?>
                  </option>
                <?php } ?>

                <?php
                $lecturerQuery = "SELECT * FROM users WHERE role = 'lecturer'";
                $lecturerStmt = $conn->query($lecturerQuery);

                while ($lecturer = $lecturerStmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                  <option value="<?= $lecturer['user_id'] ?>">
                    <?= $lecturer['full_name'] ?>
                  </option>
                <?php } ?>
              </select>
              <label for="lecturer">Lecturer name</label>
            </div>
            <div class="form-floating mb-2">
              <!-- Use a datetime picker for date and time -->
              <input name="time" type="datetime-local" class="form-control" id="time" <?php if (isset($_GET['edit'])) {
                                                                                        echo 'value="' . date('Y-m-d\TH:i', strtotime($editresult['datetime'])) . '"';
                                                                                      } ?> required>
              <label for="time">Date and time</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating mb-2 mt-4">
              <input name="desc" type="text" class="form-control" id="desc" placeholder="Enter appointment description" value="<?php if (isset($_GET['edit'])) echo $editresult['desc']; ?>" required>
              <label for="desc">Description</label>
            </div>
            <div class="form-floating mb-2">
              <input name="status" type="text" class="form-control" id="status" placeholder="" value="<?php echo (isset($_GET['edit'])) ? $editresult['status'] : 'Pending'; ?>" required disabled>
              <label for="desc">Status (pending by default)</label>
            </div>
            <div class="d-flex justify-content-end">
              <!-- Show "Update" or "Create" button based on the form content -->
              <button type="reset" class="btn btn-outline-danger btn-lg me-2" id="resetButton">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 20">
                  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                  <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                </svg>
              </button>
              <?php if (isset($_GET['edit'])) { ?>
                <input type="hidden" name="appointment_id" value="<?php echo $editresult['appointment_id']; ?>">
                <button class="btn btn-warning" type="submit" name="update" id="updateButton">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 20">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                  </svg>
                  Update
                </button>
              <?php } else { ?>
                <button class="btn btn-success" type="submit" name="create" id="createButton">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-file-earmark-plus" viewBox="0 0 16 20">
                    <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5z" />
                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z" />
                  </svg>
                  Add
                </button>
              <?php } ?>
            </div>
          </div>
        </form>
      </section>

    </main>

    <footer class="pt-5 text-center">
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
        }
      });
    });
  </script>
  <script>
    document.getElementById('resetButton').addEventListener('click', function() {
      window.location.href = 'index_student.php';
    });
  </script>

</body>

</html>