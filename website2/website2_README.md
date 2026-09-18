# Project Janus — Website 2: SQL Injection

Part of the **Project Janus** series: a set of intentionally vulnerable
websites built to teach students, in a hands-on way, how common web
security flaws look in real code — and how to fix them.

This site (Website 2) demonstrates **SQL Injection**, one of the most
well-known and historically damaging web vulnerabilities. It picks up
where Website 1 (Plain Text Passwords) left off: instead of *storing*
credentials insecurely, this app fails to *handle user input* securely
when checking those credentials against the database.

---

## Learning objectives

By the end of this module, students should be able to:

- Explain what SQL injection is and why it happens
- Identify vulnerable code that builds SQL queries by string concatenation
- Understand how an attacker can use crafted input to change a query's logic
- Log in **without knowing a valid password**, using a classic SQLi payload
- Explain the fix (parameterized queries / prepared statements) and why it works

---

## Project structure

| File | Purpose |
|---|---|
| `index.php` | Login form + the vulnerable authentication logic |
| `success.php` | Post-login landing page; echoes back the exact SQL query that ran, for teaching purposes |
| `logout.php` | Destroys the session and redirects to the login page |
| `config.php` | Database connection (reads credentials from environment variables, falls back to local defaults) |
| `docker-compose.yml` | Spins up the PHP web app + a MySQL database together |

---

## How it works

Website 1 taught students that passwords should never be stored in plain
text. Website 2 assumes the developer has moved past that mistake — but
introduces a *new* one that's arguably more dangerous: trusting user
input inside a database query.

The login handler builds its SQL query like this:

```php
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
```

Because `$username` and `$password` come straight from the submitted
form and are dropped directly into the query string, anything the user
types becomes part of the SQL the database executes — not just *data*
being compared, but potentially *code* being run.

A password field on the client side even hashes the password with
SHA-256 before submitting it — which might make a student assume the
app is "doing security right." It's a deliberate red herring: hashing
the password client-side does nothing to stop injection in the
`username` field, and it's a good discussion point about the difference
between *confidentiality in transit* and *injection safety*.

---

## Running it locally

This project ships with Docker so students don't need to configure PHP
or MySQL by hand.

```bash
docker compose up --build
```

This starts:
- **web** — the PHP app, available at `http://localhost:8080`
- **db** — a MySQL database seeded with a `users` table

> Default DB credentials (for local/dev use only — never do this in
> production) are set in `docker-compose.yml` and read by `config.php`
> via environment variables.

---

## Suggested classroom walkthrough

1. **Try logging in normally first.** Show that a correct username and
   password logs you in and lands on `success.php`, which — for teaching
   purposes — prints the exact SQL query that was just executed. This is
   the single most useful teaching hook in the app: students can *see*
   their input become part of the query.

2. **Ask: what happens if the input changes the meaning of the query,
   not just the data being searched for?** Let students experiment with
   the `username` field. A classic SQL comment sequence can be used to
   cut the rest of the query off early, effectively removing the
   password check from the logic entirely — without ever knowing a real
   password.

3. **Show the resulting query on `success.php`.** Have students compare
   the *intended* query (checking both username and password) with the
   *actual* query that ran. This is where the vulnerability "clicks" for
   most students — they can visually see the WHERE clause getting cut
   short.

4. **Discuss impact.** In a real application this same flaw could allow
   an attacker to:
   - Log in as any user without a password
   - Extract other users' data (`UNION SELECT` style attacks)
   - Modify or delete data
   - In severe/misconfigured cases, read files or execute commands on
     the server

5. **Discuss the fix.** The code comments in `index.php` already point
   to the correct approach — **prepared statements / parameterized
   queries** — where user input is passed as bound data, never
   concatenated into the query string:

   ```php
   $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
   $stmt->bind_param("ss", $username, $password);
   ```

   With this approach, no matter what a user types, it's treated strictly
   as a value being compared — never as part of the SQL syntax itself.
   Have students rewrite the vulnerable query themselves as an exercise.

6. **Bonus discussion:** password hashing. Even with SQLi fixed, this app
   still checks a hashed password against what should be a stored hash —
   tie this back to Website 1's lesson about *how* passwords should be
   stored (salted + hashed with a slow algorithm like bcrypt/argon2, not
   raw SHA-256) versus how they're checked.

---

## Blue team notes (defense-focused discussion)

- **Input validation isn't enough on its own** — allowlisting characters
  helps, but the real fix is *never building queries by concatenating
  untrusted input*.
- **Least privilege:** the database user the app connects as should only
  have the permissions it actually needs, so even a successful injection
  is limited in blast radius.
- **Logging & monitoring:** unusual query patterns (like unexpected
  quotes, `--`, `UNION`, `OR 1=1`) are things a WAF or logging pipeline
  can flag.
- **Defense in depth:** prepared statements are the primary fix, but
  error messages should also avoid leaking database structure, and
  accounts should have failed-login throttling.

---

## Safety / scope note

This application is **intentionally vulnerable** and meant to run
**locally, offline, in a container**, for educational use only. It
should never be deployed to a public server or used with real user data.