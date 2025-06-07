<?php
include_once './app/Views/Partials/header.php';
include_once './app/Views/Partials/footer.php';
?>

<div class="container py-5">
    <h2 class="mb-4">Editar Produto</h2>

    <form method="post" action="index.php?c=Produto&a=salvar" class="row g-3">
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">

        <div class="col-md-4">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </div>

        <div class="col-md-2">
            <label for="preco" class="form-label">Preço</label>
            <input type="number" name="preco" id="preco" step="0.01" class="form-control" value="<?= $produto['preco'] ?>" required>
        </div>

        <div class="col-md-3">
            <label for="variacao" class="form-label">Nova Variação (opcional)</label>
            <input type="text" name="variacao_nome" id="variacao" class="form-control" placeholder="Adicionar nova variação">
        </div>

        <div class="col-md-3">
            <label for="quantidade" class="form-label">Quantidade da nova variação</label>
            <input type="number" name="quantidade" id="quantidade" class="form-control" placeholder="Quantidade para nova variação">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
            <a href="index.php?c=Produto&a=index" class="btn btn-secondary">Voltar</a>
        </div>
    </form>

    <hr>

    <h4>Variações Atuais</h4>

    <ul class="list-group">
        <?php foreach ($produto['estoque'] as $e): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($e['variacao']) ?> - Quantidade: <?= $e['quantidade'] ?>

                <form method="POST" action="index.php?c=Produto&a=comprar" class="d-inline-flex align-items-center">
                    <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                    <input type="hidden" name="variacao_id" value="<?= $e['variacao_id'] ?>">
                    <input type="number" name="quantidade" value="1" min="1" max="<?= $e['quantidade'] ?>" class="form-control form-control-sm me-2" style="width: 80px;" required>
                    <button type="submit" class="btn btn-primary btn-sm">Adicionar ao Carrinho</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>