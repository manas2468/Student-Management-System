<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <!-- Removed link to assets.css as it wasn't provided -->
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f5f7fb; /* Default body background (will be mostly covered by Spline) */
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden; /* Prevent scrollbars from Spline */
      position: relative; /* For z-index context */
    }

    /* --- Spline Integration Styles --- */
    .spline-viewer-wrapper {
      position: absolute; /* Position it absolutely to cover the background */
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0; /* Behind the login card */
      background: #000; /* Fallback background */
    }
    spline-viewer {
      width: 100%;
      height: 100%;
      display: block;
    }
    /* --- End Spline Integration Styles --- */

    .login-card {
      background: transparent; /* Making the card background fully transparent */
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      width: 350px;
      text-align: center;
      animation: fadeIn 0.8s ease;
      position: relative; /* To ensure it stays above the spline viewer */
      z-index: 1; /* Above the spline viewer */
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(40px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .brand {
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 20px;
      color: #fff; /* Changed to white */
      letter-spacing: 1px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.4); /* Add shadow for readability */
    }

    h2 {
      margin: 10px 0 20px;
      font-size: 22px;
      color: #fff; /* Changed to white */
      text-shadow: 1px 1px 3px rgba(0,0,0,0.4); /* Add shadow for readability */
    }

    .alert {
      background: #e6f7e9;
      color: #1a7f37; /* Keep success color for alert text */
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
      animation: popIn 0.5s ease;
    }
    @keyframes popIn {
      from {transform: scale(0.9); opacity: 0;}
      to {transform: scale(1); opacity: 1;}
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    label {
      text-align: left;
      font-weight: 600;
      font-size: 14px;
      color: #fff; /* Changed to white */
      text-shadow: 1px 1px 3px rgba(0,0,0,0.4); /* Add shadow for readability */
    }

    input {
      padding: 12px;
      border: 1px solid #ddd; /* Keep border light for contrast */
      border-radius: 8px;
      font-size: 14px;
      transition: border 0.2s;
      background: rgba(255, 255, 255, 0.8); /* Slightly transparent background for inputs */
      color: #333; /* Default text color for input */
    }
    input:focus {
      border-color: #00bcd4;
      outline: none;
    }

    .btn {
      background: #00bcd4;
      color: #fff; /* Button text is already white */
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,188,212,0.4);
    }

    .extra {
      margin-top: 15px;
      font-size: 13px;
      color: #fff; /* Changed to white */
      text-shadow: 1px 1px 3px rgba(0,0,0,0.4); /* Add shadow for readability */
    }
    .extra a {
      color: #00bcd4; /* Keep link color distinct */
      text-decoration: none;
      font-weight: 600;
    }
    .extra a:hover {
      text-decoration: underline;
    }

    /* Responsive Adjustments */
    @media (max-width: 480px) {
      .login-card {
        width: 90%; /* Take up more width on very small screens */
        padding: 30px;
      }
      .brand {
        font-size: 24px;
      }
      h2 {
        font-size: 20px;
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

  <div class="login-card">
    <div class="brand">MySite</div>
    <h2>Login</h2>

    <?php if(!empty($_GET['msg'])): ?>
      <div class="alert">🎉 Registered successfully — please login</div>
    <?php endif; ?>

    <form action="auth.php" method="POST" autocomplete="off">
      <label>Email</label>
      <input type="email" name="email" required />

      <label>Password</label>
      <input type="password" name="password" required />

      <button class="btn" type="submit">Login</button>
    </form>

    <div class="extra">
      Don’t have an account? <a href="register.php">Register</a>
    </div>
  </div>

  <!-- Include the Spline viewer script -->
  <script type="module" src="https://unpkg.com/@splinetool/viewer@1.10.64/build/spline-viewer.js"></script>

</body>
</html>