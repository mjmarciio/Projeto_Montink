<?php
require 'config/Database.php';
$db = Database::getConnection();
$dados = json_decode(file_get_contents("php://input"), true);

$id = $dados['id'] ?? null;
$status = $dados['status'] ?? '';

if ($id) {
    if (strtolower($status) === 'cancelado') {
        $stmt = $db->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        $stmt = $db->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "ID inválido"]);
}