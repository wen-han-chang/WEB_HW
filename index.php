<?php
session_start();
$account = $_SESSION["account"] ?? "訪客";
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>會員系統入口</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #fdfdfd, #eef2f3);
      color: #333;
    }

    header {
      background-color: #2c3e50;
      color: #fff;
      padding: 16px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .logo {
      font-size: 1.6em;
      font-weight: bold;
      color: #f1c40f;
    }

    .welcome {
      font-weight: bold;
      color: #fff;
      font-size: 1em;
    }

    .marquee {
      background-color: #2980b9;
      color: white;
      font-size: 1em;
      padding: 10px 0;
      overflow: hidden;
      white-space: nowrap;
    }

    .marquee span {
      display: inline-block;
      animation: scrollText 12s linear infinite;
      padding-left: 100%;
    }

    @keyframes scrollText {
      0% { transform: translateX(0); }
      100% { transform: translateX(-100%); }
    }

    .hero {
      height: 90vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background-size: cover;
      background-position: center;
      color: white;
      position: relative;
      transition: background-image 0.5s ease-in-out;
    }

    .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(44, 62, 80, 0.65);
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 600px;
      padding: 20px;
    }

    .hero h1 {
      font-size: 3em;
      margin-bottom: 16px;
    }

    .hero p {
      font-size: 1.2em;
    }

    .btn-group {
      margin-top: 30px;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .btn-group a {
      text-decoration: none;
      padding: 12px 24px;
      border-radius: 8px;
      background-color: #27ae60;
      color: white;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .btn-group a:hover {
      background-color: #1e8449;
    }

    footer {
      text-align: center;
      padding: 20px;
      background-color: #ecf0f1;
      color: #666;
      font-size: 0.9em;
    }

    @media screen and (max-width: 768px) {
      .hero h1 { font-size: 2em; }
      .btn-group a { width: 80%; }
    }
  </style>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const hero = document.querySelector(".hero");
      const bgList = [
        "https://images.unsplash.com/photo-1605379399642-d125104f0d5b",
        "https://images.unsplash.com/photo-1581091870633-1e7b2155d2d3",
        "https://images.unsplash.com/photo-1616401786655-976ea7b69a0c",
        "https://images.unsplash.com/photo-1506744038136-46273834b3fb",
        "https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0",
        "https://images.unsplash.com/photo-1561484930-b4b6a95a3be3"
      ];
      const random = Math.floor(Math.random() * bgList.length);
      hero.style.backgroundImage = `url("${bgList[random]}?auto=format&fit=crop&h=900&q=80")`;
    });
  </script>
</head>
<body>

  <header>
    <div class="logo">🌟 HCI 會員系統</div>
    <div class="welcome">😄 <?= htmlspecialchars($account) ?> · 歡迎光臨！</div>
  </header>

  <div class="marquee">
    <span>📢 歡迎加入 HCI 系統｜請先登入或註冊帳號｜留言板與會員功能全面開放 🔧</span>
  </div>

  <div class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <h1>探索你的會員系統</h1>
      <p>以下是你的入口功能，可快速進入各項操作：</p>
      <div class="btn-group">
        <?php if ($account === "訪客"): ?>
          <a href="login.php">🔐 登入</a>
          <a href="register.php">🆕 註冊</a>
        <?php else: ?>
          <a href="member.php">📋 會員列表</a>
          <a href="edit.php">📝 修改資料</a>
          <a href="upload.php">📁 檔案管理</a>
          <a href="board.php">💬 留言板</a>
          <a href="subscribe.php">🔔 訂閱</a>
          <a href="start.html">🚪 登出</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <footer>
    ⓒ 2025 HCI 系統 | Powered by PHP + MySQL
  </footer>

</body>
</html>