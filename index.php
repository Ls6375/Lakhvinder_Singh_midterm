<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Blog Management</title>

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

	<!-- jQuery (for AJAX) and DataTables scripts -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

	<!-- CKEditor 5 (Classic) -->
	<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>

</head>

<body>
	<div class="container mt-4">
		<h1 class="text-center">Blog Management</h1>

		<!-- Blog Form -->
		<form id="blogForm">

			<div class="modal-body">
				<div class="mb-3">
					<label for="title" class="form-label">Title</label>
					<input type="text" class="form-control" id="title" name="title" required>
				</div>
				<div class="mb-3">
					<label for="content" class="form-label">Content</label>
					<textarea class="form-control" id="content" name="content" rows="4" required></textarea>
				</div>
				<div class="mb-3">
					<label for="author" class="form-label">Author</label>
					<input type="text" class="form-control" id="author" name="author" required>
				</div>
				<div class="mb-3">
					<label for="visibility" class="form-label">Visibility</label>
					<select class="form-control" id="visibility" name="visibility">
						<option value="visible">Visible</option>
						<option value="hidden">Hidden</option>
					</select>
				</div>
				<div class="mb-3">
					<label for="image" class="form-label">Image</label>
					<input type="file" class="form-control" id="image" name="image">
				</div>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>

		<hr>

		<!-- Blog Table -->
		<table id="blogTable" class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>Content</th>
					<th>Author</th>
					<th>Visibility</th>
					<th>Image</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<!-- Sample Row (Dynamic rows will be generated via PHP) -->
				<tr>
					<td>1</td>
					<td>Sample Blog Title</td>
					<td>Sample Blog Content goes here...</td>
					<td>Lakhvinder Singh</td>
					<td>Visible</td>
					<td><img src="path_to_image.jpg" width="50" height="50" alt="Sample Image"></td>
					<td>
						<button class="btn btn-warning btn-sm">Edit</button>
						<button class="btn btn-danger btn-sm">Delete</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- Modal for Adding/Editing Blog Posts -->
	<div class="modal fade" id="blogModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

			</div>
		</div>
	</div>

	<!-- Bootstrap JS Bundle -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

	<!-- DataTable and CKEditor -->
	<script>
		$(document).ready(function() {
			$('#blogTable').DataTable();  
			ClassicEditor
				.create(document.querySelector('#content'))
				.catch(error => {
					console.error(error);
				});
		});
	</script>

</body>

</html>
