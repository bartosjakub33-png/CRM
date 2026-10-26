<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = db();
$customerCount = (int)$pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$openCount = (int)$pdo->query("SELECT COUNT(*) FROM service_requests WHERE status IN ('Nová','Naplánovaná','V riešení')")->fetchColumn();
$gardenCount = (int)$pdo->query('SELECT COUNT(*) FROM gardens')->fetchColumn();

$recent = $pdo->query(
    "SELECT s.id, s.title, s.priority, s.status, s.created_at,
            CONCAT(c.first_name, ' ', c.last_name) AS customer_name
     FROM service_requests s
     JOIN customers c ON c.id = s.customer_id
     ORDER BY s.created_at DESC, s.id DESC
     LIMIT 5"
)->fetchAll();

$pageTitle = 'Prehľad';
require __DIR__ . '/includes/header.php';
?>
<section class="welcome">
    <div>
        <p class="eyebrow">ZÁKAZNÍCI · ZÁHRADY · SERVIS</p>
        <h1>Vitajte v CRM</h1>
        <p>Všetky dôležité informácie o zákazníkoch a servise na jednom mieste.</p>
    </div>
    <a class="button button-light" href="customer_form.php">＋ Pridať zákazníka</a>
</section>

<section class="stats">
    <article class="stat-card"><span class="stat-icon">♙</span><span class="stat-label">Zákazníci</span><strong><?= $customerCount ?></strong><a href="customers.php">Zobraziť zoznam →</a></article>
    <article class="stat-card"><span class="stat-icon">⌂</span><span class="stat-label">Evidované záhrady</span><strong><?= $gardenCount ?></strong><span class="muted">V databáze</span></article>
    <article class="stat-card"><span class="stat-icon">⚒</span><span class="stat-label">Otvorené požiadavky</span><strong><?= $openCount ?></strong><a href="services.php">Zobraziť servis →</a></article>
</section>

<section class="panel">
    <div class="panel-heading">
        <div><p class="eyebrow">ČO TREBA RIEŠIŤ</p><h2>Posledné servisné požiadavky</h2></div>
        <a class="button button-primary" href="service_form.php">＋ Nová požiadavka</a>
    </div>
    <?php if (!$recent): ?>
        <div class="empty"><strong>Zatiaľ tu nič nie je.</strong><p>Keď pridáte servisnú požiadavku, zobrazí sa tu.</p></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Požiadavka</th><th>Zákazník</th><th>Priorita</th><th>Stav</th><th>Dátum</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $item): ?>
                <tr>
                    <td><strong>#<?= (int)$item['id'] ?> · <?= e($item['title']) ?></strong></td>
                    <td><?= e($item['customer_name']) ?></td>
                    <td><span class="pill"><?= e($item['priority']) ?></span></td>
                    <td><span class="status"><?= e($item['status']) ?></span></td>
                    <td><?= e(date('d.m.Y', strtotime($item['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
