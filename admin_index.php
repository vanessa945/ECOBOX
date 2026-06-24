<?php
// admin_index.php
session_start();
if (!file_exists("db.php")) { die("找不到 db.php"); }
if (!file_exists("admin-header.php")) { die("找不到 admin-header.php"); }
include("db.php");

$page_title = "使用者管理";
$active_page = "users";
include("admin-header.php");
?>

<?php
// Fetch all users
$users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id ASC");
$total = mysqli_num_rows($users_result);
$rows = [];
while ($row = mysqli_fetch_assoc($users_result)) { $rows[] = $row; }
?>

<div class="page-header" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="color: var(--green-dark); margin: 0;">👥 使用者管理</h2>
        <div class="search-wrap">
            <input type="text" id="search-input" placeholder="搜尋使用者名稱…" oninput="filterUsers(this.value)">
        </div>
    </div>
    
    <div class="stats-bar" style="margin-bottom: 0;">
        <div class="stat-chip">總使用者 <strong><?php echo $total; ?></strong></div>
    </div>
</div>

<!-- User list -->
<div id="user-list-wrap">
<?php if (empty($rows)): ?>
  <div class="empty-state">
    <div class="icon">👥</div>
    目前尚無使用者資料
  </div>
<?php else: foreach ($rows as $row): ?>

  <div class="user-card" data-searchtext="<?php echo htmlspecialchars(strtolower(($row['user_name'] ?? '') . ' ' . ($row['email'] ?? '') . ' ' . ($row['phone'] ?? ''))); ?>">

    <!-- Avatar -->
    <div class="user-avatar">👤</div>

    <!-- Info grid -->
    <div class="user-info">
      <div class="info-item">
        <span class="info-label">使用者帳號（暱稱）</span>
        <span class="info-value"><?php echo htmlspecialchars($row['user_name'] ?? '—'); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">登入密碼</span>
        <span class="info-value masked">••••••••</span>
      </div>
      <div class="info-item">
        <span class="info-label">電話</span>
        <span class="info-value"><?php echo htmlspecialchars($row['phone'] ?? '—'); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">郵件</span>
        <span class="info-value"><?php echo htmlspecialchars($row['email'] ?? '—'); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">過敏原</span>
        <span class="info-value"><?php echo htmlspecialchars($row['allergens'] ?? '無'); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">信用卡</span>
        <span class="info-value masked"><?php echo !empty($row['credit_card']) ? '•••• •••• •••• ' . substr($row['credit_card'], -4) : '未設定'; ?></span>
      </div>
    </div>

    <!-- Actions -->
    <div class="card-actions">
      <button class="btn btn-edit"   onclick="location.href='admin_edit.php?id=<?php echo $row['user_id']; ?>'">修改</button>
      <button class="btn btn-delete" onclick="confirmDelete(<?php echo $row['user_id']; ?>, '<?php echo htmlspecialchars($row['user_name'] ?? '此使用者', ENT_QUOTES); ?>')">刪除</button>
    </div>
  </div>

<?php endforeach; endif; ?>
</div>

<script>
  // Live search filter
  function filterUsers(keyword) {
    const cards = document.querySelectorAll('.user-card');
    const kw = keyword.toLowerCase().trim();
    cards.forEach(card => {
      const text = card.dataset.searchtext || '';
      card.style.display = (!kw || text.includes(kw)) ? '' : 'none';
    });
  }

  // Delete confirmation
  function confirmDelete(userId, name) {
    if (confirm(`確定要刪除「${name}」嗎？此操作無法復原。`)) {
      window.location.href = `admin_delete.php?id=${userId}`;
    }
  }
</script>

</main>
</body>
</html>