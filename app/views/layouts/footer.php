        <footer class="app-footer">
            <div class="footer-inner">BNGRC © <?= date('Y') ?> — ETU004038 & ETU003901</div>
        </footer>
    </main>
</div>

<script>
    document.querySelectorAll('.sidebar a').forEach(link => {
        if (link.pathname === window.location.pathname) {
            link.classList.add('active');
        }
    });
</script>
</body>
</html>
