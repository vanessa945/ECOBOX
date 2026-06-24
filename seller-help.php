<?php
session_start();
include("db.php");

// 確保店鋪 ID
if (!isset($_SESSION['store_id'])) { $_SESSION['store_id'] = 1; }
$current_id = $_SESSION['store_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'contact') {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    
    // 將問題寫入 admin_messages 資料表
    // 假設 sender_id 是 store_id，sender_type 是 'store'
    $sql_insert = "INSERT INTO admin_messages (sender_id, sender_type, message_content, status) 
                   VALUES ('$current_id', 'store', '$msg', 'pending')";
    
    if (mysqli_query($conn, $sql_insert)) {
        echo "<script>alert('問題已送出，管理員將盡快回覆！'); window.location.href='seller-help.php';</script>";
        exit;
    } else {
        echo "<script>alert('傳送失敗，請稍後再試');</script>";
    }
}

// 抓取商家名稱
$sql_store = "SELECT store_name FROM stores WHERE store_id = $current_id";
$res_store = mysqli_query($conn, $sql_store);
$data_store = mysqli_fetch_assoc($res_store);
$store_name = $data_store ? $data_store['store_name'] : "麥當勞-高雄楠梓店";
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>賣家幫助中心</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="seller.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
      --cream: #e3e2d4;       
      --green-pale: #8ab8a1;  
      --green-deep: #6cae8b;  
      --white: #ffffff;       
      --text-dark: #23342b;   
      --text-muted: #5a6e63;  
      --border: rgba(108, 174, 139, 0.2); 
      --shadow: 0 4px 16px rgba(35, 52, 43, 0.05); 
    }

    body {
      background-color: var(--cream) !important;
      color: var(--text-dark);
      font-family: 'Noto Sans TC', sans-serif;
      margin: 0; padding: 0;
    }

    .seller-header { background: #6cae8b !important; }

    /* 側邊欄導覽 */
    .full-menu {
      position: fixed; top: 0; left: 0; width: 300px; height: 100vh;
      background-color: #6cae8b !important; z-index: 9999; padding: 30px 24px;
      box-sizing: border-box; display: block; 
      transform: translateX(-100%); transition: transform 0.3s ease-in-out;
      box-shadow: 5px 0 15px rgba(0,0,0,0.2);
    }
    #menu-checkbox:checked ~ .full-menu { transform: translateX(0); }
    .full-menu .menu-content { display: flex; flex-direction: column; gap: 30px; }
    .menu-store-profile { padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.15); margin-bottom: 10px; }
    .menu-store-name { color: #ffffff; font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; }
    .menu-store-badge { display: inline-block; background-color: rgba(255,255,255,0.2); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 1.2rem; }
    .full-menu .menu-main-title { color: rgba(255,255,255,0.6); font-size: 1.4rem; }
    .full-menu .menu-item { font-size: 1.6rem; color: #ffffff; text-decoration: none; display: block; padding: 12px 10px; border-radius: 8px; }
    .full-menu .menu-item:hover { background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; }
    .full-menu .menu-item.active { background-color: rgba(255, 255, 255, 0.3); font-weight: bold; }
    .full-menu .menu-close { position: absolute; top: 20px; right: 20px; color: #ffffff; font-size: 2rem; cursor: pointer; }

        /* 2. 幫助中心主體與輸入框樣式 */
        .help-container { max-width: 800px; margin: 100px auto 40px; padding: 20px; }
        .input-wrapper { display: flex; align-items: center; background: white; border: 1px solid #8ab8a1; border-radius: 30px; padding: 10px 20px; margin-top: 20px; }
        .reply-input { flex: 1; border: none; outline: none; font-size: 1.2rem; }
        .submit-btn { background: #6cae8b; color: white; border: none; border-radius: 50%; width: 45px; height: 45px; cursor: pointer; }

        .sticker-popup {
      display: none; position: absolute; bottom: 45px; right: 0; background: white;
      border: 1px solid var(--green-pale); border-radius: 12px; padding: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 10; width: 220px;
    }
    .sticker-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; font-size: 2.2rem; text-align: center; }
    .sticker-item { cursor: pointer; transition: transform 0.1s; user-select: none; }
    .sticker-item:hover { transform: scale(1.2); }
    /* 漢堡選單網格排列 (與評論頁相同) */
    .menu-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; }
    
    .menu-box { 
      display: flex; flex-direction: column; align-items: center; justify-content: center; 
      background: rgba(255,255,255,0.1); padding: 15px 5px; border-radius: 15px; 
      text-decoration: none; color: white; text-align: center; font-size: 1.2rem; transition: 0.2s; 
    }
    .menu-box:hover, .menu-box.active { background: rgba(255,255,255,0.3); }

    /* 確保貼圖按鈕的容器樣式 */
    .sticker-container { position: relative; display: flex; align-items: center; }
    </style>
</head>
<body>

    <input type="checkbox" id="menu-checkbox">

    <header class="seller-header">
        <div class="header-left">
            <label for="menu-checkbox" class="menu-toggle">☰</label>
            <div class="seller-brand">🎃 EcoBox 後台</div>
            <span class="restaurant-badge">🏪 <?php echo htmlspecialchars($store_name); ?></span>
        </div>
        <div class="header-right">
            <button class="icon-btn">🔔</button>
            <button class="icon-btn" onclick="history.back()">↩️</button>
            <a href="seller_index.php" class="icon-btn">🏠</a>
            <button class="icon-btn">👤</button>
        </div>
    </header>

    <div class="full-menu">
    <label for="menu-checkbox" class="menu-close">✕</label>
    <div class="menu-content">
      <div class="menu-store-profile">
        <h2 class="menu-store-name"><?php echo htmlspecialchars($store_name); ?></h2>
        <span class="menu-store-badge">已認證合作夥伴</span>
      </div>
      <div>
        <h3 class="menu-main-title">核心管理</h3>
        <div class="menu-list">
          <a href="seller-products.php" class="menu-item">🍱 商品管理</a>
          <a href="seller-data.php" class="menu-item">📈 數據中心</a>
          <a href="seller-reviews.php" class="menu-item">💬 評論管理</a>
          <a href="seller-finance.php" class="menu-item">💰 金流服務</a>
          <a href="seller-help.php" class="menu-item active">❓ 賣家幫助中心</a>
        </div>
      </div>
    </div>
  </div>
    <main class="help-container">
        <h2 style="font-size: 2.2rem; margin-bottom: 20px;">❓ 賣家幫助中心</h2>
        
        <input type="text" id="search-faq" placeholder="🔎 搜尋常見問題關鍵字..." style="width:100%; padding:15px; border-radius:30px; border:1px solid #8ab8a1; margin-bottom:20px; font-size:1.4rem;">

        <div class="faq-scroll-box" style="height: 450px; overflow-y: auto; padding-right: 10px;">
            <?php 
            $faqs = mysqli_query($conn, "SELECT * FROM faq_list");
            while($row = mysqli_fetch_assoc($faqs)): ?>
                <div class="faq-item" style="background: white; padding: 20px; border-radius: 14px; margin-bottom: 15px; border: 1px solid rgba(108,174,139,0.2);">
                    <p style="font-weight:700; font-size:1.5rem; margin:0 0 8px 0;">Q: <?php echo htmlspecialchars($row['question']); ?></p>
                    <p style="color:var(--text-muted); font-size:1.3rem; margin:0;">A: <?php echo htmlspecialchars($row['answer']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <h3 style="margin-top: 30px;">聯繫管理者</h3>
        <form action="seller-help.php" method="POST" class="input-wrapper">
    <input type="hidden" name="action" value="contact">
    <input type="text" name="message" class="reply-input" placeholder="輸入您的問題..." required>
    
    <div class="sticker-container">
        <i class="fa-regular fa-face-smile" onclick="toggleStickerBox(event)" style="margin-right:15px; color:#aaa; font-size:1.5rem; cursor:pointer;"></i>
        
        <div id="sticker-popup" class="sticker-popup">
            <div class="sticker-grid">
                <div class="sticker-item" onclick="appendSticker('😊')">😊</div>
                <div class="sticker-item" onclick="appendSticker('🙏')">🙏</div>
                <div class="sticker-item" onclick="appendSticker('👍')">👍</div>
                <div class="sticker-item" onclick="appendSticker('✨')">✨</div>
            </div>
        </div>
    </div>
    
    <button type="submit" class="submit-btn"><i class="fa-solid fa-paper-plane"></i></button>
</form>
    </main>

    <script>
        document.getElementById('search-faq').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.faq-item').forEach(item => {
                item.style.display = item.innerText.toLowerCase().includes(term) ? 'block' : 'none';
            });
        });
        // 貼圖盒切換邏輯
    function toggleStickerBox(event) {
        event.stopPropagation();
        const popup = document.getElementById('sticker-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }

    // 點擊貼圖填入文字
    function appendSticker(sticker) {
        const input = document.querySelector('input[name="message"]');
        input.value += sticker;
        document.getElementById('sticker-popup').style.display = 'none';
    }

    // 點擊空白處關閉
    document.addEventListener('click', () => {
        document.getElementById('sticker-popup').style.display = 'none';
    });
    </script>

</body>
</html>