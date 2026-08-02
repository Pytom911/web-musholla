<footer class="text-center text-muted small py-3 border-top">
    &copy; 2026 Sistem Informasi Musholla &middot; SMK Negeri 1 Kraksaan
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript Toggle Sidebar & Responsif -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainWrapper = document.getElementById('mainWrapper');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarMenu = document.querySelector('.sidebar-menu');

        if (!sidebarToggle || !sidebar || !mainWrapper || !sidebarOverlay) {
            return;
        }

        if (sidebarMenu) {

        // Kembalikan posisi scroll
        const saved = sessionStorage.getItem('sidebar-scroll');

        if (saved) {
        sidebarMenu.scrollTop = parseInt(saved);
        }

        // Simpan setiap kali sidebar di-scroll
        sidebarMenu.addEventListener('scroll', function () {
        sessionStorage.setItem('sidebar-scroll', sidebarMenu.scrollTop);
        });

        }

        function toggleSidebar() {
            if (window.innerWidth < 992) {
                // Perangkat Mobile/Tablet
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            } else {
                // Perangkat Desktop
                sidebar.classList.toggle('hidden');
                mainWrapper.classList.toggle('expanded');
            }
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Menutup sidebar mobile saat memilih menu (kecuali dropdown)
        document.querySelectorAll('.sidebar-link:not([data-bs-toggle])').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });

        // Menyesuaikan tampilan saat resize jendela browser
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            }
        });
    });
</script>
</body>
</html>