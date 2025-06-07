<?php
class ProdutoController
{
    public function index()
    {
        $mensagem = $_SESSION['mensagem'] ?? '';
        unset($_SESSION['mensagem']);

        require 'app/Views/produto_view.php';
    }

    public function salvar()
    {
        $acao = $_POST['acao'] ?? '';

        if ($acao === 'variacao') {
            require 'app/Views/produto_form.php';
            exit;
        }

        $nome = trim($_POST['nome']);
        $preco = (float) $_POST['preco'];
        $variacao = trim($_POST['variacao_nome']);
        $quantidade = (int) $_POST['quantidade'];

        $db = Database::getConnection();

        if ($acao === 'salvar') {
            // Salvar produto novo
            $db->beginTransaction();
            try {
                // Inserir produto
                $stmt = $db->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
                $stmt->execute([$nome, $preco]);
                $produtoId = $db->lastInsertId();

                // Inserir variação
                $stmt = $db->prepare("INSERT INTO variacoes (produto_id, nome) VALUES (?, ?)");
                $stmt->execute([$produtoId, $variacao]);
                $variacaoId = $db->lastInsertId();

                // Inserir estoque
                $stmt = $db->prepare("INSERT INTO estoque (variacao_id, quantidade) VALUES (?, ?)");
                $stmt->execute([$variacaoId, $quantidade]);

                $db->commit();

                $_SESSION['mensagem'] = "Produto salvo com sucesso!";
                header("Location: index.php?c=Produto&a=index");
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['mensagem'] = "Erro ao salvar produto: " . $e->getMessage();
                header("Location: index.php?c=Produto&a=index");
                exit;
            }
        }

        if ($acao === 'comprar') {
            // Verificar se produto+variação existe no banco antes de adicionar ao carrinho
            $stmt = $db->prepare(
                "SELECT p.id as produto_id, p.nome as nome_produto, p.preco, v.id as variacao_id, v.nome as nome_variacao, e.quantidade as estoque
                FROM produtos p
                JOIN variacoes v ON p.id = v.produto_id
                JOIN estoque e ON v.id = e.variacao_id
                WHERE p.nome = ? AND p.preco = ? AND v.nome = ?"
            );
            $stmt->execute([$nome, $preco, $variacao]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto) {
                // Produto existe, adiciona ao carrinho com quantidade 1
                if ($produto['estoque'] < 1) {
                    $_SESSION['mensagem'] = "Produto sem estoque disponível para compra.";
                } else {
                    $_SESSION['carrinho'][] = [
                        'produto_id' => $produto['produto_id'],
                        'variacao_id' => $produto['variacao_id'],
                        'nome' => $produto['nome_produto'],
                        'variacao' => $produto['nome_variacao'],
                        'quantidade' => 1,
                        'preco' => $produto['preco']
                    ];
                    $_SESSION['mensagem'] = "Produto adicionado ao carrinho.";
                    header("Location: index.php?c=Carrinho&a=index");
                    exit;
                }
            } else {
                // Produto não existe
                $_SESSION['mensagem'] = "Produto não existente.";
            }

            header("Location: index.php?c=Produto&a=index");
            exit;
        }
    }
    public function editar()
    {
        $produtoId = $_GET['id'] ?? null;

        if (!$produtoId) {
            $_SESSION['mensagem'] = "Produto não encontrado.";
            header("Location: index.php?c=Produto&a=index");
            exit;
        }

        $db = Database::getConnection();

        // Buscar produto
        $stmt = $db->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$produtoId]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            $_SESSION['mensagem'] = "Produto não encontrado.";
            header("Location: index.php?c=Produto&a=index");
            exit;
        }

        // Buscar variações e estoque
        $stmt = $db->prepare("
            SELECT v.id AS variacao_id, v.nome AS variacao, e.quantidade
            FROM variacoes v
            LEFT JOIN estoque e ON e.variacao_id = v.id
            WHERE v.produto_id = ?
        ");
        $stmt->execute([$produtoId]);
        $estoque = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $produto['estoque'] = $estoque;

        // Agora carrega a view e passa $produto para ela
        require 'app/Views/produto_form.php';
    }


    public function comprar()
    {
        $produtoId = $_POST['produto_id'] ?? null;
        $variacaoId = $_POST['variacao_id'] ?? null;
        $quantidade = $_POST['quantidade'] ?? 1;

        if (!$produtoId || !$variacaoId) {
            $_SESSION['mensagem'] = "Produto inválido.";
            header("Location: index.php?c=Produto&a=index");
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.nome AS produto_nome, p.preco, v.nome AS variacao_nome 
                              FROM produtos p
                              JOIN variacoes v ON v.produto_id = p.id
                              WHERE p.id = ? AND v.id = ?");
        $stmt->execute([$produtoId, $variacaoId]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            $_SESSION['mensagem'] = "Produto não encontrado.";
            header("Location: index.php?c=Produto&a=index");
            exit;
        }

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        $existeNoCarrinho = false;
        foreach ($_SESSION['carrinho'] as &$item) {
            if ($item['produto_id'] == $produtoId && $item['variacao_id'] == $variacaoId) {
                $item['quantidade'] += $quantidade;
                $existeNoCarrinho = true;
                break;
            }
        }
        unset($item);

        if (!$existeNoCarrinho) {
            $_SESSION['carrinho'][] = [
                'produto_id' => $produtoId,
                'variacao_id' => $variacaoId,
                'nome' => $produto['produto_nome'],
                'variacao' => $produto['variacao_nome'],
                'quantidade' => $quantidade,
                'preco' => $produto['preco'],
            ];
        }

        $_SESSION['mensagem'] = "Produto adicionado ao carrinho.";
        header("Location: index.php?c=Carrinho&a=index");
        exit;
    }

    public function validar(string $codigo, float $subtotal)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM cupons WHERE codigo = ?");
        $stmt->execute([$codigo]);
        $cupom = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cupom && $subtotal >= 50.00) {
            return $cupom;
        }

        return null;
    }

    public function limparCarrinho()
    {
        unset($_SESSION['carrinho']);
        unset($_SESSION['cupom_aplicado']);
        $_SESSION['mensagem'] = 'Seu carrinho foi esvaziado';
        header("Location: index.php?c=Produto&a=index");
        exit;
    }
}
