<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = db();
$customerId = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
if (!$customerId) { http_response_code(400); exit('Chýba zákazník.'); }

$stmt = $pdo->prepare('SELECT id, first_name, last_name FROM customers WHERE id = ?');
$stmt->execute([$customerId]);
$cust = $stmt->fetch();
if (!$cust) { http_response_code(404); exit('Zákazník sa nenašiel.'); }

$errors = [];
$data = ['garden_name'=>'Hlavná záhrada','zone_count'=>'1','sprinkler_type'=>'','controller_model'=>'','valve_info'=>'','water_source'=>'','notes'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($data) as $key) $data[$key] = trim($_POST[$key] ?? '');
    if ($data['garden_name'] === '') $errors[] = 'Vyplň názov záhrady.';
    $zones = filter_var($data['zone_count'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 999]]);
    if ($zones === false) $errors[] = 'Počet zón musí byť číslo od 1 do 999.';

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO gardens (customer_id,garden_name,zone_count,sprinkler_type,controller_model,valve_info,water_source,notes) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $customerId, $data['garden_name'], $zones,
            $data['sprinkler_type'] ?: null, $data['controller_model'] ?: null,
            $data['valve_info'] ?: null, $data['water_source'] ?: null, $data['notes'] ?: null
        ]);
        redirect('customer_view.php?id=' . $customerId);
    }
}

$pageTitle = 'Nová záhrada';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">TECHNICKÁ KARTA</p><h1>Pridať záhradu</h1><p class="muted">Zákazník: <?= e($cust['first_name'] . ' ' . $cust['last_name']) ?></p></div>
    <a class="button button-plain" href="customer_view.php?id=<?= (int)$customerId ?>">← Späť na zákazníka</a>
</div>
<section class="panel form-panel">
    <?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
    <form method="post" class="form-grid">
        <label>Názov záhrady *<input name="garden_name" required maxlength="120" value="<?= e($data['garden_name']) ?>"></label>
        <label>Počet zón *<input type="number" name="zone_count" min="1" max="999" required value="<?= e($data['zone_count']) ?>"></label>
        <label>Typ postrekovačov<input name="sprinkler_type" maxlength="120" value="<?= e($data['sprinkler_type']) ?>"></label>
        <label>Riadiaca jednotka<input name="controller_model" maxlength="120" value="<?= e($data['controller_model']) ?>"></label>
        <label>Zdroj vody<input name="water_source" maxlength="80" value="<?= e($data['water_source']) ?>" placeholder="Napr. studňa, vodovod"></label>
        <label>Ventily<input name="valve_info" maxlength="255" value="<?= e($data['valve_info']) ?>"></label>
        <label class="full">Poznámky<textarea name="notes" rows="4"><?= e($data['notes']) ?></textarea></label>
        <div class="form-actions full"><button class="button button-primary" type="submit">Uložiť záhradu</button><a class="button button-plain" href="customer_view.php?id=<?= (int)$customerId ?>">Zrušiť</a></div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
