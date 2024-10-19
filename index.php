<?php
require('./dbinit.php');

// Handle form submission (Create/Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$title = $_POST['title'];
	$content = $_POST['content'];
	$author = $_POST['author'];
	$visibility = $_POST['visibility'];
	$imagePath = '';

	// Handle image upload if provided
	if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
		$imageDir = 'images/';
		$imageName = basename($_FILES['image']['name']);
		$imagePath = $imageDir . $imageName;

		// Move uploaded file to the server directory
		if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
			die("Failed to upload image.");
		}
	}

	// Create or Update logic
	if (isset($_POST['PostID']) && !empty($_POST['PostID'])) {
		// Update existing post
		$postID = $_POST['PostID'];
		$sql = "UPDATE blog_posts SET Title='$title', Content='$content', Author='$author', Visibility='$visibility', Image='$imagePath' WHERE PostID=$postID";
	} else {
		// Insert new post
		$sql = "INSERT INTO blog_posts (Title, Content, Author, Visibility, Image) VALUES ('$title', '$content', '$author', '$visibility', '$imagePath')";
	}

	if ($conn->query($sql) === TRUE) {
		echo "Blog post saved successfully!";
	} else {
		echo "Error: " . $conn->error;
	}
}

// Delete a post
if (isset($_GET['delete'])) {
	$postID = $_GET['delete'];
	$sql = "DELETE FROM blog_posts WHERE PostID=$postID";
	if ($conn->query($sql) === TRUE) {
		echo "Blog post deleted successfully!";
	} else {
		echo "Error deleting post: " . $conn->error;
	}
}

// Fetch all blog posts
$posts = $conn->query("SELECT * FROM blog_posts");

?>

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
		<form id="blogForm" method="POST" enctype="multipart/form-data">
			<input type="hidden" id="PostID" name="PostID">

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

	</div>

	<!-- Initialize DataTable and CKEditor -->
	<script>
		$(document).ready(function() {
			$('#blogTable').DataTable();

			// ClassicEditor
			// 	.create(document.querySelector('#content'))
			// 	.catch(error => {
			// 		console.error(error);
			// 	});
		});

		// Function to populate form for editing
		function editPost(postID) {
			// Fetch the blog post data and populate the form for editing
			$.get('get_post.php', {
				id: postID
			}, function(data) {
				const post = JSON.parse(data);
				$('#PostID').val(post.PostID);
				$('#title').val(post.Title);
				$('#content').val(post.Content);
				$('#author').val(post.Author);
				$('#visibility').val(post.Visibility);
				$('#image').val(''); // Clear image input, image can't be populated
			});
		}
	</script>
</body>

</html>