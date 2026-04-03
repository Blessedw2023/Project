<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UniVote System - Landing Page</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #333;
        }

        /* ====== NAVBAR ====== */
        nav {
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav h1 {
            font-size: 26px;
            color: #6a11cb;
            font-weight: bold;
        }

        nav a {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            transition: 0.2s;
            font-weight: bold;
        }

        .btn-student {
            background: #6a11cb;
            color: white;
        }

        .btn-student:hover { background: #4e0fa1; }

        .btn-admin {
            background: #333;
            color: white;
        }

        .btn-admin:hover { background: #000; }

        .btn-register {
            border: 2px solid #6a11cb;
            color: #6a11cb;
            background: transparent;
        }

        .btn-register:hover { background: #f4f4f4; }

        /* ====== HERO SECTION ====== */
        .hero {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 70px);
            text-align: center;
            color: white;
            padding: 0 20px;
        }

        .hero h2 {
            font-size: 42px;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 40px;
            max-width: 600px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .hero .buttons a {
            margin: 0 10px;
            display: inline-block;
            min-width: 140px;
            text-align: center;
        }

        /* ====== CONTACT SECTION ====== */
        .contact {
            background: #fff;
            padding: 50px 20px;
            text-align: center;
            color: #333;
        }

        .contact h3 {
            font-size: 28px;
            color: #6a11cb;
            margin-bottom: 20px;
        }

        .contact p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .contact a {
            color: #6a11cb;
            text-decoration: none;
        }

        .contact a:hover { text-decoration: underline; }

        /* ====== FOOTER ====== */
        footer {
            text-align: center;
            padding: 15px;
            background: rgba(255,255,255,0.9);
            font-size: 14px;
            color: #333;
        }

        /* ====== RESPONSIVE ====== */
        @media(max-width: 768px){
            .hero h2 { font-size: 32px; }
            .hero p { font-size: 16px; }
            nav { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <h1>Student Leaders Voting System</h1>
    <div>
        <a href="login.php" class="btn-student">Student Login</a>
        <a href="admin/login.php" class="btn-admin">Admin Login</a>
        <a href="register.php" class="btn-register">Register</a>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <h2>Welcome to Student Leaders Voting System</h2>
    <p>
        Secure, transparent, and real-time student leaders online elections. 
        Students can vote for candidates, view results, and admins can manage elections easily.
    </p>

    <div class="buttons">
        <a href="login.php" class="btn-student">Student Login</a>
        <a href="admin/login.php" class="btn-admin">Admin Login</a>
        <a href="register.php" class="btn-register">Register</a>
    </div>
</section>

<<!-- CONTACT SECTION -->
<section class="contact">
    <h3>Contact Us</h3>
    <p>Have questions? Reach out to us:</p>
    <p>
        Email: 
        <a href="mailto:werenelly479@gmail.com">support@univote.com</a>
    </p>
    <p>
        WhatsApp: 
        <a href="https://wa.me/254114054646" target="_blank">+254 114054646</a>
    </p>
</section>


<!-- FOOTER -->
<footer>
    &copy; <?php echo date("Y"); ?> Student Leaders Voting System. All rights reserved.
</footer>

</body>
</html>
