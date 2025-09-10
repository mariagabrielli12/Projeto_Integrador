
<div class="footer">
      <div class="footer-path"><?php echo isset($breadcrumb) ? htmlspecialchars($breadcrumb) : 'Portal do Professor'; ?></div>
      <div>Copyright © 2025 Creche Feliz. Todos os direitos reservados.</div>
    </div>
  </div> 
  
  
  <script>
    // Script para controlar os menus dropdown
    document.addEventListener('DOMContentLoaded', function() {
      // Abre o menu pai do item ativo
      const activeMenuItem = document.querySelector('.sidebar .menu-item.active');
      if (activeMenuItem) {
          const parentSubmenu = activeMenuItem.closest('.submenu');
          if (parentSubmenu) {
              parentSubmenu.classList.add('open');
              parentSubmenu.previousElementSibling.classList.add('open');
          }
      }
      
      const menuItems = document.querySelectorAll('.menu-item.has-submenu');
      menuItems.forEach((item) => {
        item.addEventListener('click', function(e) {
            // Se o alvo for o link, não faz nada
            if (e.target.tagName === 'A' || e.target.closest('a')) return;

            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                this.classList.toggle('open');
                submenu.classList.toggle('open');
            }
        });
      });
    });
  </script>
  <?php
  // Se a página precisar de um JS extra, ele será incluído aqui
  if (isset($extra_js)) {
      echo $extra_js;
  }
  ?>
</body>
</html>