<?php
define('VIEW_ROOT', __DIR__); 
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Editar Utilizador';
$page_icon = 'fas fa-edit';
require_once VIEW_ROOT . '/templates/header_diretor.php';

// ... (código PHP para buscar dados do usuário) ...
$id_usuario_para_editar = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// ... (restante do código PHP) ...
?>

<div class="form-container">
    <form method="POST" action="processa_edicao_usuario.php">
        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario_para_editar; ?>">
        <h3 class="section-title">Endereço</h3>
        <div class="form-row">
            <div class="form-group"><label for="cep">CEP</label><input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($endereco['cep'] ?? ''); ?>"></div>
            <div class="form-group"><label>Logradouro</label><input type="text" name="logradouro" value="<?php echo htmlspecialchars($endereco['logradouro'] ?? ''); ?>"></div>
        </div>
        <div class="form-actions">
            <a href="javascript:history.back()" class="btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Máscara para CPF
        IMask(document.getElementById('cpf'), { mask: '000.000.000-00' });

        // Máscara para CEP
        IMask(document.getElementById('cep'), { mask: '00000-000' });

        // Máscara para Telefone
        IMask(document.getElementById('telefone'), {
            mask: [
                { mask: '(00) 0000-0000' },
                { mask: '(00) 00000-0000' }
            ]
        });
    });
</script>

<?php
require_once VIEW_ROOT . '/templates/footer.php';
?>