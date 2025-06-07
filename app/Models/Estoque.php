<?php
class Estoque {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function salvar($variacao_id, $quantidade) {
        $stmt = $this->db->prepare("INSERT INTO estoque (variacao_id, quantidade) VALUES (?, ?)");
        return $stmt->execute([$variacao_id, $quantidade]);
    }

    public function listarPorProduto($produto_id) {
        $stmt = $this->db->prepare("SELECT e.id, v.nome AS variacao, e.quantidade FROM estoque e
                                      JOIN variacoes v ON v.id = e.variacao_id
                                      WHERE v.produto_id = ?");
        $stmt->execute([$produto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirPorProduto($produto_id) {
        $stmt = $this->db->prepare("DELETE e FROM estoque e
                                      JOIN variacoes v ON v.id = e.variacao_id
                                      WHERE v.produto_id = ?");
        return $stmt->execute([$produto_id]);
    }

    public function excluirPorId($id) {
        $stmt = $this->db->prepare("DELETE FROM estoque WHERE id = ?");
        return $stmt->execute([$id]);
    }
}