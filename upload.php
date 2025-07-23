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

$loggedInUser = $_SESSION["account"] ?? '訪客';
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <title>檔案管理介面</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">📁 檔案管理系統</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="member.php">📋 會員列表</a></li>
          <li class="nav-item"><a class="nav-link" href="edit.php">📝 修改資料</a></li>
          <li class="nav-item"><a class="nav-link active" href="upload.php">📁 檔案管理</a></li>
          <li class="nav-item"><a class="nav-link" href="board.php">💬 留言板</a></li>
          <li class="nav-item"><a class="nav-link" href="subscribe.php">🔔 訂閱</a></li>
        </ul>
        <div class="d-flex align-items-center">
          <span class="text-white me-3"><?php echo htmlspecialchars($loggedInUser); ?> · 您好！</span>
          <a href="start.html" class="btn btn-outline-danger">🚪 登出</a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <h2 class="mb-4 text-center">📤 檔案上傳區</h2>

    <div class="mb-3 d-flex align-items-center gap-3">
      <label for="fileInput" class="form-label mb-0 fw-bold">選擇檔案</label>
      <input type="file" class="form-control w-50" id="fileInput">
      <button class="btn btn-primary" onclick="uploadFile()">上傳</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>檔案名稱</th>
          <th>大小 (KB)</th>
          <th>上傳時間</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody id="fileTableBody">
        <!-- 動態新增列 -->
      </tbody>
    </table>
  </div>

  <script>
    function uploadFile() {
      const input = document.getElementById('fileInput');
      const file = input.files[0];
      if (file) {
        const table = document.getElementById('fileTableBody');
        const newRow = document.createElement('tr');
        const fileURL = URL.createObjectURL(file);

        newRow.innerHTML = `
          <td class="filename-cell">${file.name}</td>
          <td>${(file.size / 1024).toFixed(2)}</td>
          <td>${new Date().toLocaleString()}</td>
          <td>
            <button class="btn btn-sm btn-danger me-1" onclick="deleteRow(this)">刪除</button>
            <button class="btn btn-sm btn-warning me-1" onclick="renameFile(this)">改名</button>
            <a href="${fileURL}" download="${file.name}">
              <button class="btn btn-sm btn-success">下載</button>
            </a>
          </td>
        `;
        table.appendChild(newRow);
        input.value = '';
      }
    }

    function deleteRow(button) {
      const row = button.closest('tr');
      row.remove();
    }

    function renameFile(button) {
      const row = button.closest('tr');
      const filenameCell = row.querySelector('.filename-cell');
      const oldName = filenameCell.textContent;
      const newName = prompt("請輸入新檔名：", oldName);
      if (newName && newName.trim() !== "") {
        filenameCell.textContent = newName.trim();
        const downloadLink = row.querySelector('a');
        if (downloadLink) {
          downloadLink.setAttribute('download', newName.trim());
        }
      }
    }
  </script>
      <!-- 回主頁按鈕 -->
    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-outline-secondary" title="返回首頁">🔙 回主頁</a>
    </div>
</body>
</html>