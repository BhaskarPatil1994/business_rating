<?php include 'config/db.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Business Listing</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.js"></script>
</head>

<body class="container mt-4">

    <h2>Business Listing</h2>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Add Business</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Business Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Average Rating</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="businessData"></tbody>
    </table>

    <!-- ADD MODAL -->
    <div class="modal" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add Business</h4>
                </div>
                <div class="modal-body">
                    <form id="addForm">
                        <input name="name" class="form-control mb-2" placeholder="Name" required>
                        <input name="address" class="form-control mb-2" placeholder="Address">
                        <input name="phone" class="form-control mb-2" placeholder="Phone">
                        <input name="email" class="form-control mb-2" placeholder="Email">
                        <button class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Edit Business</h4>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" name="id" id="edit_id">
                        <input name="name" id="edit_name" class="form-control mb-2">
                        <input name="address" id="edit_address" class="form-control mb-2">
                        <input name="phone" id="edit_phone" class="form-control mb-2">
                        <input name="email" id="edit_email" class="form-control mb-2">
                        <button class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- RATING MODAL -->
    <div class="modal" id="ratingModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Rate Business</h4>
                </div>
                <div class="modal-body">
                    <form id="ratingForm">
                        <input type="hidden" name="business_id" id="rating_business_id">
                        <input name="name" class="form-control mb-2" placeholder="Name" required>
                        <input name="email" class="form-control mb-2" placeholder="Email">
                        <input name="phone" class="form-control mb-2" placeholder="Phone">

                        <div id="userRating"></div>
                        <input type="hidden" name="rating" id="rating_value">
                        <div id="ratingSummary" class="mt-3"></div>
                         <div class="mt-3 text-right">
                       
                        <button type="submit" class="btn btn-success">
                            Submit Rating
                        </button>
                         <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadBusinesses() {
            $.get('ajax/get_business.php', function (data) {
                $('#businessData').html(data);

                 if ($(this).data('raty')) {
                $(this).raty('destroy');
            }

                $('.avg-rating').raty({
                    readOnly: true,
                    half: true,
                    path: 'https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/images',
                    score: function () { return $(this).data('score'); }
                });
            });
        }

        loadBusinesses();

        /* ADD */
        $('#addForm').submit(function (e) {
            e.preventDefault();
            $.post('ajax/add_business.php', $(this).serialize(), function () {
                $('#addModal').modal('hide');
                loadBusinesses();
            });
        });

        /* EDIT */
        function editBusiness(id) {
            $.get('ajax/get_business.php?id=' + id, function (data) {
                let d = JSON.parse(data);
                $('#edit_id').val(d.id);
                $('#edit_name').val(d.name);
                $('#edit_address').val(d.address);
                $('#edit_phone').val(d.phone);
                $('#edit_email').val(d.email);
                $('#editModal').modal('show');
            });
        }

        $('#editForm').submit(function (e) {
            e.preventDefault();
            $.post('ajax/update_business.php', $(this).serialize(), function () {
                $('#editModal').modal('hide');
                loadBusinesses();
            });
        });

        /* DELETE */
        function deleteBusiness(id) {
            if (confirm('Delete?')) {
                $.post('ajax/delete_business.php', { id: id }, function () {
                    loadBusinesses();
                });
            }
        }

        /* OPEN RATING */
        function openRating(id) {
            $('#rating_business_id').val(id);

            // Reset form
            $('#ratingForm')[0].reset();
            $('#rating_value').val(0);

            if ($('#userRating').data('raty')) {
                $('#userRating').raty('destroy');
            }

            $('#userRating').html('');

            $('#userRating').raty({
                half: true,
                path: 'https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/images',
                score: 0,
                click: function (score) {
                    $('#rating_value').val(score);
                }
            });

            // ✅ LOAD SUMMARY
            $.get('ajax/get_rating_summary.php?business_id=' + id, function (res) {
                let response = JSON.parse(res);
                let data = response.counts;
                let total = response.total;

                let html = "<h6>Rating Breakdown:</h6>";

                for (let i = 5; i >= 1; i--) {
                    let count = data[i] || 0;
                    let percent = total > 0 ? ((count / total) * 100).toFixed(1) : 0;

                    html += `
            <div class="mb-2">
                ${i} ⭐ (${count})
                <div class="progress">
                    <div class="progress-bar" style="width:${percent}%">
                        ${percent}%
                    </div>
                </div>
            </div>
            `;
                }

                html += `<small>Total Ratings: ${total}</small>`;

                $('#ratingSummary').html(html);
            });

            $('#ratingModal').modal('show');
        }

        /* SUBMIT RATING */
        $('#ratingForm').submit(function (e) {
            e.preventDefault();
            $.post('ajax/submit_rating.php', $(this).serialize(), function () {
                $('#ratingModal').modal('hide');
                loadBusinesses();
            });
        });
    </script>

</body>

</html>