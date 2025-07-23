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

// 取得 member_id
$stmt_user = $conn->prepare("SELECT id FROM members WHERE account = ?");
$stmt_user->bind_param("s", $account);
$stmt_user->execute();
$stmt_user->bind_result($member_id);
$stmt_user->fetch();
$stmt_user->close();

// 撈主留言及作者資訊
$stmt_msg = $conn->prepare("
  SELECT m.title, m.content, m.created_at, u.account, m.member_id
  FROM messages m
  JOIN members u ON m.member_id = u.id
  WHERE m.id = ?
");
$stmt_msg->bind_param("i", $msgId);
$stmt_msg->execute();
$result_msg = $stmt_msg->get_result();
$main = $result_msg->fetch_assoc();
$stmt_msg->close();

// 撈回覆清單
$stmt_replies = $conn->prepare("
  SELECT r.content, r.created_at, m.account
  FROM replies r
  JOIN members m ON r.member_id = m.id
  WHERE r.message_id = ?
  ORDER BY r.created_at ASC
");
$stmt_replies->bind_param("i", $msgId);
$stmt_replies->execute();
$reply_results = $stmt_replies->get_result();

// 處理回覆提交（只有非作者可以）
if ($_SERVER["REQUEST_METHOD"] === "POST" && $member_id !== intval($main["member_id"])) {
  $reply = trim($_POST["reply"] ?? '');
  if ($reply) {
    $stmt = $conn->prepare("INSERT INTO replies (message_id, member_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $msgId, $member_id, $reply);
    $stmt->execute();
    $stmt->close();
    echo "<script>location.href='reply.php?msg={$msgId}';</script>";
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>留言回覆</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h4 class="mb-4 text-center">💬 主留言回覆區</h4>

    <!-- 主留言區 -->
    <div class="bg-white p-4 rounded shadow-sm mb-4">
      <h5><?= htmlspecialchars($main["title"]) ?></h5>
      <p><?= nl2br(htmlspecialchars($main["content"])) ?></p>
      <small class="text-muted">🧑 作者：<?= htmlspecialchars($main["account"]) ?> ｜🕒 <?= $main["created_at"] ?></small>
    </div>

    <!-- 回覆列表 -->
    <h5 class="mb-3">🗨 回覆內容：</h5>
    <?php if ($reply_results->num_rows > 0): ?>
      <ul class="list-group mb-4">
        <?php while ($r = $reply_results->fetch_assoc()): ?>
          <li class="list-group-item">
            <?= nl2br(htmlspecialchars($r["content"])) ?><br>
            <small class="text-muted">🔁 <?= htmlspecialchars($r["account"]) ?> ｜🕒 <?= $r["created_at"] ?></small>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p class="text-muted">目前尚無回覆。</p>
    <?php endif; ?>

    <!-- 回覆表單（只有非作者能看到） -->
    <?php if ($member_id !== intval($main["member_id"])): ?>
      <form method="post" class="bg-white p-4 rounded shadow-sm mb-3" style="max-width: 600px; margin: auto;">
        <h5 class="mb-3 text-center">✍️ 發送回覆</h5>
        <div class="mb-3">
          <textarea name="reply" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">送出回覆</button>
      </form>
    <?php else: ?>
      <div class="alert alert-info text-center">👀 您是作者，只能查看回覆。</div>
    <?php endif; ?>

    <!-- 返回按鈕 -->
    <div class="text-center mt-4">
      <a href="../board.php" class="btn btn-outline-secondary">🔙 回留言板</a>
    </div>
  </div>
</body>
</html>