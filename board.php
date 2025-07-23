<?php
session_start();
if (!isset($_SESSION["account"])) {
  header("Location: login.html");
  exit();
}

$account = $_SESSION["account"];
$conn = new mysqli("localhost", "willy", "123", "DATA");
if ($conn->connect_error) {
  die("連線失敗：" . $conn->connect_error);
}

// 取得會員ID
$stmt_user = $conn->prepare("SELECT id FROM members WHERE account = ?");
$stmt_user->bind_param("s", $account);
$stmt_user->execute();
$stmt_user->bind_result($member_id);
$stmt_user->fetch();
$stmt_user->close();

// 發佈留言
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = trim($_POST["title"] ?? '');
  $content = trim($_POST["content"] ?? '');

  if ($title && $content) {
    $stmt = $conn->prepare("INSERT INTO messages (member_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $member_id, $title, $content);
    $stmt->execute();
    $stmt->close();
    echo "<script>location.href='board.php';</script>";
    exit();
  }
}

// 撈留言資料
$sql = "
  SELECT m.id, m.title, m.content, m.created_at, u.account
  FROM messages m
  JOIN members u ON m.member_id = u.id
  ORDER BY m.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>留言板</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">💬 留言板</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="member.php">📋 會員列表</a></li>
          <li class="nav-item"><a class="nav-link" href="edit.php">📝 修改資料</a></li>
          <li class="nav-item"><a class="nav-link" href="upload.php">📁 檔案管理</a></li>
          <li class="nav-item"><a class="nav-link active" href="board.php">💬 留言板</a></li>
          <li class="nav-item"><a class="nav-link" href="subscribe.php">🔔 訂閱</a></li>
        </ul>
        <div class="d-flex align-items-center">
          <span class="text-white me-3"><?= htmlspecialchars($account) ?> · 您好！</span>
          <a href="start.html" class="btn btn-outline-danger">🚪 登出</a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container">
    <h4 class="mb-4 text-center">📝 發佈新留言</h4>
    <form method="post" class="bg-white p-4 rounded shadow-sm mb-5" style="max-width: 600px; margin: auto;">
      <div class="mb-3">
        <label class="form-label">標題</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">內容</label>
        <textarea name="content" rows="4" class="form-control" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary w-100">發佈留言</button>
    </form>

    <h4 class="mb-3 text-center">🗂 所有留言</h4>
    <?php if ($result && $result->num_rows > 0): ?>
      <div class="list-group">
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="list-group-item list-group-item-action mb-2 rounded shadow-sm">
            <h5 class="mb-1"><?= htmlspecialchars($row["title"]) ?></h5>
            <p class="mb-1"><?= nl2br(htmlspecialchars($row["content"])) ?></p>
            <small class="text-muted">
              🧑 作者：<?= htmlspecialchars($row["account"]) ?> ｜🕒 時間：<?= $row["created_at"] ?>
            </small>
            <div class="mt-2">
              <?php if ($row["account"] === $account): ?>
                <a href="msg/reply.php?msg=<?= $row["id"] ?>" class="btn btn-sm btn-outline-info">查看回覆</a>
                <a href="msg/editmsg.php?msg=<?= $row["id"] ?>" class="btn btn-sm btn-outline-warning">修改</a>
                <a href="msg/delmsg.php?msg=<?= $row["id"] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('確定刪除留言？')">刪除</a>
              <?php else: ?>
                <a href="msg/reply.php?msg=<?= $row["id"] ?>" class="btn btn-sm btn-outline-primary">回覆</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-muted">目前尚無留言。</p>
    <?php endif; ?>
  </div>
      <!-- 回主頁按鈕 -->
    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-outline-secondary" title="返回首頁">🔙 回主頁</a>
    </div>
</body>
</html>