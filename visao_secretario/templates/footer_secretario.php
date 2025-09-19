

    <script>
        // Script para controlar os menus dropdown (se houver)
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item.has-submenu');
            menuItems.forEach((item) => {
                item.addEventListener('click', function (e) {
                    if (e.target.tagName === 'A' || e.target.closest('a')) return;
                    
                    const submenu = this.nextElementSibling;
                    if (submenu && submenu.classList.contains('submenu')) {
                        this.classList.toggle('open');
                    }
                });
            });
        });
    </script>
</body>
</html>