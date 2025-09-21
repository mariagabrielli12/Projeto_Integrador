<?php
// ---- COLOQUE A SENHA QUE VOCÊ DESEJA AQUI ----
$senha_que_o_secretario_vai_usar = 'secretario123';

// Este código irá gerar o hash seguro
$hash_da_senha = password_hash($senha_que_o_secretario_vai_usar, PASSWORD_DEFAULT);

echo "Use esta senha para fazer o login: <strong>" . $senha_que_o_secretario_vai_usar . "</strong><br><br>";
echo "Copie e cole este HASH no comando SQL abaixo:<br>";
echo '<textarea rows="3" cols="70" readonly>' . $hash_da_senha . '</textarea>';
?>

<?php
// ---- COLOQUE A SENHA QUE O PROFESSOR VAI USAR AQUI ----
$senha_que_o_professor_vai_usar = 'professor123';

// Este código irá gerar o hash seguro
$hash_da_senha = password_hash($senha_que_o_professor_vai_usar, PASSWORD_DEFAULT);

echo "Use esta senha para fazer o login: <strong>" . $senha_que_o_professor_vai_usar . "</strong><br><br>";
echo "Copie e cole este HASH no comando SQL abaixo:<br>";
echo '<textarea rows="3" cols="70" readonly>' . $hash_da_senha . '</textarea>';
?>

<?php
// ---- COLOQUE A SENHA QUE O DIRETOR VAI USAR AQUI ----
$senha_que_o_diretor_vai_usar = 'diretor123';

// Este código irá gerar o hash seguro
$hash_da_senha = password_hash($senha_que_o_diretor_vai_usar, PASSWORD_DEFAULT);

echo "Use esta senha para fazer o login: <strong>" . $senha_que_o_diretor_vai_usar . "</strong><br><br>";
echo "Copie e cole este HASH no comando SQL abaixo:<br>";
echo '<textarea rows="3" cols="70" readonly>' . $hash_da_senha . '</textarea>';
?>




<?php
// ---- COLOQUE A SENHA QUE O RESPONSÁVEL VAI USAR AQUI ----
$senha_do_responsavel = 'senharesponsavel123';

// Este código irá gerar o hash seguro
$hash_da_senha = password_hash($senha_do_responsavel, PASSWORD_DEFAULT);

echo "Use esta senha para fazer o login: <strong>" . $senha_do_responsavel . "</strong><br><br>";
echo "Copie e cole este HASH no comando SQL abaixo:<br>";
echo '<textarea rows="3" cols="70" readonly>' . $hash_da_senha . '</textarea>';
?>

<?php

// --- DEFINA A SENHA QUE VOCÊ QUER USAR AQUI ---
$senha_para_o_diretor = 'diretor123';
$senha_para_o_secretario = 'secretario123';


// --- GERAÇÃO DOS HASHES ---

// Hash para o Diretor
$hash_diretor = password_hash($senha_para_o_diretor, PASSWORD_DEFAULT);

// Hash para o Secretário
$hash_secretario = password_hash($senha_para_o_secretario, PASSWORD_DEFAULT);


// --- EXIBIÇÃO NA TELA ---

echo "<h1>Hashes de Senha Gerados com Sucesso!</h1>";
echo "<p>Use os hashes abaixo para atualizar seu banco de dados. A senha do Diretor será <b>'".$senha_para_o_diretor."'</b> e a do Secretário será <b>'".$senha_para_o_secretario."'</b>.</p>";

echo "<h3>Para o Diretor (matrícula: diretor01):</h3>";
echo "<textarea rows='3' cols='80' readonly onclick='this.select();'>".$hash_diretor."</textarea>";
echo "<hr>";

echo "<h3>Para o Secretário (matrícula: secretario01):</h3>";
echo "<textarea rows='3' cols='80' readonly onclick='this.select();'>".$hash_secretario."</textarea>";

?>


<br><br>
<br>
<br>

<?php

// --- DEFINA A SENHA QUE VOCÊ QUER USAR AQUI ---
$senha_para_novo_responsavel = 'senha123';


// --- GERAÇÃO DO HASH ---
$hash_responsavel = password_hash($senha_para_novo_responsavel, PASSWORD_DEFAULT);


// --- EXIBIÇÃO NA TELA ---
echo "<h1>Hash de Senha Gerado com Sucesso!</h1>";
echo "<p>Use a senha <b>'".$senha_para_novo_responsavel."'</b> para fazer o login do novo responsável.</p>";

echo "<hr>";
echo "<h3>Para o Novo Responsável:</h3>";
echo "<p>Copie o hash completo abaixo para usar no próximo passo:</p>";
echo "<textarea rows='3' cols='80' readonly onclick='this.select();'>".$hash_responsavel."</textarea>";

?>