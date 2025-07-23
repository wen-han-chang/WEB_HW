<?php
session_start();
$conn = new mysqli("localhost", "willy", "123", "DATA");

$msgId = $_GET["msg"] ?? '';
if (!$msgId) {
  echo "<div class='container mt-4'><div class='alert alert-danger'>⚠️ 未提供留言編號</div></div>";
  exit();
}

// 刪除留言
$stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
$stmt->bind_param("i", $msgId);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>刪除留言</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    // 1.5 秒後自動導回留言板
    setTimeout(() => {
      window.location.href = "../board.php";
    }, 900);
  </script>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="alert alert-success text-center shadow-sm">
      ✅ 留言已成功刪除！正在返回留言板...
    </div>
  </div>
</body>
</html>