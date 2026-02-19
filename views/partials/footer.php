</main>

<div class="container">
    <footer class="pt-6 mt-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between py-4 my-4 border-top">
            <p>&copy; <?= date('Y') ?> AuctivitySite, Inc. All rights reserved.</p>
        </div>
    </footer>
</div>


<!-- Bootstrap core JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<!-- add time to trick the cache that this is a new file, to get the updated version -->
<script src="/js/utilities.js?v=<?php echo time(); ?>"></script>


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
<!-- Popup outbid notification -->
<script>
    function fetchNotifications()
    {
        fetch('/notifications', {
        method: 'GET',
            credentials: 'same-origin'
        })
            .then(function(response) {
                return response.json();
            })
            .then(function(notifications) {
                notifications.forEach(function(n) {
                    showPopup(n);
                });
            })
            .catch(function(err) {
                console.error('Error fetching notifications:', err);
            });
    }

    //calls fetchNotification every 30 seconds in the background
    setInterval(fetchNotifications, 30000);

    //extracts message from notification and displays it
    function showPopup(notification)
    {
        const div = document.createElement('div');
        div.className = 'popup';
        div.innerText = notification.message;
        document.body.appendChild(div);

        //Marks messages as having been sent
        fetch('/notifications', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: notification.notificationId })
        });

        requestAnimationFrame(() => div.classList.add('show'));

        //Message disappears after 4 seconds
        setTimeout(function() {
            div.remove();
        }, 4000);
    }
</script>

</body>
</html>