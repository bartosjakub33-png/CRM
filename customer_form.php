<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = db();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$customer = ['first_name'=>'','last_name'=>'','phone'=>'','email'=>'','street'=>'','city'=>'','postal_code'=>'','notes'=>''];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        http_response_code(404);
        exit('Zákazník sa nenašiel.');
    }
    $customer = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($customer) as $key) {
        if ($key !== 'id' && $key !== 'created_at') {
            $customer[$key] = trim($_POST[$key] ?? '');
        }
    }

    if ($customer['first_name'] === '') $errors[] = 'Vyplň meno.';
    if ($customer['last_name'] === '') $errors[] = 'Vyplň priezvisko.';
    if ($customer['phone'] === '') $errors[] = 'Vyplň telefónne číslo.';
    if ($customer['email'] !== '' && !filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail nemá správny formát.';

    if (!$errors) {
        $values = [
            $customer['first_name'], $customer['last_name'], $customer['phone'],
            $customer['email'] ?: null, $customer['street'] ?: null, $customer['city'] ?: null,
            $customer['postal_code'] ?: null, $customer['notes'] ?: null
        ];
        try {
            if ($id) {
                $values[] = $id;
                $stmt = $pdo->prepare('UPDATE customers SET first_name=?, last_name=?, phone=?, email=?, street=?, city=?, postal_code=?, notes=? WHERE id=?');
                $stmt->execute($values);
            } else {
                $stmt = $pdo->prepare('INSERT INTO customers (first_name,last_name,phone,email,street,city,postal_code,notes) VALUES (?,?,?,?,?,?,?,?)');
                $stmt->execute($values);
            }
            redirect('customers.php');
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $errors[] = 'Zákazník s týmto e-mailom už existuje.';
            } else {
                throw $ex;
            }
        }
    }
}

$pageTitle = $id ? 'Upraviť zákazníka' : 'Nový zákazník';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">EVIDENCIA</p><h1><?= $id ? 'Upraviť zákazníka' : 'Pridať zákazníka' ?></h1><p class="muted">Kontaktné údaje zákazníka.</p></div>
    <a class="button button-plain" href="customers.php">← Späť na zoznam</a>
</div>
<section class="panel form-panel">
    <?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
    <form method="post" class="form-grid">
        <label>Meno *<input name="first_name" required maxlength="80" value="<?= e($customer['first_name']) ?>"></label>
        <label>Priezvisko *<input name="last_name" required maxlength="80" value="<?= e($customer['last_name']) ?>"></label>
        <label>Telefón *<input name="phone" required maxlength="30" value="<?= e($customer['phone']) ?>"></label>
        <label>E-mail<input type="email" name="email" maxlength="160" value="<?= e($customer['email']) ?>"></label>
        <label>Ulica a číslo<input name="street" maxlength="160" value="<?= e($customer['street']) ?>"></label>
        <label>Mesto<input name="city" maxlength="100" value="<?= e($customer['city']) ?>"></label>
        <label>PSČ<input name="postal_code" maxlength="12" value="<?= e($customer['postal_code']) ?>"></label>
        <label class="full">Poznámky<textarea name="notes" rows="4"><?= e($customer['notes']) ?></textarea></label>
        <div class="form-actions full"><button class="button button-primary" type="submit">Uložiť zákazníka</button><a class="button button-plain" href="customers.php">Zrušiť</a></div>
    </form>
    <p class="muted form-note">Polia označené * sú povinné.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
