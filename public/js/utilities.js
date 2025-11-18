
// --------------------------------------------
// Watchlist Function
// --------------------------------------------
function addToWatchlist(auctionId) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: { functionname: 'add_to_watchlist', arguments: [auctionId] },

        success:
            function (obj, textstatus) {
                // Callback function for when call is successful and returns obj
                console.log("Success");
                var objT = obj.trim();

                if (objT == "success") {
                    $("#watch_nowatch").hide();
                    $("#watch_watching").show();
                } else {
                    var mydiv = document.getElementById("watch_nowatch");
                    mydiv.appendChild(document.createElement("br"));
                    mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
                }
            },

        error:
            function (obj, textstatus) {
                console.log("Error");
            }
    });
}

function removeFromWatchlist(auctionId) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: { functionname: 'remove_from_watchlist', arguments: [auctionId] },

        success:
            function (obj, textstatus) {
                // Callback function for when call is successful and returns obj
                console.log("Success");
                var objT = obj.trim();

                if (objT == "success") {
                    $("#watch_watching").hide();
                    $("#watch_nowatch").show();
                } else {
                    var mydiv = document.getElementById("watch_watching");
                    mydiv.appendChild(document.createElement("br"));
                    mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
                }
            },

        error:
            function (obj, textstatus) {
                console.log("Error");
            }
    }); // End of AJAX call
}


// --------------------------------------------
// Image Gallery & Scroller Function
// --------------------------------------------
function initImageGallery(imageUrls) {
    if (!imageUrls || imageUrls.length <= 1) {
        // Hide nav buttons if there's only one image
        const navElements = ['prev-image', 'next-image', 'thumbnail-viewport', 'thumb-prev', 'thumb-next'];
        navElements.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.style.display = 'none';
        });
        return;
    }

    // 1. Main Image Gallery Logic
    let currentIndex = 0;
    const mainImage = document.getElementById('main-image');
    const prevButton = document.getElementById('prev-image');
    const nextButton = document.getElementById('next-image');
    const thumbnails = document.querySelectorAll('.gallery-thumb');

    // Check if essential elements exist
    if (!mainImage || !prevButton || !nextButton || thumbnails.length === 0) {
        console.warn("Image gallery main elements not found. Gallery may not function.");
        // Don't return, scroller might still work
    }

    function showImage(index) {
        if (index >= imageUrls.length) index = 0;
        if (index < 0) index = imageUrls.length - 1;

        if(mainImage) mainImage.src = imageUrls[index];
        currentIndex = index;

        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active-thumb', i === currentIndex);
        });

        // New function call to sync scroller
        centerThumbnailInView(index);
    }

    if(prevButton) prevButton.addEventListener('click', () => showImage(currentIndex - 1));
    if(nextButton) nextButton.addEventListener('click', () => showImage(currentIndex + 1));

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => {
            showImage(parseInt(thumb.dataset.index, 10));
        });
    });


    // 2. Thumbnail Scroller Logic
    const viewport = document.getElementById('thumbnail-viewport');
    const thumbContainer = document.getElementById('thumbnail-container');
    const thumbPrev = document.getElementById('thumb-prev');
    const thumbNext = document.getElementById('thumb-next');

    // Check if scroller elements exist
    if (!viewport || !thumbContainer || !thumbPrev || !thumbNext || !thumbnails[0]) {
        console.warn("Thumbnail scroller elements not found. Scroller will not initialize.");
        return; // Exit if scroller can't work
    }

    let scrollAmount = 0;
    const thumbScrollWidth = thumbnails[0].offsetWidth + 8; // 8px margin

    function updateThumbNav() {
        // Show/hide prev button
        thumbPrev.style.display = (scrollAmount <= 0) ? 'none' : 'block';

        // Show/hide next button
        const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
        thumbNext.style.display = (scrollAmount >= maxScroll) ? 'none' : 'block';
    }

    thumbPrev.addEventListener('click', () => {
        const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
        scrollAmount -= thumbScrollWidth * 3; // Scroll by 3 images
        if (scrollAmount < 0) scrollAmount = 0;
        if (scrollAmount > maxScroll && maxScroll > 0) scrollAmount = maxScroll; // prevent overscroll
        thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
        updateThumbNav();
    });

    thumbNext.addEventListener('click', () => {
        const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
        scrollAmount += thumbScrollWidth * 3; // Scroll by 3 images
        if (scrollAmount > maxScroll) scrollAmount = maxScroll;
        if (scrollAmount < 0) scrollAmount = 0; // prevent underscroll
        thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
        updateThumbNav();
    });

    // This new function centers the active thumb in the scroller
    function centerThumbnailInView(index) {
        const activeThumb = thumbnails[index];
        if (!activeThumb) return;

        const viewportWidth = viewport.clientWidth;
        const thumbLeft = activeThumb.offsetLeft;
        const thumbWidth = activeThumb.offsetWidth;

        // Calculate new scroll amount to center the thumb
        let newScroll = thumbLeft - (viewportWidth / 2) + (thumbWidth / 2);

        const maxScroll = thumbContainer.scrollWidth - viewportWidth;
        if (newScroll < 0) newScroll = 0;
        if (newScroll > maxScroll) newScroll = maxScroll;

        scrollAmount = newScroll;
        thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
        updateThumbNav();
    }

    // Initial check
    updateThumbNav();
}