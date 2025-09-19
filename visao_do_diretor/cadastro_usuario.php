<?php
define('VIEW_ROOT', __DIR__); 
define('PROJECT_ROOT', dirname(VIEW_ROOT)); 

$page_title = 'Cadastrar Novo Funcionário';
$page_icon = 'fas fa-user-plus';
require_once VIEW_ROOT . '/templates/header_diretor.php';
?>

<div class="form-container">
    <form method="POST" action="processa_cadastro_geral.php">
        <h3 class="section-title">Dados do Novo Funcionário</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="id_tipo">Função*</label>
                <select id="id_tipo" name="id_tipo" required>
                    <option value="">Selecione o tipo</option>
                    <option value="2">Secretário</option>
                    <option value="3">Professor</option>
                    <option value="4">Berçarista</option>
                </select>
            </div>
             <div class="form-group"><label>Nome Completo*</label><input type="text" name="nome_completo" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label for="cpf">CPF*</label><input type="text" id="cpf" name="cpf" required></div>
            <div class="form-group"><label for="email">Email*</label><input type="email" name="email" required></div>
        </div>
         <div class="form-row">
            <div class="form-group"><label for="telefone">Telefone</label><input type="tel" id="telefone" name="telefone"></div>
        </div>

        <h3 class="section-title">Endereço</h3>
        <div class="form-row">
            <div class="form-group"><label for="cep">CEP</label><input type="text" id="cep" name="cep"></div>
            <div class="form-group"><label>Logradouro</label><input type="text" name="logradouro"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Número</label><input type="text" name="numero"></div>
            <div class="form-group"><label>Bairro</label><input type="text" name="bairro"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Cidade</label><input type="text" name="cidade"></div>
            <div class="form-group"><label>Estado</label><input type="text" name="estado"></div>
        </div>

        <h3 class="section-title">Credenciais de Acesso</h3>
        <div class="form-row">
            <div class="form-group"><label>Matrícula (Login)*</label><input type="text" name="matricula" required></div>
            <div class="form-group"><label>Senha Provisória*</label><input type="password" name="senha" required></div>
        </div>
        <div class="form-actions">
            <a href="javascript:history.back()" class="btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Salvar Cadastro</button>
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