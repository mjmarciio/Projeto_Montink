<?php
class Produto {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarTodos() {
        return $this->db->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvar($nome, $preco) {
        $stmt = $this->db->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
        $stmt->execute([$nome, $preco]);
        return $this->db->lastInsertId();
    }

    public function atualizar($id, $nome, $preco) {
        $stmt = $this->db->prepare("UPDATE produtos SET nome=?, preco=? WHERE id=?");
        return $stmt->execute([$nome, $preco, $id]);
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("DELETE FROM produtos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
