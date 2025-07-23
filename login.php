<?php
session_start();

$servername = "localhost";
$username = "willy";
$password = "123";
$dbname = "DATA";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("連線失敗：" . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $account = $_POST["account"] ?? '';
  $pwd = $_POST["password"] ?? '';

  $stmt = $conn->prepare("SELECT * FROM members WHERE account = ? AND password = ?");
  $stmt->bind_param("ss", $account, $pwd);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION["account"] = $row["account"];
    header("Location: index.php");
    exit();
  } else {
    $error = "登入失敗：帳號或密碼錯誤";
  }

  $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>會員登入</title>
  <style>
    body {
      background: #111;
      color: #eee;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-box {
      background: #222;
      padding: 30px;
      border-radius: 12px;
      width: 360px;
      box-shadow: 0 0 12px #333;
    }
    input {
      width: 100%;
      padding: 8px;
      border-radius: 6px;
      border: none;
      background: #333;
      color: #fff;
      margin-bottom: 12px;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #2196f3;
      border: none;
      border-radius: 6px;
      color: white;
      font-size: 1em;
      cursor: pointer;
    }
    .error {
      color: #f44336;
      text-align: center;
      margin-bottom: 12px;
    }
  </style>
</head>
<body>
  <form method="post" class="form-box">
    <h2>會員登入</h2>
    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <label>帳號：</label>
    <input type="text" name="account" required>
    <label>密碼：</label>
    <input type="password" name="password" required>
    <button type="submit">登入</button>
  </form>
</body>
</html>