<?php
session_start(); // Start the session

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store form data in session variables
    $_SESSION['cumulative_gpa'] = $_POST['cumulative_gpa'];
    $_SESSION['completed_hours'] = $_POST['completed_hours'];
    $_SESSION['subjects'] = $_POST['subjects'];
}

// Render the HTML below
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPA Calculator</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body dir="rtl">
    <?php include 'assets/includes/navbar.inc.php'; ?>

    <div class="container-fluid text-center text-white mb-4">
        <span class="fs-2 arabic">احسب معدلك الجامعي<br></span>
    </div>

    <div class="container-fluid centered-form">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <form id="mainForm" method="post">
                            <input type="number" class="form-control mb-2" name="cumulative_gpa"
                                style="width: 40%;padding: 12px 10px;" placeholder="المعدل التراكمي"
                                value="<?php echo isset($_SESSION['cumulative_gpa']) ? $_SESSION['cumulative_gpa'] : ''; ?>"
                                required>
                            <input type="number" class="form-control mb-2" name="completed_hours"
                                style="width: 60%;padding: 12px 10px;" placeholder="عدد الساعات المقطوعة"
                                value="<?php echo isset($_SESSION['completed_hours']) ? $_SESSION['completed_hours'] : ''; ?>"
                                required>
                            <hr class="sidebar-divider">
                            <div class="table-responsive arabic">
                                <table id="mainTable" class="table table-bordered table-hover text-center">
                                    <thead class="table-secondary">
                                        <tr class="align-middle">
                                            <th scope="col">علامة المادة</th>
                                            <th scope="col">عدد الساعات</th>
                                            <th scope="col">العلامة السابقة <br>اذا كانت المادة معادة</th>
                                            <th scope="col">حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['subjects']) && is_array($_SESSION['subjects'])) {
                                            foreach ($_SESSION['subjects'] as $index => $subject) {
                                                echo '
                                                <tr>
                                                    <td><input type="number" class="form-control" name="subjects[' . $index . '][grade]" placeholder="100" value="' . $subject['grade'] . '" required></td>
                                                    <td><input type="number" class="form-control" name="subjects[' . $index . '][credits]" placeholder="3" value="' . $subject['credits'] . '" required></td>
                                                    <td><input type="number" class="form-control" name="subjects[' . $index . '][oldGrade]" placeholder="تجاهل" value="' . $subject['oldGrade'] . '"></td>
                                                    <td><button type="button" class="btn btn-danger delete-row"><i class="fas fa-trash"></i></button></td>
                                                </tr>';
                                            }
                                        } else {
                                            echo '
                                            <tr>
                                                <td><input type="number" class="form-control" name="subjects[0][grade]" placeholder="100" required></td>
                                                <td><input type="number" class="form-control" name="subjects[0][credits]" placeholder="3" required></td>
                                                <td><input type="number" class="form-control" name="subjects[0][oldGrade]" placeholder="تجاهل"></td>
                                                <td><button type="button" class="btn btn-danger delete-row"><i class="fas fa-trash"></i></button></td>
                                            </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" id="addRowBtn" class="btn btn-secondary mt-3 me-1 arabic">اضافة
                                    مادة</button>
                            </div>
                            <div class="d-flex justify-content-start">
                                <button type="button" id="calculateBtn"
                                    class="btn btn-success btn-block mt-1 arabic">احسب المعدل</button>
                            </div>
                        </form>
                        <div id="result" class="mt-3 text-center text-success arabic"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            let rowCounter = <?php echo isset($_SESSION['subjects']) ? count($_SESSION['subjects']) + 1 : 1; ?>; // Start counter for row index

            $('#addRowBtn').click(function () {
                if (rowCounter <= 6) { // Check if max rows (7 including initial row) is reached
                    const newRow = `
                  <tr>
                      <td><input type="number" class="form-control" name="subjects[${rowCounter}][grade]" placeholder="100" required></td>
                      <td><input type="number" class="form-control" name="subjects[${rowCounter}][credits]" placeholder="3" required></td>
                      <td><input type="number" class="form-control" name="subjects[${rowCounter}][oldGrade]" placeholder="تجاهل"></td>
                      <td><button type="button" class="btn btn-danger delete-row"><i class="fas fa-trash"></i></button></td>
                  </tr>
              `;
                    $('#mainTable tbody').append(newRow);
                    rowCounter++;
                } else {
                    alert('لا يمكن إضافة المزيد من المواد، تم الوصول إلى الحد الأقصى.');
                }
            });

            // Event delegation for deleting rows, excluding the first row
            $('#mainTable tbody').on('click', '.delete-row', function () {
                const row = $(this).closest('tr');
                if (row.index() !== 0) { // Check if it's not the first row
                    row.remove();
                    rowCounter--; // Decrement row counter
                } else {
                    alert('لا يمكنك حذف السطر الأول.');
                }
            });

            $('#calculateBtn').click(function () {
                var formData = $('#mainForm').serialize();
                $.ajax({
                    url: 'calculate_gpa.php',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#result').html(response);
                    }
                });
            });
        });
    </script>
</body>

</html>