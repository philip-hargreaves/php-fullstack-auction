<!-- Bootstrap core JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>

<!-- Auto-dismiss alerts after 3 seconds -->
<script>
    $(document).ready(function() {
        $('.alert-dismissible').delay(3000).fadeOut('slow');
        
        // Prevent blue color and underline on hover for right-section links
        $('.right-section a.nav-button').on('mouseenter mouseleave', function(e) {
            $(this).css({
                'color': 'rgb(229, 229, 229)',
                'text-decoration': 'none',
                'border-bottom': 'none'
            });
        });
    });
</script>

<!-- Modal Utility Functions -->
<script>
    function showLoginModal() {
        $('#loginModal').modal('show');
    }

    function showBecomeSellerModal() {
        $('#becomeSellerModal').modal('show');
    }

    // Make functions globally available
    window.showLoginModal = showLoginModal;
    window.showBecomeSellerModal = showBecomeSellerModal;
</script>

</body>

</html>