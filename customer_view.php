<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$id]);
$c = $stmt->fetch();
if (!$c) { http_response_code(404); exit('Zákazník sa nenašiel.'); }

$g = db()->prepare('SELECT * FROM gardens WHERE customer_id = ? ORDER BY id DESC');
$g->execute([$id]);
$gardens = $g->fetchAll();

$s = db()->prepare('SELECT * FROM service_requests WHERE customer_id = ? ORDER BY created_at DESC, id DESC');
$s->execute([$id]);
$requests = $s->fetchAll();

$pageTitle = 'Detail zákazníka';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">ZÁKAZNÍK #<?= (int)$c['id'] ?></p><h1><?= e($c['first_name'] . ' ' . $c['last_name']) ?></h1><p class="muted">Zákaznícka karta a história servisu.</p></div>
    <div class="button-row"><a class="button button-plain" href="customers.php">← Späť</a><a class="button button-primary" href="customer_form.php?id=<?= (int)$c['id'] ?>">Upraviť údaje</a></div>
</div>
<div class="detail-grid">
    <section class="panel">
        <p class="eyebrow">KONTAKTNÉ ÚDAJE</p><h2>Kontakt</h2>
        <dl class="details">
            <dt>Telefón</dt><dd><?= e($c['phone']) ?></dd>
            <dt>E-mail</dt><dd><?= e($c['email'] ?: 'Neuvedený') ?></dd>
            <dt>Adresa</dt><dd><?= e(trim(($c['street'] ?? '') . ', ' . ($c['postal_code'] ?? '') . ' ' . ($c['city'] ?? ''), ', ')) ?: 'Neuvedená' ?></dd>
            <dt>Poznámky</dt><dd><?= nl2br(e($c['notes'] ?: 'Bez poznámok')) ?></dd>
        </dl>
    </section>
    <section class="panel">
        <div class="panel-heading"><div><p class="eyebrow">TECHNICKÁ KARTA</p><h2>Záhrady</h2></div><a class="button button-small button-primary" href="garden_form.php?customer_id=<?= (int)$c['id'] ?>">＋ Záhrada</a></div>
        <?php if (!$gardens): ?><p class="muted">Zatiaľ nie je evidovaná záhrada.</p><?php else: foreach ($gardens as $garden): ?>
            <div class="garden-card"><strong><?= e($garden['garden_name']) ?></strong><p><?= (int)$garden['zone_count'] ?> zón · <?= e($garden['sprinkler_type'] ?: 'Typ postrekovačov neuvedený') ?></p><p class="muted">Riadiaca jednotka: <?= e($garden['controller_model'] ?: 'Neuvedená') ?><br>Zdroj vody: <?= e($garden['water_source'] ?: 'Neuvedený') ?></p></div>
        <?php endforeach; endif; ?>
    </section>
</div>
<section class="panel">
    <div class="panel-heading"><div><p class="eyebrow">HISTÓRIA</p><h2>Servisné požiadavky</h2></div><a class="button button-primary" href="service_form.php?customer_id=<?= (int)$c['id'] ?>">＋ Pridať požiadavku</a></div>
    <?php if (!$requests): ?><p class="muted">Zatiaľ bez servisných požiadaviek.</p><?php else: ?>
        <div class="table-wrap"><table><thead><tr><th>Požiadavka</th><th>Priorita</th><th>Stav</th><th>Nahlásené</th></tr></thead><tbody>
        <?php foreach ($requests as $r): ?><tr><td><strong><?= e($r['title']) ?></strong><small class="subline"><?= e($r['description']) ?></small></td><td><span class="pill"><?= e($r['priority']) ?></span></td><td><span class="status"><?= e($r['status']) ?></span></td><td><?= e(date('d.m.Y', strtotime($r['created_at']))) ?></td></tr><?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
