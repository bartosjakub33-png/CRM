<?php
$pageTitle = $pageTitle ?? 'CRM';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | GardenCare CRM</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">✳</span> GardenCare <small>CRM</small></a>
    <nav>
        <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">Prehľad</a>
        <a class="<?= in_array($currentPage, ['customers.php','customer_form.php','customer_view.php','garden_form.php'], true) ? 'active' : '' ?>" href="customers.php">Zákazníci</a>
        <a class="<?= in_array($currentPage, ['services.php','service_form.php'], true) ? 'active' : '' ?>" href="services.php">Servis</a>
    </nav>
</header>
<main class="container">
