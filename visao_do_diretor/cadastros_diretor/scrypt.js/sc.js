document.addEventListener('DOMContentLoaded', function () {
  const select = document.getElementById('atuacao');

  const forms = {
    bercarista: document.getElementById('form-bercarista'),
    professor: document.getElementById('form-professor'),
    secretario: document.getElementById('form-secretario')
  };

  function atualizarFormulario() {
    const valorSelecionado = select.value;
    for (let key in forms) {
      forms[key].classList.remove('active');
    }
    if (forms[valorSelecionado]) {
      forms[valorSelecionado].classList.add('active');
    }
  }

  select.addEventListener('change', atualizarFormulario);
  atualizarFormulario();
});