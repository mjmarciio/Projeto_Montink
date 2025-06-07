<?php
include_once './app/Views/Partials/header.php';
include_once './app/Views/Partials/footer.php';
?>

<div class="container py-5">
    <h2>Carrinho de Compras</h2>

    <?php if (!empty($_SESSION['mensagem_cupom'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($_SESSION['mensagem_cupom']) ?>
        </div>
        <?php unset($_SESSION['mensagem_cupom']); ?>
    <?php endif; ?>

    <?php if (empty($carrinho)): ?>
        <p>Seu carrinho está vazio.</p>
    <?php else: ?>
        <form method="POST" action="?c=Carrinho&a=finalizar">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Variação</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrinho as $index => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nome']) ?></td>
                            <td><?= htmlspecialchars($item['variacao']) ?></td>
                            <td><?= $item['quantidade'] ?></td>
                            <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                            <td>
                                <a href="?c=Carrinho&a=limpar" class="btn btn-danger ms-2">Limpar Carrinho</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p><strong>Subtotal:</strong> R$ <?= number_format($subtotal, 2, ',', '.') ?></p>

            <?php if ($desconto > 0): ?>
                <p><strong>Desconto (<?= number_format($percentualDesconto, 2) ?>%):</strong> -R$ <?= number_format($desconto, 2, ',', '.') ?></p>
            <?php endif; ?>

            <p><strong>Frete:</strong> R$ <?= number_format($frete, 2, ',', '.') ?></p>
            <p><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>

            <div class="mb-3">
                <label>CEP</label>
                <input type="text" name="cep" id="cep" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Endereço</label>
                <input type="text" name="endereco" id="endereco" class="form-control" readonly required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Finalizar Pedido</button>
        </form>

        <hr>

        <!-- Formulário separado para aplicar cupom -->
        <form method="POST" action="?c=Carrinho&a=aplicarCupom" class="mt-4">
            <div class="mb-3">
                <label>Cupom de Desconto</label>
                <input type="text" name="cupom" class="form-control" 
                    value="<?= htmlspecialchars($_SESSION['cupom_aplicado']['codigo'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Aplicar Cupom</button>
        </form>

    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cep').on('blur', function() {
                const cep = $(this).val().replace(/\D/g, '');
                if (cep.length === 8) {
                    $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                        if (!data.erro) {
                            $('#endereco').val(data.logradouro + ', ' + data.bairro + ', ' + data.localidade + '/' + data.uf);
                        }
                    });
                }
            });
        });
    </script>
</div>
