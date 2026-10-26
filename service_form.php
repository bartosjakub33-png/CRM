<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = db();
$customers = $pdo->query("SELECT id, first_name, last_name FROM customers ORDER BY last_name, first_name")->fetchAll();
if (!$customers) {
    $pageTitle = 'Nová požiadavka';
    require __DIR__ . '/includes/header.php';
    echo '<section class="panel empty"><h2>Najprv pridaj zákazníka</h2><p>Servisná požiadavka musí patriť konkrétnemu zákazníkovi.</p><a class="button button-primary" href="customer_form.php">Pridať zákazníka</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
$priorities = ['Nízka','Normálna','Vysoká','Urgentná'];
$errors = [];
$data = ['customer_id'=>($_GET['customer_id'] ?? ''),'title'=>'','description'=>'','priority'=>'Normálna','preferred_date'=>''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($data) as $key) $data[$key] = trim($_POST[$key] ?? '');
    if (!filter_var($data['customer_id'], FILTER_VALIDATE_INT)) $errors[] = 'Vyber zákazníka.';
    if ($data['title'] === '') $errors[] = 'Vyplň názov požiadavky.';
    if ($data['description'] === '') $errors[] = 'Opíš problém.';
    if (!in_array($data['priority'], $priorities, true)) $errors[] = 'Vyber platnú prioritu.';
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO service_requests (customer_id,title,description,priority,request_date,preferred_date) VALUES (?,?,?,?,CURDATE(),?)');
        $stmt->execute([(int)$data['customer_id'], $data['title'], $data['description'], $data['priority'], $data['preferred_date'] ?: null]);
        redirect('services.php');
    }
}
$pageTitle = 'Nová servisná požiadavka';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">SERVIS A ÚDRŽBA</p><h1>Nová požiadavka</h1><p class="muted">Zaznamenaj problém, aby sa nestratil.</p></div>
    <a class="button button-plain" href="services.php">← Späť na servis</a>
</div>
<section class="panel form-panel">
<?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form method="post" class="form-grid">
    <label class="full">Zákazník *
        <select name="customer_id" required><option value="">Vyber zákazníka</option>
        <?php foreach ($customers as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$data['customer_id'] === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['last_name'] . ' ' . $c['first_name']) ?></option><?php endforeach; ?>
        </select>
    </label>
    <label class="full">Názov požiadavky *<input name="title" required maxlength="160" value="<?= e($data['title']) ?>" placeholder="Napr. Pokazený ventil"></label>
    <label class="full">Popis problému *<textarea name="description" required rows="4" placeholder="Čo sa stalo a čo treba skontrolovať?"><?= e($data['description']) ?></textarea></label>
    <label>Priorita<select name="priority"><?php foreach ($priorities as $p): ?><option <?= $data['priority']===$p?'selected':'' ?>><?= e($p) ?></option><?php endforeach; ?></select></label>
    <label>Preferovaný termín<input type="date" name="preferred_date" value="<?= e($data['preferred_date']) ?>"></label>
    <div class="form-actions full"><button class="button button-primary" type="submit">Uložiť požiadavku</button><a class="button button-plain" href="services.php">Zrušiť</a></div>
</form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
