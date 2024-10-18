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

</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">Blog Management</h1>

    <!-- Add New Blog Post Button -->
    <div class="text-end">
        <button class="btn btn-primary mb-3" data-bs-toggle data-bs-targetAdd New Post</button>
    </div>

		
		<form id="blogForm">
			<div class="">
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
			<div class="">
					<button type="submit" class="btn btn-primary">Save</button>
			</div>
	</form>


   
</div>


</body>
</html>
