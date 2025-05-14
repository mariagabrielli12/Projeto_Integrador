const select = document.getElementById('atuacao');
const forms = {
  bercarista: document.getElementById('form-bercarista'),
  diretor: document.getElementById('form-diretor'),
  secretario: document.getElementById('form-secretario')
};

function atualizarFormulario() {
  const valorSelecionado = select.value;
  for (let key in forms) {
    forms[key].classList.remove('active');
  }
  forms[valorSelecionado].classList.add('active');
}

select.addEventListener('change', atualizarFormulario);
window.onload = atualizarFormulario;
