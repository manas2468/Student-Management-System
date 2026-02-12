<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Activity Manager</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6fa; /* Default body background, mostly hidden by Spline */
            height: 100vh;
            display: flex; /* Use flex for header centering */
            flex-direction: column; /* Stack header, then content/spline */
            justify-content: flex-start; /* Align items to the top */
            align-items: center; /* Center horizontally */
            overflow-x: hidden; /* Prevent horizontal scrollbar */
            position: relative; /* For z-index context */
            min-height: 100vh; /* Ensure body takes full viewport height */
        }

        header {
            background: #111;
            color: #fff;
            padding: 20px 40px;
            width: 100%; /* Header spans full width */
            box-sizing: border-box; /* Include padding in width */
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            z-index: 10; /* Header needs to be the highest */
            position: relative; /* Needed for z-index to work */
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        nav a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
        }
        nav a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 0;
            height: 2px;
            background: #00bcd4;
            transition: width 0.3s ease;
        }
        nav a:hover {
            color: #00bcd4;
        }
        nav a:hover::after {
            width: 100%;
        }

        /* --- Spline Scene Container --- */
        /* This will act as the background element */
        .spline-background {
            position: fixed; /* Fixed position to stay in view */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0; /* At the very bottom */
            overflow: hidden; /* Hide anything spilling out */
        }
        spline-viewer { /* Styling for the custom element */
            width: 100%;
            height: 100%;
            display: block;
        }

        /* --- Content Container (Transparent Background) --- */
        /* This div holds the main content and is positioned OVER the Spline */
        .content-overlay {
            position: relative; /* For z-index context and positioning */
            z-index: 5; /* Clearly above the Spline viewer */
            width: 80%;
            margin: 40px auto; /* Standard centering margin */
            text-align: center; /* Center text content */
            animation: fadeInUp 1s ease forwards;
            /* No background, padding, border-radius, box-shadow here */
        }

        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* Styles for the content within the transparent container */
        .content-overlay h1 {
            margin-top: 0;
            font-size: 28px;
            color: #222; /* Default text color, adjust if needed for Spline background */
            text-shadow: 1px 1px 3px rgba(0,0,0,0.4); /* Subtle shadow */
        }

        .content-overlay p.small {
            font-size: 16px;
            color: #555; /* Slightly different color for subtitle */
            text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
        }

        .content-overlay ul {
            padding-left: 20px; /* Maintain list indentation */
            display: inline-block; /* Allows text-align: center to work for block elements */
            text-align: left; /* Re-align list items to the left within the centered block */
            color: #333; /* Default text color for list items */
        }

        /* Centering the grid layout */
        .content-overlay .grid {
            display: grid;
            grid-template-columns: 1fr; /* Stack items on small screens by default */
            gap: 20px;
            max-width: 600px; /* Limit width for better readability when centered */
            margin: 20px auto; /* Center the grid container itself */
            text-align: left; /* Align content inside grid items to the left */
        }

        /* Styles for the button itself */
        .content-overlay .btn {
            display: inline-block; /* Correct display for centering */
            padding: 12px 20px;
            border-radius: 8px;
            background: #00bcd4;
            color: #fff; /* Button text color */
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,188,212,0.3);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none; /* Remove default button border */
            cursor: pointer; /* Ensure it looks clickable */
            text-align: center; /* Ensure button text is centered */
        }
        .content-overlay .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 14px rgba(0,188,212,0.5);
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #777; /* Keep footer text color as is */
            font-size: 14px;
            position: relative; /* For z-index context */
            z-index: 5; /* Ensure footer is above the spline viewer */
            width: 100%; /* Footer spans full width */
        }

        /* Responsive adjustments for layout */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 15px;
            }
            .brand {
                margin-bottom: 10px;
            }
            nav {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            nav a {
                margin: 5px 10px;
            }
            .spline-viewer-wrapper {
                height: 400px; /* Smaller height on mobile */
            }
            .content-overlay { /* Adjust content container for mobile */
                width: 90%;
                margin-top: 20px; /* Adjust margin for mobile */
            }
            /* Adjust grid for mobile if needed */
            .content-overlay .grid {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">Student Activity Manager</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
            <a href="contact.php">Contact</a>
            <a href="users.php">Users</a>
        </nav>
    </header>

    <!-- Content appears ABOVE the Spline scene -->
    <!-- This container is now transparent and positioned over the Spline -->
    <div class="content-overlay">
        <h1>Welcome</h1>
        <p class="small">Work in progress 🚀</p>
        <div class="grid"> <!-- Grid layout for features and quick links -->
            <div>
                <h3>Features</h3>
                <ul>
                    <li>Register and Login (secure password hashing)</li>
                    <li>View registered users</li>
                    <li>Contact form stored to database</li>
                </ul>
            </div>

            <aside>
                <h4>Quick links</h4>
                <!-- The button is now centered by the parent .content-overlay and .grid styles -->
                <a class="btn" href="register.php">Create account</a>
            </aside>
        </div>
    </div>

    <!-- Spline Scene Integration is LAST in the DOM and has the lowest z-index -->
    <div class="spline-viewer-wrapper">
        <spline-viewer url="https://prod.spline.design/jtS7bYD-aNlsmiwo/scene.splinecode"></spline-viewer>
    </div>

    <footer>© <?php echo date("Y"); ?> MySite — All rights reserved</footer>

    <!-- Include the Spline viewer script -->
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.10.64/build/spline-viewer.js"></script>

</body>
</html>