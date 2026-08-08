    </div> <!-- .admin-main -->
</div> <!-- .admin-wrapper -->

<script>
    // Set active menu based on current page
    (function() {
        const currentFile = window.location.pathname.split('/').pop();
        const menuItems = document.querySelectorAll('.sidebar-menu li');
        
        menuItems.forEach(function(item) {
            const link = item.querySelector('a');
            // Pastikan link ada dan href tidak kosong/mentah
            if (link && link.getAttribute('href')) {
                const linkFile = link.getAttribute('href').split('/').pop();
                if (linkFile === currentFile) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            }
        });
    })();
</script>

</body>
</html>