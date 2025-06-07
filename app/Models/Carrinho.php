<?php
class Carrinho {
    public static function adicionar($produtoId, $variacaoId, $nome, $preco, $quantidade) {
        session_start();
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        $idUnico = $produtoId . '_' . $variacaoId;

        if (isset($_SESSION['carrinho'][$idUnico])) {
            $_SESSION['carrinho'][$idUnico]['quantidade'] += $quantidade;
        } else {
            $_SESSION['carrinho'][$idUnico] = [
                'produto_id' => $produtoId,
                'variacao_id' => $variacaoId,
                'nome' => $nome,
                'preco' => $preco,
                'quantidade' => $quantidade
            ];
        }
    }

    public static function remover($idUnico) {
        session_start();
        unset($_SESSION['carrinho'][$idUnico]);
    }

    public static function limpar() {
        session_start();
        $_SESSION['carrinho'] = [];
    }

    public static function listar() {
        session_start();
        return $_SESSION['carrinho'] ?? [];
    }

    public static function subtotal() {
        $itens = self::listar();
        $total = 0;
        foreach ($itens as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        return $total;
    }
}
