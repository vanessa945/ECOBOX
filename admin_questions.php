<?php
session_start();
include("db.php");
// 請確保有檢查管理員登入狀態
include("admin-header.php");
?>
<main class="admin-main" style="max-width: 1100px; margin: 0 auto; padding: 20px;">
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 2.2rem; color: #23342b; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-comments"></i> 問題中心管理
        </h2>
    </div>
    
    <div class="user-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 16px rgba(35, 52, 43, 0.05);">
       
    <?php
    $res = mysqli_query($conn, "SELECT * FROM admin_messages ORDER BY created_at DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        $status_color = ($row['status'] == 'pending') ? '#ffcccc' : '#ccffcc';
        echo "<div style='border:1px solid #ddd; padding:15px; margin-bottom:15px; background:{$status_color}; border-radius:8px;'>";
        echo "<p><small>發訊者: {$row['sender_type']} (ID: {$row['sender_id']}) - 時間: {$row['created_at']}</small></p>";
        echo "<p><strong>問題內容:</strong> " . htmlspecialchars($row['message_content']) . "</p>";
        
        if ($row['status'] == 'replied') {
            echo "<p style='color:green;'><strong>管理員回覆:</strong> " . htmlspecialchars($row['admin_reply']) . "</p>";
        } else {
            echo "<form action='reply_action.php' method='POST'>
                    <input type='hidden' name='msg_id' value='{$row['message_id']}'>
                    <textarea name='reply' style='width:100%; height:60px;' placeholder='請輸入回覆內容...' required></textarea>
                    <button type='submit' style='margin-top:5px;'>送出回覆</button>
                  </form>";
        }
        echo "</div>";
    }
    ?>
     </div>
</main>