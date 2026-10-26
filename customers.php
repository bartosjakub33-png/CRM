<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$search = trim($_GET['q'] ?? '');
$sql = "SELECT c.*,
        (SELECT COUNT(*) FROM gardens g WHERE g.customer_id = c.id) AS garden_count,
        (SELECT COUNT(*) FROM service_requests s WHERE s.customer_id = c.id) AS request_count
        FROM customers c";
$params = [];
if ($search !== '') {
    // Pri vypnutých emulovaných prepared statements sa nesmie ten istý :q použiť viackrát
    $sql .= " WHERE c.first_name LIKE :q1 OR c.last_name LIKE :q2 OR c.phone LIKE :q3 OR c.email LIKE :q4";
    $like = '%' . $search . '%';
    $params = ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like];
}
$sql .= " ORDER BY c.last_name, c.first_name";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

$pageTitle = 'Zákazníci';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">EVIDENCIA</p><h1>Zákazníci</h1><p class="muted">Kontakty a základný prehľad o zákazníkoch.</p></div>
    <a class="button button-primary" href="customer_form.php">＋ Pridať zákazníka</a>
</div>
<form class="searchbar" method="get">
    <input type="search" name="q" placeholder="Hľadať meno, telefón alebo e-mail…" value="<?= e($search) ?>">
    <button class="button button-dark" type="submit">Hľadať</button>
    <?php if ($search !== ''): ?><a class="button button-plain" href="customers.php">Zrušiť</a><?php endif; ?>
</form>
<section class="panel">
    <?php if (!$customers): ?>
        <div class="empty"><strong>Nenašli sa žiadni zákazníci.</strong><p>Skús zmeniť hľadaný výraz alebo pridaj zákazníka.</p></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Meno</th><th>Kontakt</th><th>Adresa</th><th>Záhrady</th><th>Požiadavky</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($customers as $c): ?>
                <tr>
                    <td><a class="table-link" href="customer_view.php?id=<?= (int)$c['id'] ?>"><?= e($c['first_name'] . ' ' . $c['last_name']) ?></a><small class="subline">Zákazník #<?= (int)$c['id'] ?></small></td>
                    <td><?= e($c['phone']) ?><small class="subline"><?= e($c['email']) ?></small></td>
                    <td><?= e(trim(($c['street'] ?? '') . ', ' . ($c['city'] ?? ''), ', ')) ?><small class="subline"><?= e($c['postal_code']) ?></small></td>
                    <td><?= (int)$c['garden_count'] ?></td>
                    <td><?= (int)$c['request_count'] ?></td>
                    <td><a class="button button-small button-plain" href="customer_form.php?id=<?= (int)$c['id'] ?>">Upraviť</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
