<?php
session_start();
if (!isset($_SESSION["account"])) {
  header("Location: ../login.html");
  exit();
}

$account = $_SESSION["account"];
$conn = new mysqli("localhost", "willy", "123", "DATA");
if ($conn->connect_error) {
  die("連線失敗：" . $conn->connect_error);
}

$msgId = $_GET["msg"] ?? '';
if (!$msgId) {
  echo "留言編號缺失！";
  exit();
}

// 更新留言
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = $_POST["title"] ?? '';
  $content = $_POST["content"] ?? '';
  $stmt = $conn->prepare("UPDATE messages SET title=?, content=? WHERE id=?");
  $stmt->bind_param("ssi", $title, $content, $msgId);
  $stmt->execute();
  $stmt->close();
  echo "<script>location.href='../board.php';</script>";
  exit();
}

// 撈原始留言
$stmt = $conn->prepare("SELECT title, content FROM messages WHERE id=?");
$stmt->bind_param("i", $msgId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>✏️ 編輯留言</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

  <!-- 導覽列（可根據 board.php 調整） -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="../board.php">💬 留言板</a>
    </div>
  </nav>

  <!-- 編輯留言表單 -->
  <div class="container">
    <div class="bg-white p-4 rounded shadow-sm mx-auto" style="max-width: 600px;">
      <h4 class="text-center mb-4">✏️ 編輯留言 #<?= $msgId ?></h4>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">標題</label>
          <input type="text" name="title" value="<?= htmlspecialchars($data["title"]) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">內容</label>
          <textarea name="content" rows="4" class="form-control" required><?= htmlspecialchars($data["content"]) ?></textarea>
        </div>
        <button type="submit" class="btn btn-warning w-100">💾 儲存修改</button>
      </form>
    </div>
  </div>

</body>
</html>