<?php
require_once 'functions.php';

// If already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$page_title = 'Register';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($password)) {
        $error_msg = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Please enter a valid email address.';
    } elseif ($password !== $confirm_password) {
        $error_msg = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error_msg = 'Password must be at least 6 characters.';
    } elseif ($user_obj->emailExists($email)) {
        $error_msg = 'Email address is already registered.';
    } else {
        // Register the user (default role: buyer)
        $new_id = $user_obj->register($first_name, $last_name, $email, $username, $password, 'buyer');
        
        if ($new_id) {
            $_SESSION['reg_success'] = 'Account created successfully! You can now log in.';
            header('Location: login.php');
            exit;
        } else {
            $error_msg = 'Failed to create account. Please try again.';
        }
    }
}

require_once 'header.php';
?>

<section class="container mt-5 mb-5" style="max-width:600px;">
    <div style="background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);">
        <h2 class="text-center font-baloo" style="font-weight:800;color:#333;margin-bottom:20px;">
            Create an Account
        </h2>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger font-rale" style="border-radius:8px;font-size:14px;">
                <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="row">
                <div class="col-sm-6 form-group mb-4">
                    <label for="first_name" class="font-rubik" style="font-weight:600;color:#555;">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" 
                           style="padding:10px 15px;border-radius:8px;" required
                           value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                </div>
                <div class="col-sm-6 form-group mb-4">
                    <label for="last_name" class="font-rubik" style="font-weight:600;color:#555;">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" 
                           style="padding:10px 15px;border-radius:8px;" required
                           value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="username" class="font-rubik" style="font-weight:600;color:#555;">Username</label>
                <input type="text" name="username" id="username" class="form-control" 
                       style="padding:10px 15px;border-radius:8px;" required
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="form-group mb-4">
                <label for="email" class="font-rubik" style="font-weight:600;color:#555;">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" 
                       style="padding:10px 15px;border-radius:8px;" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="row">
                <div class="col-sm-6 form-group mb-4">
                    <label for="password" class="font-rubik" style="font-weight:600;color:#555;">Password</label>
                    <input type="password" name="password" id="password" class="form-control" 
                           style="padding:10px 15px;border-radius:8px;" required minlength="6">
                </div>
                <div class="col-sm-6 form-group mb-4">
                    <label for="confirm_password" class="font-rubik" style="font-weight:600;color:#555;">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                           style="padding:10px 15px;border-radius:8px;" required minlength="6">
                </div>
            </div>

            <button type="submit" class="btn btn-block color-primary-bg text-white font-baloo" 
                    style="border-radius:25px;padding:12px;font-size:18px;font-weight:700;margin-top:20px;">
                Sign Up
            </button>
        </form>

        <div class="text-center mt-4 font-rale" style="font-size:14px;color:#777;">
            Already have an account? <a href="login.php" style="color:#00A5C4;font-weight:600;">Sign in here</a>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>
