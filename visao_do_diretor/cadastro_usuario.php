<?php
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
$page_title = 'Cadastro Geral de Utilizadores';
$page_icon = 'fas fa-user-plus';
require_once VIEW_ROOT . '/templates/header_diretor.php';
?>

<div class="form-container">
    <form method="POST" action="processa_cadastro_geral.php">
        <h3 class="section-title">Dados Básicos</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="id_tipo">Tipo de Utilizador*</label>
                <select id="id_tipo" name="id_tipo" required>
                    <option value="">Selecione o tipo</option>
                    <option value="2">Secretário</option>
                    <option value="3">Professor</option>
                    <option value="4">Berçarista</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Nome Completo*</label><input type="text" name="nome_completo" required></div>
            <div class="form-group"><label>CPF*</label><input type="text" name="cpf" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Email*</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Telefone</label><input type="tel" name="telefone"></div>
        </div>
        <h3 class="section-title">Credenciais de Acesso</h3>
        <div class="form-row">
            <div class="form-group"><label>Matrícula (Utilizador para Login)*</label><input type="text" name="matricula" required></div>
            <div class="form-group"><label>Senha Provisória*</label><input type="password" name="senha" required></div>
        </div>
        <div class="form-actions">
            <button type="reset" class="btn-secondary"><i class="fas fa-times"></i> Limpar</button>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Salvar Cadastro</button>
        </div>
    </form>
</div>

<?php
require_once PROJECT_ROOT . '/visao_do_diretor/templates/footer_diretor.php';
?>