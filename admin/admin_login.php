<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="adminstyle.css" />
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>
    <form action="login_process.php" method="POST">
        <!-- Username -->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <span id="togglePassword">👁️</span>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="form-group remember-me">
            <input type="checkbox" id="rememberMe" />
            <label for="rememberMe">Remember Me</label>
        </div>

        <!-- Login Button -->
        <button type="submit">Login</button>
    </form>
</div>

<script>
    // ===== Show/Hide Password =====
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            togglePassword.textContent = '🙈'; // Hide Icon
        } else {
            passwordField.type = 'password';
            togglePassword.textContent = '👁️'; // Show Icon
        }
    });

    // ===== Save to Local Storage =====
    const usernameField = document.getElementById('username');
    const rememberMe = document.getElementById('rememberMe');

    // 1️⃣ Load saved data from localStorage on page load
    window.addEventListener('load', () => {
        const savedUsername = localStorage.getItem('adminUsername');
        const savedPassword = localStorage.getItem('adminPassword');

        if (savedUsername && savedPassword) {
            usernameField.value = savedUsername;
            passwordField.value = savedPassword;
            rememberMe.checked = true;
        }
    });

    // 2️⃣ Save data to localStorage if "Remember Me" is checked
    document.querySelector('form').addEventListener('submit', () => {
        if (rememberMe.checked) {
            localStorage.setItem('adminUsername', usernameField.value);
            localStorage.setItem('adminPassword', passwordField.value);
        } else {
            localStorage.removeItem('adminUsername');
            localStorage.removeItem('adminPassword');
        }
    });
</script>

</body>
</html>
