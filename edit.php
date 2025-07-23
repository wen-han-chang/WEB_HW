<?php
session_start();
if (!isset($_SESSION["account"])) {
  header("Location: login.html");
  exit();
}

$account = $_SESSION["account"];
$servername = "localhost";
$username = "willy";
$password = "123";
$dbname = "DATA";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("連線失敗：" . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST["email"] ?? '';
  $gender = $_POST["gender"] ?? '';
  $color = $_POST["color"] ?? '';

  $stmt = $conn->prepare("UPDATE members SET email = ?, gender = ?, color = ? WHERE account = ?");
  $stmt->bind_param("ssss", $email, $gender, $color, $account);
  $stmt->execute();
  $stmt->close();

  echo "<div class='container'>
          <div id='successAlert' class='alert alert-success mt-4'>✅ 資料已成功更新！</div>
        </div>
        <script>
          setTimeout(function() {
            const alertBox = document.getElementById('successAlert');
            if (alertBox) alertBox.style.display = 'none';
          }, 1000); // 1秒後自動隱藏
        </script>";}

// 讀取原始資料
$stmt = $conn->prepare("SELECT email, gender, color FROM members WHERE account = ?");
$stmt->bind_param("s", $account);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>修改資料</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">🛠 修改資料</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="member.php">📋 會員列表</a></li>
          <li class="nav-item"><a class="nav-link active" href="edit.php">📝 修改資料</a></li>
          <li class="nav-item"><a class="nav-link" href="upload.php">📁 檔案管理</a></li>
          <li class="nav-item"><a class="nav-link" href="board.php">💬 留言板</a></li>
          <li class="nav-item"><a class="nav-link" href="subscribe.php">🔔 訂閱</a></li>
        </ul>
        <div class="d-flex align-items-center">
          <span class="text-white me-3"><?php echo htmlspecialchars($account); ?> · 您好！</span>
          <a href="start.html" class="btn btn-outline-danger">🚪 登出</a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 500px;">
    <h4 class="mb-3 text-center">🔧 修改您的會員資料</h4>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">帳號：</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($account) ?>" disabled>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">電子郵件：</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($data["email"]) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">性別：</label>
        <select name="gender" class="form-select">
          <option value="female" <?= $data["gender"] == "female" ? "selected" : "" ?>>女性</option>
          <option value="male" <?= $data["gender"] == "male" ? "selected" : "" ?>>男性</option>
          <option value="other" <?= $data["gender"] == "other" ? "selected" : "" ?>>其他</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="color" class="form-label">喜好顏色：</label>
        <input type="color" class="form-control form-control-color" name="color" value="<?= htmlspecialchars($data["color"]) ?>">
      </div>

      <button type="submit" class="btn btn-primary w-100">儲存修改</button>
    </form>
  </div>
      <!-- 回主頁按鈕 -->
    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-outline-secondary" title="返回首頁">🔙 回主頁</a>
    </div>
</body>
</html>