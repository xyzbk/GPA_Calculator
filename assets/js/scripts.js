document.addEventListener('DOMContentLoaded', function () {
    const addRowBtn = document.getElementById('addRowBtn');
    const tableBody = document.querySelector('#mainTable tbody');

    let rowCounter = 1; // Start counter for row index

    addRowBtn.addEventListener('click', function () {
        if (rowCounter <= 6) { // Check if max rows (7 including initial row) is reached
            const newRow = `
              <tr>
                  <td><input type="number" class="form-control" name="subjects[${rowCounter}][grade]" placeholder="100"></td>
                  <td><input type="number" class="form-control" name="subjects[${rowCounter}][credits]" placeholder="3"></td>
                  <td><input type="number" class="form-control" name="subjects[${rowCounter}][oldGrade]" placeholder="تجاهل"></td>
                  <td><button type="button" class="btn btn-danger delete-row"><i class="fas fa-trash"></i></button></td>
              </tr>
          `;
            tableBody.insertAdjacentHTML('beforeend', newRow);
            rowCounter++;
        } else {
            alert('لا يمكن إضافة المزيد من المواد، تم الوصول إلى الحد الأقصى.');
        }
    });

    // Event delegation for deleting rows, excluding the first row
    tableBody.addEventListener('click', function (event) {
        if (event.target.classList.contains('delete-row')) {
            const row = event.target.closest('tr');
            if (row.previousElementSibling !== null) { // Check if it's not the first row
                row.remove();
                rowCounter--; // Decrement row counter
            } else {
                alert('لا يمكنك حذف السطر الأول.');
            }
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


