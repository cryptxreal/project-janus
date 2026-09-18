# Project Janus — Website 1: Plain Text Passwords

Part of the **Project Janus** series: a set of intentionally vulnerable
websites built to teach students, in a hands-on way, how common web
security flaws look in real code — and how to fix them.

This site (Website 1) demonstrates what happens when an application
checks credentials against **plain text passwords** instead of hashed
ones, and — just as importantly — what happens when that check is done
entirely in **client-side JavaScript** rather than on a server.

---

## Learning objectives

By the end of this module, students should be able to:

- Explain why storing or comparing passwords in plain text is dangerous
- Understand the difference between client-side and server-side
  authentication, and why client-side checks can never be trusted
- Use browser developer tools to inspect page source and JavaScript
- Locate a hardcoded credential sitting in front-end code
- Explain how this maps to real-world incidents (leaked databases,
  hardcoded secrets in JS bundles, etc.)

---

## Project structure

| File | Purpose |
|---|---|
| `website1pt1.html` | The login page — form, styling, and the vulnerable client-side check |
| `website1pt2.html` | The "logged in" success page, reached only by passing the check |

Unlike Website 2, this part of the project is static HTML/JS — there's
no server or database involved. That's intentional: it isolates the
lesson to a single idea (plain text + client-side trust) without the
added complexity of a backend.

---

## How it works

`website1pt1.html` holds the username and password directly in the page's
JavaScript:

```javascript
const CORRECT_USER = "user1";
const CORRECT_PASS = "wVwTnTArfutHwjVxRPlUKW17ORROCp6pyo1TGyoic9b71UBkol";
```

When the form is submitted, `tryLogin()` compares what the user typed
against these two constants **in the browser**. If they match, the page
simply redirects to `website1pt2.html`:

```javascript
if (u === CORRECT_USER && p === CORRECT_PASS) {
  window.location.href = "website1pt2.html";
}
```

This is the core of the lesson: the "password check" never touches a
server. Everything needed to pass it — including the correct password,
in plain text — is already sitting in the HTML/JS delivered to the
browser before the user ever types anything.

---

## Running it locally

No server, database, or build step needed — these are plain static HTML
files.

- Open `website1pt1.html` directly in a browser, **or**
- Serve the folder with any simple static server, e.g.:

  ```bash
  python3 -m http.server 8000
  ```

  then visit `http://localhost:8000/website1pt1.html`

---

## Suggested classroom walkthrough

1. **Try the login form with a wrong password first.** Show the normal
   "Incorrect username or password" error, so it feels like a real login
   page.

2. **Open browser DevTools → view page source / the Sources tab.** Have
   students find the `<script>` block in `website1pt1.html` on their own.
   Let them discover `CORRECT_USER` and `CORRECT_PASS` sitting in plain
   text.

3. **Log in using the real credentials found in the source**, and land on
   `website1pt2.html`, which reveals the module's flag as confirmation.

4. **Discuss what just happened:** the "authentication" never left the
   browser. Anyone with access to the page's source — which is *everyone*,
   since it's delivered to every visitor — already has the password. No
   database breach, no cracking, no brute force required.

5. **Bridge to the bigger lesson:** this is a simplified stand-in for two
   related real-world problems:
   - **Plain text password storage** — if a real backend stored passwords
     this way and its database leaked, every account would be instantly
     compromised, since there's no hashing to slow an attacker down.
   - **Client-side trust** — any check performed in JavaScript can be
     read, modified, or bypassed entirely by the user (e.g. calling
     `tryLogin()` manually from the console, or just navigating straight
     to `website1pt2.html`). Real authorization decisions must always be
     enforced server-side.

6. **Have students test the bypass themselves:** navigating directly to
   `website1pt2.html` without ever submitting the form. Because there's
   no server-side session or check gating that page, it loads anyway —
   a good demonstration of "security through obscurity" failing.

---

## Blue team notes (defense-focused discussion)

- **Never trust the client.** Any check that decides *what a user is
  allowed to do or see* must be enforced on the server, not just the
  browser.
- **Never ship secrets in front-end code.** Anything in HTML/CSS/JS sent
  to the browser should be treated as public information.
- **Hash, don't store, passwords.** Use a slow, salted hashing algorithm
  (bcrypt, scrypt, or Argon2) so that even a full database leak doesn't
  hand over usable passwords.
- **Compare this to Website 2:** that module moves the check to a real
  server and database — which fixes *this* vulnerability, but introduces
  a new one (SQL injection) for students to find next.

---

## Safety / scope note

This application is **intentionally vulnerable** and meant to run
**locally, offline**, for educational use only. The hardcoded credentials
and lack of real authentication are deliberate teaching tools, not bugs
to silently "fix" — the discovery is the point.