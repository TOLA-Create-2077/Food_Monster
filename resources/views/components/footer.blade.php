</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.submenu-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const group = this.closest('.menu-group');
            group.classList.toggle('open');
        });
    });

    document.querySelectorAll('.submenu-toggle-mini').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const next = this.nextElementSibling;
            if (next) {
                next.classList.toggle('open');
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>