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