<?php
// ... (código PHP inicial) ...
?>
<div class="form-container">
    <form id="form-responsavel" method="POST" action="Cadastro_Responsavel.php<?php echo $is_edit_mode ? '?id=' . htmlspecialchars($responsavel['id_usuario'] ?? '') : ''; ?>">
        <h3 class="section-title">Endereço</h3>
        <div class="form-row">
            <div class="form-group"><label for="cep">CEP</label><input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($endereco['cep'] ?? ''); ?>"></div>
            <div class="form-group"><label>Logradouro</label><input type="text" name="logradouro" value="<?php echo htmlspecialchars($endereco['logradouro'] ?? ''); ?>"></div>
            <div class="form-group"><label>Número</label><input type="text" name="numero" value="<?php echo htmlspecialchars($endereco['numero'] ?? ''); ?>"></div>
        </div>
        </form>
</div>

<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        IMask(document.getElementById('cpf'), { mask: '000.000.000-00' });

        IMask(document.getElementById('cep'), { mask: '00000-000' });

        IMask(document.getElementById('telefone'), {
            mask: [ { mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' } ]
        });
    });
</script>

<?php require_once PROJECT_ROOT . '/visao_secretario/templates/footer_secretario.php'; ?>