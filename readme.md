# 會員系統與訂閱功能網站

本專案使用 PHP 搭配 MySQL 資料庫，並整合 Bootstrap 風格，提供以下模組功能：

- 會員登入、修改個人資料
- 檔案上傳與管理
- 留言板與回覆機制
- 訂閱推薦文章（含互動式展開）

---

## 📁 專案檔案一覽

- `start.html`：網頁最開始的起點
- `index.php`：首頁入口
- `login.php`：登入
- `member.php`：會員資料管理介面
- `edit.php`:編輯會員資料介面
- `register.php` / `login.php`：會員資料註冊與登入介面
- `upload.php`：個人檔案上傳與下載功能
- `board.php`：留言發表與列表介面
- `subscribe.php`：訂閱推薦文章模組
- `msg/` 資料夾：留言板子模組（reply、editmsg、delmsg 等）
- `README.md`：專案說明文件

---

## 🚀 使用方式

1. 將專案放入本機 XAMPP 或遠端主機
2. 建立 MySQL 資料庫 `DATA` 並匯入資料表
3. 調整 `DATABASE` 連線資訊（帳號、密碼、資料庫名稱）
4. 開啟 `start.html` 開始使用
