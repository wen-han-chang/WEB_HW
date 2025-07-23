<?php
session_start();
$servername = "localhost";
$username = "willy";
$password = "123";
$dbname = "DATA";

// 資料庫連線
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("連線失敗：" . $conn->connect_error);
}

// 抓使用者帳號
$account = $_SESSION["account"] ?? null;
$loggedInUser = $account ?? "訪客";

// 訂閱狀態查詢
$isSubscribed = false;
if ($account) {
    $stmt = $conn->prepare("SELECT is_subscribed FROM subscriptions WHERE user_id = ?");
    $stmt->bind_param("s", $account);
    $stmt->execute();
    $stmt->bind_result($subscribed);
    if ($stmt->fetch()) {
        $isSubscribed = $subscribed;
    }
    $stmt->close();
}

// 處理訂閱或取消訂閱
if ($_SERVER["REQUEST_METHOD"] === "POST" && $account) {
    $subscribe = $_POST["subscribe"] === "true" ? 1 : 0;
    $stmt = $conn->prepare("
        INSERT INTO subscriptions (user_id, is_subscribed, subscribed_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE is_subscribed = VALUES(is_subscribed), subscribed_at = NOW()
    ");
    $stmt->bind_param("si", $account, $subscribe);
    $stmt->execute();
    $stmt->close();
    header("Location: subscribe.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>🔔 訂閱推薦</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
  <!-- 導覽列 -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">👥 會員管理</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="member.php">📋 會員列表</a></li>
          <li class="nav-item"><a class="nav-link" href="edit.php">📝 修改資料</a></li>
          <li class="nav-item"><a class="nav-link" href="upload.php">📁 檔案管理</a></li>
          <li class="nav-item"><a class="nav-link" href="board.php">💬 留言板</a></li>
          <li class="nav-item"><a class="nav-link active" href="subscribe.php">🔔 訂閱</a></li>
        </ul>
        <div class="d-flex align-items-center">
          <span class="text-white me-3"><?= htmlspecialchars($loggedInUser); ?> · 您好！</span>
          <a href="start.html" class="btn btn-outline-danger">🚪 登出</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- 主內容 -->
  <div class="container">
    <div class="bg-white rounded shadow p-4 text-center">
      <h2 class="mb-4"><?= $isSubscribed ? '🎉 您已訂閱！' : '🔔 訂閱推薦內容' ?></h2>

      <?php if ($account): ?>
      <form method="POST" class="mb-3">
        <input type="hidden" name="subscribe" value="<?= $isSubscribed ? 'false' : 'true' ?>">
        <button class="btn <?= $isSubscribed ? 'btn-danger' : 'btn-success' ?>">
          <?= $isSubscribed ? '取消訂閱' : '立即訂閱' ?>
        </button>
      </form>
      <?php else: ?>
        <div class="alert alert-warning">⚠️ 請先登入才能訂閱！</div>
      <?php endif; ?>

      <?php if ($isSubscribed): ?>
    <div class="accordion mt-4 text-start" id="richAccordion">
    <?php
    $topics = [
        ["title" => "🌍 氣候變遷的真相", "content" => "全球氣候持續變暖已經不是預測，而是正在發生的事實。從極端氣候現象的頻率增加，到南北極冰層的迅速融化，科學家警告我們已踏入一個不可逆的臨界點。不論是個人減碳行動、政策推動綠能轉型，或企業責任感的提升，每一份努力都可能成為地球的救命線。氣候變遷不僅是環境問題，更是關乎經濟、健康與全球公平的挑戰。"],
        ["title" => "🖼️ 藝術如何改變社會", "content" => "藝術不只是美的呈現，它往往是社會反思的鏡子。從畢卡索的《格爾尼卡》揭露戰爭殘酷，到街頭塗鴉作為青年發聲的平台，創作能打破沉默、引發對話，並促進觀念改變。藝術能使邊緣議題浮上檯面，也能為個人心理創傷帶來治癒力。你曾被一幅畫、一段旋律、或一句詩深深打動嗎？那就是藝術的力量。"],
        ["title" => "🤖 AI 是否會取代人類？", "content" => "人工智慧正在重塑工作型態與產業架構。從自動駕駛車、語音助理、醫療診斷到藝術生成，AI 展現出前所未有的效率與精準。然而，科技的崛起也引發「人類價值」的焦慮。我們應該思考的不是 AI 是否取代人類，而是如何與它協同共存，讓人類情感、創意與倫理意識成為未來不可取代的優勢。"],
        ["title" => "🌱 慢活哲學：生活不必急", "content" => "在高效率、高資訊的時代，我們是否仍懂得「停下來」的價值？慢活不是懶惰，而是對生活品質的深度追求。慢食、慢閱讀、慢旅——這些體驗讓人從繁忙中抽離，回歸對感官與心靈的覺察。你上次無目的地散步是什麼時候？當我們學會與時間對話，生活也會回應更真實的滋味。"],
        ["title" => "🎮 遊戲設計中的心理學", "content" => "好的遊戲不只讓人沉迷，更讓人感覺有成就感。關卡設計背後藏著大量心理學原理，像是『操作性強化』（rewards）、『目標成就』與『難度曲線』。從角色成長系統到玩家選擇自由度，設計師需洞察人類動機與行為反應，才能打造真正引人入勝的體驗。你喜歡哪種類型的遊戲？那往往透露了你內在需求。"],
        ["title" => "🧬 基因編輯的倫理爭議", "content" => "CRISPR 技術讓科學家可以直接修改 DNA，開啟醫療革命與新物種培育的可能。然而，這同時引爆『設計寶寶』、『物種滅絕』等爭議。當人類有能力重構生命時，我們也需面對誰該決定什麼是『正確的基因』。科技越強大，倫理的鷹眼就越不能閃躲。"],
        ["title" => "💡 創意思考的五種方式", "content" => "創意不是天賦，是可以鍛鍊的思考方式。從反向思考、隨機聯想、設限創作，到角色扮演與情境重塑，每種技巧都能突破思維慣性。真正的創意來自能連結遠距概念並產生新組合的人。當你說『我想不到』時，不妨試試這些方法，靈感或許就在下一個腦中漫步裡。"],
        ["title" => "📱 社群媒體的雙面性", "content" => "Facebook、Instagram、TikTok 等平台讓人快速連接世界，但也可能讓人陷入比較焦慮與注意力破碎。社群媒體一方面塑造了現代生活風格，一方面也影響著自我認同與人際關係。該如何健康使用網路？關鍵在於意識與節制——我們控制時間，而非被時間綁架。"],
        ["title" => "🌃 城市設計與人性", "content" => "你所在的城市，是為人而設計的嗎？從行人動線、公共空間到燈光氛圍，一個城市的設計決定了人的生活品質與互動模式。良好的城市規劃能提升安全感、社交密度與幸福指數。反之，雜亂、過度汽車導向的設計，可能使人感到孤立。城市，不只是建築，是一種文化容器。"],
        ["title" => "📖 閱讀的力量", "content" => "一本好書，能改變人的思想軌跡。閱讀不只是吸收知識，更是跟自我對話的方式。在不同階段，你所選擇的書往往反映了你正在尋求的答案。從小說中的情感共鳴，到理論書的邏輯啟發，閱讀能拓展語言、邏輯、創造力與同理心。即便一天只讀一頁，也是在滋養自己。"]
    ];

    foreach ($topics as $i => $topic): ?>
        <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?= $i ?>">
            <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>">
            <?= $topic["title"] ?>
            </button>
        </h2>
        <div id="collapse<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
            data-bs-parent="#richAccordion">
            <div class="accordion-body">
            <?= $topic["content"] ?>
            </div>
        </div>
        </div>
    <?php endforeach; ?>
    </div>
      <?php endif; ?>

      <!-- 回主頁按鈕 -->
      <div class="mt-4">
        <a href="index.php" class="btn btn-outline-secondary">🔙 回主頁</a>
      </div>
    </div>
  </div>
</body>
</html>