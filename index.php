<?php
require('./dbinit.php');

$editMode = false;
$errors = [];
$toyData = [];
$notify = [];

function notify($msg, $type)
{
	// Define alert types and their corresponding Bootstrap classes
	$alertTypes = [
		'success' => 'alert-success',
		'error' => 'alert-danger',
		'warning' => 'alert-warning',
		'info' => 'alert-info'
	];

	// Get the appropriate class for the type, default to success if not found
	$alertClass = isset($alertTypes[$type]) ? $alertTypes[$type] : $alertTypes['success'];

	// Return the formatted alert HTML
	return "
    <div class='alert $alertClass' role='alert'>
        <strong>Alert!	</strong> $msg
    </div>
    ";
}


// Handle form submission for adding or editing a toy
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Check if editing or adding
	if (isset($_POST['editToy'])) {
		$toyId = $_POST['toyId']; // Added to track the toy being edited
	} else {
		$toyId = null;
	}

	$toyName = trim($_POST['toyName']);
	$toyDescription = trim($_POST['toyDescription']);
	$quantityAvailable = $_POST['quantityAvailable'];
	$price = $_POST['price'];
	$visibility = $_POST['visibility'];

	// Server-side validation
	if (empty($toyName)) {
		$errors['toyName'] = "Toy Name is Required";
	}
	if (empty($toyDescription)) {
		$errors['toyDescription'] = "Toy Description is Required";
	}
	if (empty($quantityAvailable) || !is_numeric($quantityAvailable) || $quantityAvailable < 0) {
		$errors['quantityAvailable'] = "Quantity Available must be a non-negative number.";
	}
	if (empty($price) || !is_numeric($price) || $price < 0) {
		$errors['price'] = "Price must be a non-negative number.";
	}
	if (empty($visibility)) {
		$errors['visibility'] = "Visibility Field is Required";
	}

	// If there are no errors, proceed to insert or update the toy
	if (empty($errors)) {
		if (isset($_POST['addToy'])) {
			// Prepare statement for adding a toy
			$stmt = $conn->prepare("INSERT INTO toys (ToyName, ToyDescription, QuantityAvailable, Price, Visibility) VALUES (?, ?, ?, ?, ?)");
			$stmt->bind_param("ssids", $toyName, $toyDescription, $quantityAvailable, $price, $visibility);
		} elseif (isset($_POST['editToy'])) {
			// Prepare statement for updating a toy
			$stmt = $conn->prepare("UPDATE toys SET ToyName = ?, ToyDescription = ?, QuantityAvailable = ?, Price = ?, Visibility = ? WHERE ToyID = ?");
			$stmt->bind_param("ssidsi", $toyName, $toyDescription, $quantityAvailable, $price, $visibility, $toyId);
		}

		if ($stmt->execute()) {
			$notify[] = notify(isset($_POST['editToy']) ? "Toy updated successfully<br>" : "Toy added successfully<br>", "success");

			// Reset form fields after successful operation
			$toyData = []; // Clear toyData array
			$_POST = array(); // Clear $_POST array
			$editMode = false; // Reset edit mode
		} else {
			$notify[] = notify("Error: " . $stmt->error, "error");
		}
		$stmt->close();
	} else {
		$notify[] = notify("Error: Please correct one or more errors in the form.", "error");
	}
}

// Handle delete request
if (isset($_GET['delete'])) {
	$toyId = $_GET['delete'];
	$stmt = $conn->prepare("DELETE FROM toys WHERE ToyID = ?");
	$stmt->bind_param("i", $toyId);
	if ($stmt->execute()) {
		$notify[] = notify("Toy deleted successfully", "success");
	} else {
		$notify[] = notify("Error deleting toy: " . $stmt->error, "error");
	}
	$stmt->close();
}

// Fetch toys from the database
$result = $conn->query("SELECT * FROM toys");

// Check if an edit is requested
if (isset($_GET['id'])) {
	$editMode = true;
	$toyId = $_GET['id'];
	$stmt = $conn->prepare("SELECT * FROM toys WHERE ToyID = ?");
	$stmt->bind_param("i", $toyId);
	$stmt->execute();
	$toyData = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Panel - Boing! Toys</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<link rel="stylesheet" href="./css/style.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
</head>

<body>

	<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
		<a class="text-center p-3 navbar-brand col-md-3 col-lg-2 me-0 px-3" href="./index.php">Boing! Toys</a>
		<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="navbar-nav">
			<!-- <div class="nav-item text-nowrap button bg-primary rounded m-2">
				<a class="nav-link px-3" href="#">Sign out</a>
			</div> -->
		</div>
	</header>

	<div class="container-fluid">
		<div class="row">
			<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
				<div class="position-sticky pt-3">
					<ul class="nav flex-column">
						<li class="nav-item">
							<a class="nav-link text-light bg-dark active" aria-current="page" href="./index.php">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart" aria-hidden="true">
									<circle cx="9" cy="21" r="1"></circle>
									<circle cx="20" cy="21" r="1"></circle>
									<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
								</svg>
								Products
							</a>
						</li>
					</ul>
				</div>
			</nav>

			<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
					<h1 class="h2">Dashboard</h1>
					<img class="w-100" style="max-width: 150px;" src="./images/logo.png" alt="logo">
				</div>
				<?php
				// Display all notifications
				foreach ($notify as $message) {
					echo $message;
				}
				?>
				<hr>

				<!-- Toy Form -->
				<h2><?= $editMode ? 'Edit Toy' : 'Add a Toy' ?></h2>
				<form method="POST" action="index.php">
					<input type="hidden" name="toyId" value="<?= $editMode ? $toyData['ToyID'] : ''; ?>">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="toyName" class="form-label">Toy Name</label>
							<input type="text" class="form-control" id="toyName" name="toyName" value="<?= $editMode ? htmlspecialchars($toyData['ToyName']) : (isset($_POST['toyName']) ? htmlspecialchars($_POST['toyName']) : ''); ?>">
							<?php if (isset($errors['toyName'])): ?>
								<div class="text-danger"><?= htmlspecialchars($errors['toyName']); ?></div>
							<?php endif; ?>
						</div>
						<div class="col-md-6 mb-3">
							<label for="quantityAvailable" class="form-label">Quantity Available</label>
							<input type="number" class="form-control" id="quantityAvailable" name="quantityAvailable" value="<?= $editMode ? htmlspecialchars($toyData['QuantityAvailable']) : (isset($_POST['quantityAvailable']) ? htmlspecialchars($_POST['quantityAvailable']) : ''); ?>" min="0">
							<?php if (isset($errors['quantityAvailable'])): ?>
								<div class="text-danger"><?= htmlspecialchars($errors['quantityAvailable']); ?></div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="toyDescription" class="form-label">Toy Description</label>
							<textarea class="form-control" id="toyDescription" name="toyDescription"><?= $editMode ? htmlspecialchars($toyData['ToyDescription']) : (isset($_POST['toyDescription']) ? htmlspecialchars($_POST['toyDescription']) : ''); ?></textarea>
							<?php if (isset($errors['toyDescription'])): ?>
								<div class="text-danger"><?= htmlspecialchars($errors['toyDescription']); ?></div>
							<?php endif; ?>
						</div>
						<div class="col-md-6 mb-3">
							<label for="price" class="form-label">Price</label>
							<input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $editMode ? htmlspecialchars($toyData['Price']) : (isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''); ?>" min="0">
							<?php if (isset($errors['price'])): ?>
								<div class="text-danger"><?= htmlspecialchars($errors['price']); ?></div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="productAddedBy" class="form-label">Added By</label>
							<input type="text" class="form-control" value="Lakhvinder Singh" readonly id="productAddedBy" name="productAddedBy">
						</div>
						<div class="col-md-6 mb-3">
							<label for="visibility" class="form-label">Visibility</label>
							<select class="form-select" id="visibility" name="visibility">
								<option disabled>Select visility</option>
								<option value="visible" <?= $editMode && $toyData['Visibility'] == 'visible' ? 'selected' : ''; ?>>Visible</option>
								<option value="hidden" <?= $editMode && $toyData['Visibility'] == 'hidden' ? 'selected' : ''; ?>>Hidden</option>
							</select>
							<?php if (isset($errors['visibility'])): ?>
								<div class="text-danger"><?= htmlspecialchars($errors['visibility']); ?></div>
							<?php endif; ?>
						</div>
						<div class="col-md-6 mb-3 d-flex align-items-end">
							<button type="submit" name="<?= $editMode ? 'editToy' : 'addToy' ?>" class="btn btn-primary"><?= $editMode ? 'Update Toy' : 'Add Toy' ?></button>
							<?= $editMode ? '<a class="btn mx-3 btn-secondary" href="./index.php">Cancel</a>' : '' ?>
						</div>
					</div>
				</form>

				<h2>Products List</h2>
				<div class="table-responsive">
					<table id="toysTable" class="table table-striped table-sm">
						<thead>
							<tr>
								<th scope="col">Toy ID</th>
								<th scope="col">Toy Name</th>
								<th scope="col">Description</th>
								<th scope="col">Quantity Available</th>
								<th scope="col">Price</th>
								<th scope="col">Added By</th>
								<th scope="col">Created At</th>
								<th scope="col">Visibility</th>
								<th scope="col">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($result->num_rows > 0): ?>
								<?php while ($row = $result->fetch_assoc()): ?>
									<tr>
										<td><?= $row['ToyID']; ?></td>
										<td><?= $row['ToyName']; ?></td>
										<td><?= $row['ToyDescription']; ?></td>
										<td><?= $row['QuantityAvailable']; ?></td>
										<td><?= $row['Price']; ?></td>
										<td><?= $row['ProductAddedBy']; ?></td>
										<td><?= $row['CreatedAt']; ?></td>
										<td><?= $row['Visibility']; ?></td>
										<td>
											<a href="?id=<?= $row['ToyID']; ?>" class="btn btn-warning btn-sm">
												<i class="bi bi-pencil-square"></i> Edit
											</a>
											<a href="?delete=<?= $row['ToyID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this toy?');">
												<i class="bi bi-trash"></i> Delete
											</a>
										</td>
									</tr>
								<?php endwhile; ?>
							<?php else: ?>
								<tr>
									<td colspan="9">No toys available</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</main>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#toysTable').DataTable();
		});
	</script>
</body>

</html>