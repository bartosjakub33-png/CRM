<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$statuses = ['Nová','Naplánovaná','V riešení','Vyriešená','Zrušená'];

// Zmena stavu požiadavky
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rid = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $newStatus = $_POST['status'] ?? '';
    if ($rid && in_array($newStatus, $statuses, true)) {
        $stmt = db()->prepare('UPDATE service_requests SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $rid]);
    }
    redirect('services.php');
}

$sql = "SELECT s.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name
        FROM service_requests s JOIN customers c ON c.id=s.customer_id
        ORDER BY FIELD(s.status,'Nová','Naplánovaná','V riešení','Vyriešená','Zrušená'), s.created_at DESC, s.id DESC";
$requests = db()->query($sql)->fetchAll();
$pageTitle = 'Servis';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">SERVIS A ÚDRŽBA</p><h1>Servisné požiadavky</h1><p class="muted">Všetky nahlásené problémy na jednom mieste.</p></div>
    <a class="button button-primary" href="service_form.php">＋ Nová požiadavka</a>
</div>
<section class="panel">
<?php if (!$requests): ?><div class="empty"><strong>Žiadne servisné požiadavky.</strong><p>Pridaj prvú požiadavku a bude sa zobrazovať tu.</p></div>
<?php else: ?><div class="table-wrap"><table><thead><tr><th>Požiadavka</th><th>Zákazník</th><th>Priorita</th><th>Stav</th><th>Nahlásené</th></tr></thead><tbody>
<?php foreach ($requests as $r): ?><tr>
<td><strong>#<?= (int)$r['id'] ?> · <?= e($r['title']) ?></strong><small class="subline"><?= e($r['description']) ?></small></td>
<td><a class="table-link" href="customer_view.php?id=<?= (int)$r['customer_id'] ?>"><?= e($r['customer_name']) ?></a></td>
<td><span class="pill"><?= e($r['priority']) ?></span></td>
<td>
    <form method="post" class="inline-form">
        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <select name="status" onchange="this.form.submit()">
            <?php foreach ($statuses as $st): ?><option <?= $r['status'] === $st ? 'selected' : '' ?>><?= e($st) ?></option><?php endforeach; ?>
        </select>
        <noscript><button class="button button-small button-plain" type="submit">Uložiť</button></noscript>
    </form>
</td>
<td><?= e(date('d.m.Y', strtotime($r['created_at']))) ?></td>
</tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
