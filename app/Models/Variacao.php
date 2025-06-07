<?php
class Variacao {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function salvar($produto_id, $nome) {
        $stmt = $this->db->prepare("INSERT INTO variacoes (produto_id, nome) VALUES (?, ?)");
        $stmt->execute([$produto_id, $nome]);
        return $this->db->lastInsertId();
    }

    public function listarPorProduto($produto_id) {
        $stmt = $this->db->prepare("SELECT * FROM variacoes WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirPorProduto($produto_id) {
        $stmt = $this->db->prepare("DELETE FROM variacoes WHERE produto_id = ?");
        return $stmt->execute([$produto_id]);
    }

    public function excluirPorId($id) {
        $stmt = $this->db->prepare("DELETE FROM variacoes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}