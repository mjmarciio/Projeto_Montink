<?php 
require_once __DIR__ . '/../utils/EmailSender.php';
require_once __DIR__ . '/../models/Cupom.php';
require_once __DIR__ . '/../models/Pedido.php';

class CarrinhoController
{
    public function index()
    {
        $carrinho = $_SESSION['carrinho'] ?? [];

        $subtotal = 0;
        foreach ($carrinho as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }

        // Aplica cupom (se houver na sessão)
        $cupom = $_SESSION['cupom_aplicado'] ?? null;
        $desconto = 0;
        $percentualDesconto = 0;

        if ($cupom) {
            $percentualDesconto = floatval($cupom['desconto']);
            $desconto = ($percentualDesconto / 100) * $subtotal;
        }

        $subtotalComDesconto = $subtotal - $desconto;

        if ($subtotalComDesconto >= 52.00 && $subtotalComDesconto <= 166.59) {
            $frete = 15.00;
        } elseif ($subtotalComDesconto > 200.00) {
            $frete = 0.00;
        } else {
            $frete = 20.00;
        }

        $total = $subtotalComDesconto + $frete;

        require 'app/Views/carrinho_view.php';
    }

    public function aplicarCupom()
    {
        $codigo = $_POST['cupom'] ?? '';
        $carrinho = $_SESSION['carrinho'] ?? [];

        $subtotal = 0;
        foreach ($carrinho as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }

        $cupomModel = new Cupom();
        $cupom = $cupomModel->validar($codigo, $subtotal);

        if ($cupom) {
            $_SESSION['cupom_aplicado'] = $cupom;
            $_SESSION['mensagem_cupom'] = "Cupom '{$cupom['codigo']}' aplicado com sucesso!";
        } else {
            unset($_SESSION['cupom_aplicado']);
            $_SESSION['mensagem_cupom'] = "Cupom inválido ou não aplicável.";
        }

        header("Location: index.php?c=Carrinho&a=index");
        exit;
    }

    public function finalizar()
    {
        $cep = $_POST['cep'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $email = $_POST['email'] ?? '';
        $cupomDigitado = $_POST['cupom'] ?? '';
        $carrinho = $_SESSION['carrinho'] ?? [];

        if (empty($carrinho)) {
            $_SESSION['mensagem'] = 'Seu carrinho está vazio.';
            header("Location: index.php?c=Produto&a=index");
            exit;
        }

        $db = Database::getConnection();

        foreach ($carrinho as $item) {
            $stmt = $db->prepare("SELECT p.id AS produto_id, v.id AS variacao_id
                                  FROM produtos p
                                  JOIN variacoes v ON v.produto_id = p.id
                                  WHERE p.id = ? AND v.id = ?");
            $stmt->execute([$item['produto_id'], $item['variacao_id']]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existe) {
                $_SESSION['mensagem'] = "Produto '{$item['nome']}' com variação '{$item['variacao']}' não está disponível.";
                header("Location: index.php?c=Carrinho&a=index");
                exit;
            }
        }

        $subtotal = 0;
        foreach ($carrinho as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }

        // Verifica novamente o cupom (pode já estar na sessão)
        $cupomModel = new Cupom();
        $cupom = $_SESSION['cupom_aplicado'] ?? $cupomModel->validar($cupomDigitado, $subtotal);

        $percentualDesconto = $cupom ? floatval($cupom['desconto']) : 0;
        $desconto = ($percentualDesconto / 100) * $subtotal;
        $subtotalComDesconto = $subtotal - $desconto;

        if ($subtotalComDesconto >= 52.00 && $subtotalComDesconto <= 166.59) {
            $frete = 15.00;
        } elseif ($subtotalComDesconto > 200.00) {
            $frete = 0.00;
        } else {
            $frete = 20.00;
        }

        $total = $subtotalComDesconto + $frete;

        $pedidoModel = new Pedido();
        $pedidoId = $pedidoModel->criar($subtotalComDesconto, $frete, $total, $cep, $endereco, $email, $carrinho);

        $mensagem = "<h3>Pedido #$pedidoId confirmado</h3><p>Endereço: $endereco</p><p>Total: R$" . number_format($total, 2, ',', '.') . "</p>";
        EmailSender::enviar($email, "Confirmação do Pedido #$pedidoId", $mensagem);

        unset($_SESSION['carrinho']);
        unset($_SESSION['cupom_aplicado']);

        $_SESSION['mensagem'] = "Pedido #$pedidoId finalizado com sucesso!";
        header("Location: index.php?c=Produto&a=index");
        exit;
    }

    public function limpar()
    {
        unset($_SESSION['carrinho']);
        unset($_SESSION['cupom_aplicado']);
        $_SESSION['mensagem'] = 'Seu carrinho foi esvaziado';
        header("Location: index.php?c=Produto&a=index");
        exit;
    }
}
