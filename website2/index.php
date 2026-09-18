<?php
session_start();
require "config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // --- THIS IS THE VULNERABILITY ---
    // User input is concatenated directly into the SQL string instead of
    // being passed as a bound parameter. A real app should ALWAYS use a
    // prepared statement, e.g.:
    //   $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    //   $stmt->bind_param("ss", $username, $password);
    // Here we deliberately skip that so students can see how an attacker
    // can reshape the query. Try username:  admin' -- 
    // (anything in the password field, it gets commented out)
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $row['username'];
        $_SESSION['last_query'] = $sql; // shown on success page for teaching purposes
        header("Location: success.php");
        exit;
    } else {
        $error = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Login Page</title>
  <style>
    :root {
      --bg: #0B0E14;
      --card: #131820;
      --border: #2A3241;
      --text: #E7EAEE;
      --text-muted: #8A93A3;
      --input-bg: #1B222D;
      --button-bg: #1B222D;
      --button-border: #2A3241;
      --button-hover: #232B38;
      --error: #E5484D;
    }
    [data-theme="light"] {
      --bg: #f3f4f6;
      --card: #ffffff;
      --border: #e5e7eb;
      --text: #111827;
      --text-muted: #6b7280;
      --input-bg: #ffffff;
      --button-bg: #ffffff;
      --button-border: #d1d5db;
      --button-hover: #f9fafb;
      --error: #dc2626;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; background: var(--bg); color: var(--text); min-height: 100vh;
           display: flex; align-items: center; justify-content: center;
           transition: background 0.2s ease, color 0.2s ease; }
    .card { background: var(--card); border: 1px solid var(--border); border-radius: 12px;
            padding: 2rem; width: 100%; max-width: 360px; }
    h1 { font-size: 20px; margin-bottom: 1.5rem; }
    label { font-size: 13px; color: var(--text-muted); display: block; margin-bottom: 6px; }
    input { width: 100%; padding: 8px 12px; border: 1px solid var(--border); background: var(--input-bg);
            color: var(--text); border-radius: 8px; font-size: 15px; margin-bottom: 1rem; }
    button { width: 100%; padding: 9px; background: var(--button-bg); border: 1px solid var(--button-border);
             color: var(--text); border-radius: 8px; font-size: 15px; cursor: pointer; }
    button:hover { background: var(--button-hover); }
    .error { font-size: 13px; color: var(--error); margin-bottom: 1rem; min-height: 18px; }
    .theme-toggle { position: fixed; bottom: 1rem; right: 1rem; background: var(--card);
                    border: 1px solid var(--border); color: var(--text); border-radius: 999px;
                    padding: 6px 12px; font-size: 13px; cursor: pointer; }
  </style>
</head>
<body>
  <button class="theme-toggle" onclick="toggleTheme()" id="themeToggle"> Dark</button>

  <div id="login-view">
    <div class="card">
      <h1>Sign in</h1>
      <form method="POST" action="index.php">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" />
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" />
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <button type="submit">Sign in</button>
      </form>
    </div>
  </div>

  <script>
    let theme = "dark";
    function toggleTheme() {
      theme = theme === "dark" ? "light" : "dark";
      document.documentElement.setAttribute("data-theme", theme);
      document.getElementById("themeToggle").textContent = theme === "dark" ? "Dark" : "Light";
    }

    async function sha256(text) {
      const buf = await crypto.subtle.digest("SHA-256", new TextEncoder().encode(text));
      return [...new Uint8Array(buf)].map(b => b.toString(16).padStart(2, "0")).join("");
    }
    
    document.documentElement.setAttribute("data-theme", theme);

    document.querySelector("form").addEventListener("submit", async (e) => {
      e.preventDefault();
      const pwField = document.querySelector("input[name='password']");
      pwField.value = await sha256(pwField.value);
      e.target.submit();
    });
  </script>
</body>
</html>