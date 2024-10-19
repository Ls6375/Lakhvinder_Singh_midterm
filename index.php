<?php
require('./dbinit.php');

// Function to sanitize input data
function sanitizeInput($data, $conn)
{
	return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

$errors = []; // Array to hold error messages

// Handle form submission (Create/Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Validate required fields
	if (empty($_POST['title'])) {
		$errors['title'] = "Title is required.";
	}
	if (empty($_POST['content'])) {
		$errors['content'] = "Content is required.";
	}
	if (empty($_POST['author'])) {
		$errors['author'] = "Author is required.";
	}

	// Check if this is a new post (i.e., no PostID)
	$isCreating = !isset($_POST['PostID']) || empty($_POST['PostID']);

	// If creating a new post, image is required
	if ($isCreating) {
		if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
			$errors['image'] = "Image is required when creating a new blog post.";
		}
	}

	// Sanitize inputs if there are no errors
	if (empty($errors)) {
		$title = sanitizeInput($_POST['title'], $conn);
		$content = sanitizeInput($_POST['content'], $conn);
		$author = sanitizeInput($_POST['author'], $conn);
		$visibility = isset($_POST['visibility']) ? sanitizeInput($_POST['visibility'], $conn) : 'hidden'; // default to hidden if not set
		$imagePath = '';

		// Validate image upload if provided
		if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
			$imageDir = 'images/';
			$imageName = basename($_FILES['image']['name']);
			$imagePath = $imageDir . $imageName;

			// Check file type (only allow JPEG, PNG, GIF)
			$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
			$imageType = mime_content_type($_FILES['image']['tmp_name']);

			if (!in_array($imageType, $allowedMimeTypes)) {
				$errors['image'] = "Only JPEG, PNG, and GIF image formats are allowed.";
			}

			// Check file size (limit to 2MB)
			if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
				$errors['image'] = "Image size should not exceed 2MB.";
			}

			// Move uploaded file to the server directory
			if (empty($errors['image']) && !move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
				$errors['image'] = "Failed to upload image.";
			}
		}

		// Proceed with inserting or updating the post if there are no errors
		if (empty($errors)) {
			if (!$isCreating) {
				// Update existing post
				$postID = intval($_POST['PostID']);
				// Update the image only if a new one was uploaded
				$sql = $imagePath
					? "UPDATE blog_posts SET Title='$title', Content='$content', Author='$author', Visibility='$visibility', Image='$imagePath' WHERE PostID=$postID"
					: "UPDATE blog_posts SET Title='$title', Content='$content', Author='$author', Visibility='$visibility' WHERE PostID=$postID";
			} else {
				// Insert new post
				$sql = "INSERT INTO blog_posts (Title, Content, Author, Visibility, Image) VALUES ('$title', '$content', '$author', '$visibility', '$imagePath')";
			}

			if ($conn->query($sql) === TRUE) {
				$msg = "Blog post saved successfully!";
				$_POST = []; // rest form fields
			} else {
				echo "Error: " . $conn->error;
			}
		}
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
		<?php
		if (isset($msg)) {
			echo <<<HTML
        <div class="alert alert-primary" role="alert">
            <h4 class="alert-heading"></h4>
            <p>$msg</p>
        </div>
HTML;
		}
		?>


		<!-- Blog Form -->
		<form id="blogForm" method="POST" enctype="multipart/form-data">
			<input type="hidden" id="PostID" name="PostID">

			<div class="modal-body">
				<div class="mb-3">
					<label for="title" class="form-label">Title</label>
					<input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
					<div class="invalid-feedback">
						<?php echo isset($errors['title']) ? $errors['title'] : ''; ?>
					</div>
				</div>

				<div class="mb-3">
					<label for="content" class="form-label">Content</label>
					<textarea class="form-control <?php echo isset($errors['content']) ? 'is-invalid' : ''; ?>" id="content" name="content" rows="4"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
					<div class="invalid-feedback">
						<?php echo isset($errors['content']) ? $errors['content'] : ''; ?>
					</div>
				</div>

				<div class="mb-3">
					<label for="author" class="form-label">Author</label>
					<input type="text" class="form-control <?php echo isset($errors['author']) ? 'is-invalid' : ''; ?>" id="author" name="author" value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>">
					<div class="invalid-feedback">
						<?php echo isset($errors['author']) ? $errors['author'] : ''; ?>
					</div>
				</div>

				<div class="mb-3">
					<label for="visibility" class="form-label">Visibility</label>
					<select class="form-control" id="visibility" name="visibility">
						<option value="visible" <?php echo (isset($_POST['visibility']) && $_POST['visibility'] == 'visible') ? 'selected' : ''; ?>>Visible</option>
						<option value="hidden" <?php echo (isset($_POST['visibility']) && $_POST['visibility'] == 'hidden') ? 'selected' : ''; ?>>Hidden</option>
					</select>
				</div>

				<div class="mb-3">
					<label for="image" class="form-label">Image</label>
					<input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image">
					<div class="invalid-feedback">
						<?php echo isset($errors['image']) ? $errors['image'] : ''; ?>
					</div>
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
    <?php
    if ($posts->num_rows > 0) {
        while ($row = $posts->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['PostID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Content']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Author']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Visibility']) . "</td>";
            echo "<td>";
            if (!empty($row['Image'])) {
                echo '<img src="' . htmlspecialchars($row['Image']) . '" alt="Image" style="max-width: 100px;">';
            } else {
                echo 'No image';
            }
            echo "</td>";
            echo '<td>';
            echo '<button class="btn btn-warning btn-sm editBtn" data-id="' . htmlspecialchars($row['PostID']) . '">Edit</button>';
            echo '<button class="btn btn-danger btn-sm deleteBtn" data-id="' . htmlspecialchars($row['PostID']) . '">Delete</button>';
            echo '</td>';
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No blog posts found.</td></tr>";
    }
    ?>
</tbody>

		</table>
	</div>

	<!-- Initialize DataTable -->
	<script>
		$(document).ready(function() {
			$('#blogTable').DataTable();
		});
	</script>
</body>

</html>