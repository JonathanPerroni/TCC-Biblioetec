<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['files'])) {
    $dir = 'arquivoXML/';
    foreach ($_POST['files'] as $arquivo) {
        $filePath = $dir . $arquivo;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
header('Location: index.php');
exit;
?>