    </div>
</main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'bottom' },
            dismissible: true
        });

        <?php use App\Services\Session; ?>

        <?php if (Session::hasFlash('success')): ?>
            notyf.success("<?= addslashes(Session::getFlash('success')) ?>");
        <?php endif; ?>

        <?php if (Session::hasFlash('error')): ?>
            notyf.error("<?= addslashes(Session::getFlash('error')) ?>");
        <?php endif; ?>

        <?php if (Session::hasFlash('info')): ?>
            notyf.open({
                type: 'info',
                message: "<?= addslashes(Session::getFlash('info')) ?>",
                background: '#3b82f6', // Tailwind Blue
                icon: { className: 'material-symbols-outlined', tagName: 'i', text: 'info' }
            });
        <?php endif; ?>
    });
</script>

</body>

</html>
