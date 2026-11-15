            </div> <!-- end page-content -->
        </main>
    </div> <!-- end dashboard-wrapper -->
    
    <script>
        // Active link handling
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === window.location.pathname) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
