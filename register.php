<?php
// --- IMPORTANT DEBUGGING LINES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END DEBUGGING LINES ---

require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register — MySite</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a1a; /* Dark background to match robot */
            height: 100vh;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            overflow: hidden;
            position: relative;
            padding-left: 50px;
            box-sizing: border-box;
        }

        /* --- Spline Integration Styles --- */
        .spline-viewer-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: #1a1a1a;
        }
        spline-viewer {
            width: 100%;
            height: 100%;
            display: block;
        }
        /* --- End Spline Integration Styles --- */

        /* --- Registration Card Styles --- */
        .register-card {
            background: transparent; /* Removed white background */
            padding: 0;              /* Removed padding */
            border-radius: 0;        /* Removed border radius */
            box-shadow: none;        /* Removed shadow */
            width: 370px;
            max-width: 90%;
            text-align: left;        /* Align text left */
            color: white;            /* Light text for contrast */
            z-index: 2;
            position: relative;
            flex-shrink: 0;
        }

        /* Optional: animate from right but no background */
        @keyframes slideInFromFarRight {
            from {
                opacity: 0;
                transform: translateX(200px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .register-card {
            animation: slideInFromFarRight 0.8s ease-out forwards;
        }

        .brand {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
            letter-spacing: 1px;
        }

        h2 {
            margin: 10px 0 25px;
            font-size: 22px;
            color: white;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            color: white;
        }

        input {
            padding: 12px;
            border: 1px solid #555;
            border-radius: 8px;
            font-size: 14px;
            background: #222;
            color: white;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input::placeholder {
            color: #aaa;
        }
        input:focus {
            border-color: #00bcd4;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,188,212,0.2);
            background: #333;
        }

        .btn {
            background: #00bcd4;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,188,212,0.4);
        }

        .extra {
            margin-top: 15px;
            font-size: 13px;
            color: #aaa;
        }
        .extra a {
            color: #00bcd4;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .extra a:hover {
            color: #0097a7;
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 420px) {
            body {
                padding-left: 0;
                justify-content: center;
            }
            .register-card {
                width: 90%;
                padding: 25px;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Spline Scene Integration -->
    <div class="spline-viewer-wrapper">
        <spline-viewer url="https://prod.spline.design/t4ww9Vf37irD-ESY/scene.splinecode"></spline-viewer>
    </div>
    <!-- End Spline Scene Integration -->

    <div class="register-card">
        <div class="brand">MySite</div>
        <h2>Create an account</h2>

        <form action="insert.php" method="POST" autocomplete="off">
            <label>Username</label>
            <input name="username" type="text" required minlength="3" maxlength="200" placeholder="Enter your username"/>

            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email"/>

            <label>Password</label>
            <input type="password" name="password" required minlength="6" placeholder="Enter your password"/>

            <button class="btn" type="submit">Register</button>
        </form>

        <div class="extra">
            Already registered? <a href="login.php">Login</a>
        </div>
    </div>

    <!-- Include the Spline viewer script -->
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.10.64/build/spline-viewer.js"></script>
</body>
</html>
