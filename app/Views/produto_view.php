<?php
include_once './app/Views/Partials/header.php';
include_once './app/Views/Partials/footer.php';
?>

<div class="container py-5">
    <h2>Cadastro e Compra de Produto</h2>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="POST" action="?c=Produto&a=salvar">
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control">
        </div>

        <div class="mb-3">
            <label>Preço</label>
            <input type="number" name="preco" class="form-control" step="0.01">
        </div>

        <div class="mb-3">
            <label>Variação</label>
            <input type="text" name="variacao_nome" class="form-control">
        </div>

        <div class="mb-3">
            <label>Estoque</label>
            <input type="number" name="quantidade" class="form-control">
        </div>

        <button type="submit" name="acao" value="salvar" class="btn btn-primary">Salvar</button>
        <button type="submit" name="acao" value="comprar" class="btn btn-success">Comprar</button>

    </form>
</div>