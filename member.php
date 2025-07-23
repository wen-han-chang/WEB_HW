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

$sql = "SELECT account, email, gender, color FROM members";
$result = $conn->query($sql);
$loggedInUser = $_SESSION["account"] ?? '訪客';
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>會員管理主介面</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">👥 會員管理</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="member.php">📋 會員列表</a></li>
          <li class="nav-item"><a class="nav-link" href="edit.php">📝 修改資料</a></li>
          <li class="nav-item"><a class="nav-link" href="upload.php">📁 檔案管理</a></li>
          <li class="nav-item"><a class="nav-link" href="board.php">💬 留言板</a></li>
          <li class="nav-item"><a class="nav-link" href="subscribe.php">🔔 訂閱</a></li>
        </ul>
        <div class="d-flex align-items-center">
          <span class="text-white me-3"><?= htmlspecialchars($loggedInUser); ?> · 您好！</span>
          <a href="start.html" class="btn btn-outline-danger">🚪 登出</a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container">
    <h2 class="text-center mb-4">👥 會員列表</h2>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>帳號</th>
          <th>Email</th>
          <th>性別</th>
          <th>喜好顏色</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row["account"]) ?></td>
          <td><?= htmlspecialchars($row["email"]) ?></td>
          <td><?= htmlspecialchars($row["gender"]) ?></td>
          <td>
            <span class="badge text-white px-3" style="background-color:<?= $row["color"] ?>;">
              <?= $row["color"] ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- 回主頁按鈕 -->
    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-outline-secondary" title="返回首頁">🔙 回主頁</a>
    </div>
  </div>
</body>
</html>