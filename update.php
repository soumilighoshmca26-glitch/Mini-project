<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "expense_tracker");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If update form is submitted
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    // Update query
    $sql = "UPDATE transactions SET category=?, description=?, amount=?, date=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $category, $description, $amount, $date, $id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Record updated successfully!</p>";
        echo "<a href='manage.php'>Go Back</a>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// If coming from Edit button with id
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM transactions WHERE id=$id");
    $row = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Transaction</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow-lg" style="width:400px;">
        <h3 class="text-center">Update Expense</h3>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" class="form-control" name="category" value="<?php echo $row['category']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" class="form-control" name="description" value="<?php echo $row['description']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $row['amount']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="date" value="<?php echo $row['date']; ?>" required>
            </div>

            <button type="submit" name="update" class="btn btn-primary w-100">Update Expense</button>
        </form>
    </div>
</body>
</html>
