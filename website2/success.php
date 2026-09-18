<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>logged in</title>
  <style>
    :root {
      --bg: #0B0E14;
      --card: #131820;
      --border: #2A3241;
      --text: #E7EAEE;
      --text-muted: #8A93A3;
      --badge-bg: #16321F;
      --badge-text: #3FB27F;
    }
    [data-theme="light"] {
      --bg: #f3f4f6;
      --card: #ffffff;
      --border: #e5e7eb;
      --text: #111827;
      --text-muted: #6b7280;
      --badge-bg: #dcfce7;
      --badge-text: #16a34a;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; background: var(--bg); color: var(--text); min-height: 100vh;
           display: flex; align-items: center; justify-content: center;
           transition: background 0.2s ease, color 0.2s ease; }
    .success-card { background: var(--card); border: 1px solid var(--border); border-radius: 12px;
                    padding: 2.5rem; text-align: center; max-width: 420px; }
    .badge { background: var(--badge-bg); color: var(--badge-text); font-size: 13px;
             padding: 4px 14px; border-radius: 8px; display: inline-block; margin-bottom: 1.25rem; }
    a.back-link { display: inline-block; margin-top: 0.5rem; font-size: 13px; color: var(--text-muted); }
    .theme-toggle { position: fixed; bottom: 1rem; right: 1rem; background: var(--card);
                    border: 1px solid var(--border); color: var(--text); border-radius: 999px;
                    padding: 6px 12px; font-size: 13px; cursor: pointer; }
    code { display: block; background: var(--input-bg, #1B222D); border: 1px solid var(--border);
           border-radius: 8px; padding: 10px; font-size: 12px; text-align: left; margin-top: 1rem;
           color: var(--text-muted); word-break: break-all; }
  </style>
</head>
<body>
  <button class="theme-toggle" onclick="toggleTheme()" id="themeToggle"> Dark</button>

  <div id="success-view">
    <div class="success-card">
      <div class="badge">Flag cryptX{sql_1nj3ct10n_101}</div>
      <h2 style="font-size:22px; margin-bottom:0.5rem;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
      <p style="color:var(--text-muted); margin-bottom:1.5rem;">You gave the right password... or did you?</p>
      <code><?php echo htmlspecialchars($_SESSION['last_query'] ?? ''); ?></code>
      <p style="font-size:12px; color:var(--text-muted); margin-top:0.75rem;">That's the exact SQL query your login just ran. Notice anything you typed inside it?</p>
      <a class="back-link" href="logout.php">&larr; Sign out</a>
    </div>
  </div>

  <script>
    let theme = "dark";
    function toggleTheme() {
      theme = theme === "dark" ? "light" : "dark";
      document.documentElement.setAttribute("data-theme", theme);
      document.getElementById("themeToggle").textContent = theme === "dark" ? "Dark" : "Light";
    }
    document.documentElement.setAttribute("data-theme", theme);
  </script>
</body>
</html>