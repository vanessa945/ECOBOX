<?php
session_start();
include("db.php");
$page_title = "評論管理";
$active_page = "reviews";
include("admin-header.php");

// 聯合查詢
$sql = "SELECT r.*, s.store_name, u.user_name 
        FROM store_reviews r 
        LEFT JOIN stores s ON r.store_id = s.store_id 
        LEFT JOIN users u ON r.user_id = u.user_id 
        ORDER BY r.review_date DESC";
$result = mysqli_query($conn, $sql);
?>

<main class="admin-main" style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: var(--green-dark); margin: 0;">💬 評論管理</h2>
        <div class="search-wrap">
            <input type="text" id="search-input" placeholder="搜尋評論內容…" oninput="filterReviews(this.value)">
        </div>
    </div>
    
    <div class="stats-bar" style="margin-bottom: 20px;">
        <div class="stat-chip">總評論數 <strong><?php echo mysqli_num_rows($result); ?></strong></div>
    </div>

    <div class="comment-list">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="comment-card" data-searchtext="<?php echo htmlspecialchars(strtolower($row['comment_text'])); ?>" style="background:#fff; border:1px solid #e0e0e0; border-radius:12px; padding:20px; margin-bottom:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                
                <div style="font-weight:bold; margin-bottom:10px;">
                    <?php echo htmlspecialchars($row['user_name'] ?? '匿名'); ?> 評論了 
                    <span style="color:var(--green-dark);"><?php echo htmlspecialchars($row['store_name'] ?? '未知商家'); ?></span>
                </div>
                
                <div class="comment-box" style="padding:15px; background:#f9f9f9; border-left:4px solid var(--green-mid); margin-bottom:10px;">
                    <div><?php echo htmlspecialchars($row['comment_text']); ?></div>
                    <small style="color:#888;"><?php echo $row['review_date']; ?></small>
                    <button onclick="deleteReview(<?php echo $row['No']; ?>)" class="btn btn-delete" style="float:right;">刪除</button>
                </div>

                <?php if (!empty($row['reply_text'])): ?>
                    <div class="reply-box" style="padding:15px; background:#fff8e1; border-left:4px solid #f39c12; margin-left:30px; margin-top:10px;">
                        <strong>商家回覆：</strong>
                        <div><?php echo htmlspecialchars($row['reply_text']); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<script>
function filterReviews(keyword) {
    const cards = document.querySelectorAll('.comment-card');
    const kw = keyword.toLowerCase().trim();
    cards.forEach(card => {
        const text = card.dataset.searchtext || '';
        card.style.display = (!kw || text.includes(kw)) ? '' : 'none';
    });
}

function deleteReview(id) {
    if (confirm('確定要刪除這則評論嗎？')) {
        window.location.href = 'admin_delete.php?id=' + id + '&type=review';
    }
}
</script>