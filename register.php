<?php
session_start();
include "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Check if username already exists
        $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            $hashed_password = sha1($password);
            $role = "customer";

            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Electricity Management System</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
    }

    .container {
      display: flex;
      height: 100vh;
      width: 100%;
    }

    .left {
      flex: 1;
      background: linear-gradient(rgba(255,255,255,0.05), rgba(255,255,255,0.05)), url('login.jfif') no-repeat center center;
      background-size: cover;
    }

    .right {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: rgb(154, 168, 230);
    }

    .register-box {
      background: rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      backdrop-filter: blur(16px) saturate(180%);
      -webkit-backdrop-filter: blur(16px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.3);
      padding: 40px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      animation: fadeIn 1s ease forwards;
      color: #000;
    }

    @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 2rem;
      font-weight: 600;
      color: #007acc;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 14px;
      margin: 10px 0;
      border-radius: 12px;
      border: 1px solid #ddd;
      font-size: 1rem;
      background: rgba(255, 255, 255, 0.8);
      transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #00c0ff;
      box-shadow: 0 0 10px #00c0ff55;
      outline: none;
    }

    input[type="submit"] {
      width: 100%;
      padding: 14px;
      margin-top: 20px;
      border: none;
      border-radius: 50px;
      background: linear-gradient(90deg, #00c0ff, #007acc);
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    input[type="submit"]:hover {
      transform: scale(1.05);
      background: linear-gradient(90deg, #007acc, #004f8f);
    }

    .message {
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-weight: 500;
    }

    .error-msg {
      background: rgba(255, 0, 0, 0.1);
      color: red;
      border-left: 4px solid red;
    }

    .success-msg {
      background: rgba(0, 255, 0, 0.1);
      color: green;
      border-left: 4px solid green;
    }

    p {
      text-align: center;
      margin-top: 20px;
    }

    p a {
      color: #007acc;
      text-decoration: none;
    }

    p a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .left {
        height: 200px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left"></div>
    <div class="right">
      <div class="register-box">
        <h2>Register</h2>

        <?php if ($error): ?>
          <div class="message error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="message success-msg"><?= $success ?></div>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
          <input type="text" name="username" placeholder="Username" required />
          <input type="password" name="password" placeholder="Password" required />
          <input type="submit" value="Register" />
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </div>
  </div>
</body>
</html>
