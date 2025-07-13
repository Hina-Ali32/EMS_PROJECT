<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Electricity Management System - Welcome</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet"/>
  <style>
    /* Reset and base */
    * {
      box-sizing: border-box;
    }
    body, html {
      margin: 0;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      overflow-x: hidden;
    }
    /* Background */
    .bg-container {
      position: relative;
      height: 100vh;
      background: url('background.jpg') center center/cover no-repeat fixed;
    }
    /* Gradient overlay for darkening */
    .bg-container::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(0, 136, 204, 0.75), rgba(0, 51, 102, 0.85));
      z-index: 1;
    }
    /* Main content */
    .welcome-content {
      position: relative;
      z-index: 10;
      max-width: 900px;
      margin: 0 auto;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 0 20px;
      text-align: center;
    }
    .welcome-content h1 {
      font-weight: 600;
      font-size: 3.5rem;
      margin-bottom: 15px;
      text-shadow: 0 2px 6px rgba(0, 0, 0, 0.7);
    }
    .welcome-content p {
      font-weight: 300;
      font-size: 1.3rem;
      margin-bottom: 30px;
      line-height: 1.6;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
      text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
    }
    /* Button */
    .btn-login {
      padding: 14px 40px;
      font-size: 1.25rem;
      font-weight: 600;
      border: none;
      border-radius: 30px;
      background: #00aaff;
      color: #fff;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(0, 170, 255, 0.6);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
    }
    .btn-login:hover {
      background: #007bbf;
      box-shadow: 0 8px 20px rgba(0, 123, 191, 0.8);
    }
    /* About and Contact sections */
    .info-sections {
      margin-top: 50px;
      display: flex;
      justify-content: center;
      gap: 80px;
      flex-wrap: wrap;
    }
    .info-block {
      max-width: 320px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 15px;
      padding: 25px 30px;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(8px);
    }
    .info-block h3 {
      margin-bottom: 15px;
      font-weight: 600;
      font-size: 1.5rem;
      border-bottom: 2px solid #00aaff;
      padding-bottom: 6px;
    }
    .info-block p {
      font-weight: 300;
      font-size: 1rem;
      line-height: 1.5;
      color: #e0e0e0;
    }
    /* Footer */
    footer {
      position: absolute;
      bottom: 20px;
      width: 100%;
      text-align: center;
      font-weight: 300;
      color: #ccc;
      font-size: 0.9rem;
      user-select: none;
      z-index: 10;
    }

    /* Responsive */
    @media (max-width: 720px) {
      .welcome-content h1 {
        font-size: 2.5rem;
      }
      .info-sections {
        flex-direction: column;
        gap: 30px;
      }
      .info-block {
        max-width: 90%;
        margin: 0 auto;
      }
    }
  </style>
</head>
<body>
  <div class="bg-container">
    <div class="welcome-content">
      <h1>⚡ Electricity Management System</h1>
      <p>
        Streamline electricity billing, customer records, payments, and complaints with our powerful management platform.
      </p>
     <div style="margin-top: 20px;">
  <button class="btn-login" onclick="window.location.href='login.php'" style="margin-right: 15px;">Login</button>
  <button class="btn-login" onclick="window.location.href='register.php'" style="background-color: #00cc66; box-shadow: 0 6px 15px rgba(0, 204, 102, 0.6);">Register</button>
</div>

      <div class="info-sections" aria-label="About and Contact Sections">
        <section class="info-block" aria-labelledby="about-title">
          <h3 id="about-title">About Us</h3>
          <p>We deliver efficient and smart solutions to simplify electricity management for homes and businesses.</p>
        </section>

        <section class="info-block" aria-labelledby="contact-title">
          <h3 id="contact-title">Contact Us</h3>
          <p>Email: support@electricityms.com<br/>Phone: +123 456 7890</p>
        </section>
      </div>
    </div>
    <footer>&copy; 2025 Electricity Management System</footer>
  </div>
</body>
</html>
