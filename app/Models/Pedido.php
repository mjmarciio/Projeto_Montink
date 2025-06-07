<?php
class Pedido {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function criar($subtotal, $frete, $total, $cep, $endereco, $email, $itens) {
        $stmt = $this->db->prepare("INSERT INTO pedidos (subtotal, frete, total, cep, endereco, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$subtotal, $frete, $total, $cep, $endereco, $email]);
        $pedidoId = $this->db->lastInsertId();
    
        foreach ($itens as $item) {
            $stmtItem = $this->db->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, variacao_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?, ?)");
            $stmtItem->execute([
                $pedidoId,
                $item['produto_id'],
                $item['variacao_id'],
                $item['quantidade'],
                $item['preco']  
            ]);
        }
    
        return $pedidoId;
    }
}

