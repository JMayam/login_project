<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            header("location: dashboard.php");
                            exit;
                        } else {
                            $error = "Invalid username or password";
                        }
                    }
                } else {
                    $error = "Invalid username or password";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        :root {
            --color-white: rgba(255, 255, 255, 1);
            --color-black: rgba(0, 0, 0, 1);
            --color-cream-50: rgba(252, 252, 249, 1);
            --color-cream-100: rgba(255, 255, 253, 1);
            --color-gray-200: rgba(245, 245, 245, 1);
            --color-gray-300: rgba(167, 169, 169, 1);
            --color-gray-400: rgba(119, 124, 124, 1);
            --color-slate-500: rgba(98, 108, 113, 1);
            --color-brown-600: rgba(94, 82, 64, 1);
            --color-charcoal-700: rgba(31, 33, 33, 1);
            --color-charcoal-800: rgba(38, 40, 40, 1);
            --color-slate-900: rgba(19, 52, 59, 1);
            --color-teal-300: rgba(50, 184, 198, 1);
            --color-teal-400: rgba(45, 166, 178, 1);
            --color-teal-500: rgba(33, 128, 141, 1);
            --color-teal-600: rgba(29, 116, 128, 1);
            --color-teal-700: rgba(26, 104, 115, 1);
            --color-brown-600-rgb: 94, 82, 64;
            --color-teal-500-rgb: 33, 128, 141;
            --color-slate-900-rgb: 19, 52, 59;
            --color-background: var(--color-cream-50);
            --color-surface: var(--color-cream-100);
            --color-text: var(--color-slate-900);
            --color-text-secondary: var(--color-slate-500);
            --color-primary: var(--color-teal-500);
            --color-primary-hover: var(--color-teal-600);
            --color-primary-active: var(--color-teal-700);
            --color-secondary: rgba(var(--color-brown-600-rgb), 0.12);
            --color-border: rgba(var(--color-brown-600-rgb), 0.2);
            --color-btn-primary-text: var(--color-cream-50);
            --color-card-border: rgba(var(--color-brown-600-rgb), 0.12);
            --color-focus-ring: rgba(var(--color-teal-500-rgb), 0.4);
            --font-family-base: "FKGroteskNeue", "Geist", "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-size-sm: 12px;
            --font-size-base: 14px;
            --font-size-lg: 16px;
            --font-size-3xl: 24px;
            --font-weight-medium: 500;
            --font-weight-semibold: 550;
            --space-8: 8px;
            --space-12: 12px;
            --space-16: 16px;
            --space-24: 24px;
            --space-32: 32px;
            --radius-base: 8px;
            --radius-lg: 12px;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            --duration-normal: 250ms;
            --ease-standard: cubic-bezier(0.16, 1, 0.3, 1);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --color-gray-400-rgb: 119, 124, 124;
                --color-teal-300-rgb: 50, 184, 198;
                --color-gray-300-rgb: 167, 169, 169;
                --color-gray-200-rgb: 245, 245, 245;
                --color-background: var(--color-charcoal-700);
                --color-surface: var(--color-charcoal-800);
                --color-text: var(--color-gray-200);
                --color-text-secondary: rgba(var(--color-gray-300-rgb), 0.7);
                --color-primary: var(--color-teal-300);
                --color-primary-hover: var(--color-teal-400);
                --color-secondary: rgba(var(--color-gray-400-rgb), 0.15);
                --color-border: rgba(var(--color-gray-400-rgb), 0.3);
                --color-btn-primary-text: var(--color-slate-900);
                --color-card-border: rgba(var(--color-gray-400-rgb), 0.2);
                --color-focus-ring: rgba(var(--color-teal-300-rgb), 0.4);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family-base);
            font-size: var(--font-size-base);
            color: var(--color-text);
            background-color: var(--color-background);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: var(--space-16);
            -webkit-font-smoothing: antialiased;
        }

        .login-container {
            background-color: var(--color-surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-card-border);
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 400px;
            padding: var(--space-32);
        }

        h1 {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-semibold);
            color: var(--color-text);
            margin-bottom: var(--space-8);
            text-align: center;
        }

        .subtitle {
            font-size: var(--font-size-base);
            color: var(--color-text-secondary);
            text-align: center;
            margin-bottom: var(--space-32);
        }

        .form-group {
            margin-bottom: var(--space-24);
        }

        label {
            display: block;
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-medium);
            color: var(--color-text);
            margin-bottom: var(--space-8);
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: var(--space-12);
            font-size: var(--font-size-base);
            font-family: var(--font-family-base);
            color: var(--color-text);
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-base);
            transition: border-color var(--duration-normal) var(--ease-standard), box-shadow var(--duration-normal) var(--ease-standard);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: 2px solid var(--color-primary);
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px var(--color-focus-ring);
        }

        input::placeholder {
            color: var(--color-text-secondary);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: var(--space-24);
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-right: var(--space-8);
            cursor: pointer;
        }

        .checkbox-group label {
            margin: 0;
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-medium);
            cursor: pointer;
        }

        .btn {
            width: 100%;
            padding: var(--space-12) var(--space-16);
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-medium);
            font-family: var(--font-family-base);
            color: var(--color-btn-primary-text);
            background-color: var(--color-primary);
            border: none;
            border-radius: var(--radius-base);
            cursor: pointer;
            transition: background-color var(--duration-normal) var(--ease-standard);
        }

        .btn:hover {
            background-color: var(--color-primary-hover);
        }

        .btn:active {
            background-color: var(--color-primary-active);
        }

        .btn:focus-visible {
            outline: 2px solid var(--color-primary);
            box-shadow: 0 0 0 3px var(--color-focus-ring);
        }

        .links {
            margin-top: var(--space-24);
            text-align: center;
            font-size: var(--font-size-sm);
        }

        .links a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: var(--font-weight-medium);
            transition: color var(--duration-normal) var(--ease-standard);
        }

        .links a:hover {
            color: var(--color-primary-hover);
        }

        .error-message {
            display: none;
            padding: var(--space-12);
            margin-bottom: var(--space-16);
            background-color: rgba(192, 21, 47, 0.1);
            border: 1px solid rgba(192, 21, 47, 0.25);
            border-radius: var(--radius-base);
            color: rgba(192, 21, 47, 1);
            font-size: var(--font-size-sm);
        }

        .error-message.show {
            display: block;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: var(--space-24);
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Welcome Back</h1>
        <p class="subtitle">Sign in to your account</p>

        <?php if (!empty($error)): ?>
        <div class="error-message show">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Enter your username" 
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn">Sign In</button>
        </form>

        <div class="links">
            <a href="#" id="forgotPassword">Forgot password?</a>
            <span style="margin: 0 var(--space-8); color: var(--color-text-secondary);">•</span>
            <a href="register.php">Create an account</a>
        </div>
    </div>

    <script>
        const forgotPasswordLink = document.getElementById('forgotPassword');
        
        // Forgot password functionality
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Password reset functionality would be implemented here.');
            });
        }
    </script>
</body>
</html>
