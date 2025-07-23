<?php
$servername = "localhost";
$username = "willy";
$password = "123";
$dbname = "DATA";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("連線失敗：" . $conn->connect_error);
}

$msg = "";
$redirect = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $account  = $_POST["account"] ?? '';
  $pwd      = $_POST["password"] ?? '';
  $email    = $_POST["email"] ?? '';
  $gender   = $_POST["gender"] ?? '';
  $color    = $_POST["color"] ?? '';

  $stmt_check = $conn->prepare("SELECT id FROM members WHERE account = ?");
  $stmt_check->bind_param("s", $account);
  $stmt_check->execute();
  $stmt_check->store_result();

  if ($stmt_check->num_rows > 0) {
    $msg = "⚠️ 帳號已存在，請使用其他帳號";
  } else {
    $stmt = $conn->prepare("INSERT INTO members (account, password, email, gender, color) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $account, $pwd, $email, $gender, $color);
    $stmt->execute();
    $stmt->close();
    $msg = "✅ 註冊成功！即將前往登入畫面...";
    $redirect = true;
  }
  $stmt_check->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>會員註冊</title>
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
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin: 10px 0 4px;
    }
    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="color"] {
      width: 100%;
      padding: 8px;
      border-radius: 6px;
      border: none;
      background: #333;
      color: #fff;
    }
    .gender-group {
      display: flex;
      gap: 10px;
      margin-top: 8px;
    }
    button {
      margin-top: 16px;
      width: 100%;
      padding: 10px;
      font-size: 1em;
      background: #2196f3;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      color: #fff;
    }
    button:hover {
      background: #1976d2;
    }
    .msg {
      text-align: center;
      margin-bottom: 12px;
      color: #ff9800;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <form method="post" class="form-box">
    <h2>會員註冊</h2>
    <?php if ($msg): ?>
      <div class="msg"><?= $msg ?></div>
      <?php if ($redirect): ?>
        <script>
          setTimeout(() => {
            window.location.href = "start.html";
          }, 1000); // 1 秒後自動跳轉
        </script>
      <?php endif; ?>
    <?php endif; ?>

    <label for="account">帳號：</label>
    <input type="text" id="account" name="account" required>

    <label for="password">密碼：</label>
    <input type="password" id="password" name="password" required>

    <label for="email">電子郵件：</label>
    <input type="email" id="email" name="email" required>

    <label>性別：</label>
    <div class="gender-group">
      <label><input type="radio" name="gender" value="male" required> 男</label>
      <label><input type="radio" name="gender" value="female"> 女</label>
      <label><input type="radio" name="gender" value="other"> 其他</label>
    </div>

    <label for="color">喜好顏色：</label>
    <input type="color" id="color" name="color" value="#000000">

    <button type="submit">確認註冊</button>
  </form>

</body>
</html>