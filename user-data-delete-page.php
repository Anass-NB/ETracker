<?php
/**
 * Delete Employee Page
 * Provides a confirmation interface for deleting an employee
 */

// Include header with proper page title
$pageTitle = "Delete Employee";
require_once 'includes/header.php';

$id = null;
if (!empty($_GET['id'])) {
    $id = $_REQUEST['id'];
}

if (!empty($_POST)) {
    // Process delete action when form is submitted
    $id = $_POST['id'];
    
    // Delete data from employees table
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "DELETE FROM employees WHERE rfid_uid = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($id));
    Database::disconnect();
    
    // Redirect to the employee list
    header("Location: dashboard .php");
    exit;
}

// Get employee info for the confirmation message
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT * FROM employees WHERE rfid_uid = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$employee = $q->fetch(PDO::FETCH_ASSOC);
Database::disconnect();

// If employee not found, redirect to the list
if (!$employee) {
    header("Location: user-data.php");
    exit;
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="fas fa-trash me-2 text-danger"></i> Delete Employee</h3>
        <p class="text-muted mb-0">Permanently remove employee record from the system</p>
    </div>
    <a href="user-data.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i> Back to Employee List
    </a>
</div>

<!-- Delete Confirmation Card -->
<div class="app-card">
    <div class="card-header bg-danger">
        <h5 class="mb-0 text-white"><i class="fas fa-exclamation-triangle me-2"></i> Delete Confirmation</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center mb-4 mb-md-0">
                <?php if (!empty($employee['profile_image'])): ?>
                <img src="<?php echo htmlspecialchars($employee['profile_image']); ?>"
                    class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;"
                    alt="Employee Photo">
                <?php else: ?>
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center bg-primary text-white"
                    style="width: 150px; height: 150px; font-size: 60px;">
                    <?php echo substr($employee['name'], 0, 1); ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-9">
                <div class="alert alert-danger mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All data associated with this employee will
                    be permanently deleted.
                </div>

                <h4 class="mb-3"><?php echo htmlspecialchars($employee['name']); ?></h4>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="fas fa-id-card me-2"></i> RFID UID:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($employee['rfid_uid']); ?></p>
                    </div>

                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="fas fa-envelope me-2"></i> Email:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($employee['email']); ?></p>
                    </div>

                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="fas fa-phone me-2"></i> Mobile:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($employee['mobile']); ?></p>
                    </div>

                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="fas fa-building me-2"></i> Department:</strong></p>
                        <p class="text-muted">
                            <?php echo !empty($employee['department']) ? htmlspecialchars($employee['department']) : 'Not assigned'; ?>
                        </p>
                    </div>
                </div>

                <form action="user-data-delete-page.php" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />
                    <div class="d-flex justify-content-end mt-4">
                        <a href="user-data.php" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i> Delete Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>