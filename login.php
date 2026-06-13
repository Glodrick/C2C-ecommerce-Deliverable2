<?php
require_once 'functions.php';

// If already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$page_title = 'Login';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_msg = 'Please enter both email and password.';
    } else {
        $user = $user_obj->login($email, $password);
        if ($user) {
            // Setup session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_first_name'] = $user['first_name'];
            $_SESSION['user_last_name'] = $user['last_name'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: index.php');
            exit;
        } else {
            $error_msg = 'Invalid email or password.';
        }
    }
}

require_once 'header.php';
?>

<section class="container mt-5 mb-5" style="max-width:500px;">
    <div style="background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);">
        <h2 class="text-center font-baloo" style="font-weight:800;color:#333;margin-bottom:20px;">
            Welcome Back
        </h2>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger font-rale" style="border-radius:8px;font-size:14px;">
                <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['reg_success'])): ?>
            <div class="alert alert-success font-rale" style="border-radius:8px;font-size:14px;">
                <i class="fas fa-check-circle" style="margin-right:6px;"></i>
                <?php echo htmlspecialchars($_SESSION['reg_success']); unset($_SESSION['reg_success']); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group mb-4">
                <label for="email" class="font-rubik" style="font-weight:600;color:#555;">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" 
                       style="padding:10px 15px;border-radius:8px;" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group mb-4">
                <label for="password" class="font-rubik" style="font-weight:600;color:#555;">Password</label>
                <input type="password" name="password" id="password" class="form-control" 
                       style="padding:10px 15px;border-radius:8px;" required>
            </div>

            <button type="submit" class="btn btn-block color-primary-bg text-white font-baloo" 
                    style="border-radius:25px;padding:12px;font-size:18px;font-weight:700;margin-top:20px;">
                Sign In
            </button>
        </form>

        <div class="text-center mt-4 font-rale" style="font-size:14px;color:#777;">
            Don't have an account? <a href="register.php" style="color:#00A5C4;font-weight:600;">Sign up here</a>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>
