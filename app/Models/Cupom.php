<?php
class Cupom {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function validar($codigo, $subtotal) {
        $stmt = $this->db->prepare("SELECT * FROM cupons WHERE codigo = ? AND validade >= CURDATE() AND minimo <= ?");
        $stmt->execute([$codigo, $subtotal]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
