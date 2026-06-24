<?php
session_start();
include("db.php");
$page_title = "商家管理";
$active_page = "stores";
include("admin-header.php");

$stores_result = mysqli_query($conn, "SELECT * FROM stores ORDER BY store_id ASC");
$rows = mysqli_fetch_all($stores_result, MYSQLI_ASSOC);
$total = count($rows);
?>

<div class="page-header" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="color: var(--green-dark); margin: 0;">🏪 商家管理</h2>
        <div class="search-wrap">
            <input type="text" id="search-input" placeholder="搜尋商家名稱…" oninput="filterStores(this.value)">
        </div>
    </div>
    
    <div class="stats-bar" style="margin-bottom: 0;">
        <div class="stat-chip">總商家 <strong><?php echo $total; ?></strong></div>
    </div>
</div>

<div id="store-list-wrap">
    <?php if (empty($rows)): ?>
        <div class="empty-state">目前尚無商家資料</div>
    <?php else: foreach ($rows as $row): ?>
        <div class="user-card store-card" data-searchtext="<?php echo htmlspecialchars(strtolower($row['store_name'])); ?>">
            <div class="user-avatar">🏪</div>
            <div class="user-info">
                <div class="info-item">
                    <span class="info-label">店名</span>
                    <span class="info-value"><?php echo htmlspecialchars($row['store_name'] ?? '—'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">電話</span>
                    <span class="info-value"><?php echo htmlspecialchars($row['phone'] ?? '無'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">郵件</span>
                    <span class="info-value"><?php echo htmlspecialchars($row['email'] ?? '無'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">地址</span>
                    <span class="info-value"><?php echo htmlspecialchars($row['address'] ?? '無'); ?></span>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn btn-edit" onclick="location.href='admin_edit.php?id=<?php echo $row['store_id']; ?>&type=store'">修改</button>
                <button class="btn btn-delete" onclick="confirmDelete(<?php echo $row['store_id']; ?>, '<?php echo htmlspecialchars($row['store_name'], ENT_QUOTES); ?>')">刪除</button>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<script>
function filterStores(keyword) {
    const cards = document.querySelectorAll('.store-card');
    const kw = keyword.toLowerCase().trim();
    cards.forEach(card => {
        const text = card.dataset.searchtext || '';
        card.style.display = (!kw || text.includes(kw)) ? '' : 'none';
    });
}
function confirmDelete(id, name) {
    if (confirm(`確定要刪除「${name}」嗎？`)) {
        window.location.href = `admin_delete.php?id=${id}&type=store`;
    }
}
</script>