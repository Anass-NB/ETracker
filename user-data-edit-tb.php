<?php
/**
 * Edit Employee Page
 * Provides an interface for updating employee information
 */

// Include header with proper page title
$pageTitle = "Edit Employee";
require_once 'includes/header.php';

// Get employee ID from URL
$id = null;
if (!empty($_GET['id'])) {
    $id = $_REQUEST['id'];
}

// Process form submission
if (!empty($_POST) && !empty($_GET['id'])) {
    // Get form data
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $department = isset($_POST['department']) ? $_POST['department'] : null;
    $position = isset($_POST['position']) ? $_POST['position'] : null;
    
    // Update the database - employees table
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "UPDATE employees SET name = ?, gender = ?, email = ?, mobile = ?";
    $params = array($name, $gender, $email, $mobile);
    
    // Add department and position if they exist
    if ($department !== null) {
        $sql .= ", department = ?";
        $params[] = $department;
    }
    
    if ($position !== null) {
        $sql .= ", position = ?";
        $params[] = $position;
    }
    
    $sql .= " WHERE rfid_uid = ?";
    $params[] = $id;
    
    $q = $pdo->prepare($sql);
    $q->execute($params);
    
    Database::disconnect();
    
    // Redirect to the user data page
    header("Location: dashboard.php");
    exit;
}

// Get employee data
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT * FROM employees WHERE rfid_uid = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);
Database::disconnect();

// If no data found, redirect
if (!$data) {
    header("Location: user-data.php");
    exit;
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="fas fa-user-edit me-2"></i> Edit Employee</h3>
        <p class="text-muted mb-0">Update information for <?php echo htmlspecialchars($data['name']); ?></p>
    </div>
    <a href="user-data.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i> Back to Employee List
    </a>
</div>

<!-- Edit Form -->
<div class="app-card">
    <div class="card-header bg-primary">
        <h4 class="mb-0 text-white"><i class="fas fa-edit me-2"></i>Employee Information</h4>
    </div>
    <div class="card-body">
        <form class="app-form needs-validation" action="user-data-edit-tb.php?id=<?php echo $id; ?>" method="post"
            novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="rfid_uid" class="form-label">RFID UID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" id="rfid_uid" name="rfid_uid"
                            value="<?php echo htmlspecialchars($data['rfid_uid']); ?>" readonly>
                    </div>
                    <div class="form-text">RFID identifier cannot be changed</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?php echo htmlspecialchars($data['name']); ?>" required>
                    </div>
                    <div class="invalid-feedback">Please enter the employee's name.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="Male" <?php echo ($data['gender'] === 'Male') ? 'selected' : ''; ?>>Male
                            </option>
                            <option value="Female" <?php echo ($data['gender'] === 'Female') ? 'selected' : ''; ?>>
                                Female</option>
                            <option value="Other" <?php echo ($data['gender'] === 'Other') ? 'selected' : ''; ?>>Other
                            </option>
                        </select>
                    </div>
                    <div class="invalid-feedback">Please select a gender.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?php echo htmlspecialchars($data['email']); ?>" required>
                    </div>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="mobile" class="form-label">Mobile Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control" id="mobile" name="mobile"
                            value="<?php echo htmlspecialchars($data['mobile']); ?>" required>
                    </div>
                    <div class="invalid-feedback">Please enter a mobile number.</div>
                </div>

                <?php if (isset($data['department'])): ?>
                <div class="col-md-6 mb-3">
                    <label for="department" class="form-label">Department</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" class="form-control" id="department" name="department"
                            value="<?php echo htmlspecialchars($data['department'] ?? ''); ?>">
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (isset($data['position'])): ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="position" class="form-label">Position</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                        <input type="text" class="form-control" id="position" name="position"
                            value="<?php echo htmlspecialchars($data['position'] ?? ''); ?>">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="hire_date" class="form-label">Hire Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="date" class="form-control" id="hire_date" name="hire_date"
                            value="<?php echo htmlspecialchars($data['hire_date'] ?? date('Y-m-d')); ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-end mt-4">
                <a href="user-data.php" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>