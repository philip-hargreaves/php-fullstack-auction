
// Upload image to cloud
function initializeImageUploader(config) {
    // 1. Destructure and set defaults
    const {
        submitBtnId,
        uploaderId,
        previewContainerId,
        formId,
        hiddenContainerId,
        alertContainerId,
        uploadUrl = 'ajax/upload-image.php', // Default URL
        maxImages = 10,
        minImages = 1
    } = config;

    // 2. Get DOM Elements
    const submitBtn = document.getElementById(submitBtnId);
    const uploader = document.getElementById(uploaderId);
    const container = document.getElementById(previewContainerId);
    const form = document.getElementById(formId);
    const hiddenContainer = document.getElementById(hiddenContainerId);

    // 3. Critical Error Check
    if (!submitBtn || !form || !hiddenContainer || !uploader || !container) {
        console.error("Image Uploader Error: One or more required HTML elements are missing.", config);
        return;
    }

    // --- INTERNAL FUNCTIONS ---

    // Create the visual card element
    function createCard(url, isLoading = false) {
        const div = document.createElement('div');
        div.className = 'img-card';

        if (isLoading) {
            div.innerHTML = '<div class="p-4 text-muted">Uploading...</div>';
            return div;
        }
        return div; // Should not happen in current logic, but safe fallback
    }

    // Update card content after upload and attach events
    function updateCardWithImage(card, url) {
        card.dataset.url = url; // Store URL in data attribute
        card.innerHTML = `
            <div class="main-badge">MAIN</div>
            <img src="${url}" alt="Preview">
            <div class="img-actions">
                <button type="button" class="btn btn-secondary btn-xs btn-move-left">&lt;</button>
                <button type="button" class="btn btn-danger btn-xs btn-remove">X</button>
                <button type="button" class="btn btn-secondary btn-xs btn-move-right">&gt;</button>
            </div>
        `;

        // Attach Card Events
        card.querySelector('.btn-remove').addEventListener('click', () => card.remove());

        card.querySelector('.btn-move-left').addEventListener('click', () => {
            if (card.previousElementSibling) {
                container.insertBefore(card, card.previousElementSibling);
            }
        });

        card.querySelector('.btn-move-right').addEventListener('click', () => {
            if (card.nextElementSibling) {
                container.insertBefore(card.nextElementSibling, card);
            }
        });
    }

    // Handle AJAX Upload
    function uploadFileToCloud(file) {
        // Create placeholder
        const card = createCard(null, true);
        container.appendChild(card);

        const formData = new FormData();
        formData.append('file', file);

        fetch(uploadUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    updateCardWithImage(card, data.url);
                } else {
                    console.warn('Upload failed:', data);
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                    card.remove();
                }
            })
            .catch(error => {
                console.error('Error uploading image:', error);
                alert('Error uploading image. Please check console.');
                card.remove();
            });
    }

    // --- EVENT LISTENERS ---

    // 1. File Selection Listener
    uploader.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => uploadFileToCloud(file));
        uploader.value = ''; // Reset input
    });

    // 2. Submit Button Listener
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Stop default button behavior immediately

        // Clear previous hidden inputs
        hiddenContainer.innerHTML = '';

        const cards = container.querySelectorAll('.img-card');

        // Validation
        if (cards.length < minImages || cards.length > maxImages) {
            const errorHtml = `
                <div class="alert alert-danger shadow-sm" role="alert" id="create-auction-alert">
                    <i class="fa fa-exclamation-circle"></i>
                    Please upload at least ${minImages} and up to ${maxImages} image(s).
                </div>
            `;

            const alertBox = document.getElementById(alertContainerId);
            if (alertBox) {
                alertBox.innerHTML = errorHtml;
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Call global autoDismiss if available
                if (typeof autoDismissAlerts === 'function') {
                    autoDismissAlerts();
                }
            }
            return; // Stop submission
        }

        // Generate Hidden Inputs
        cards.forEach((card, index) => {
            const url = card.dataset.url;
            if (!url) return;

            // Image URL Input
            const inputUrl = document.createElement('input');
            inputUrl.type = 'hidden';
            inputUrl.name = `uploaded_images[${index}][image_url]`;
            inputUrl.value = url;
            hiddenContainer.appendChild(inputUrl);

            // Is Main Input
            const inputMain = document.createElement('input');
            inputMain.type = 'hidden';
            inputMain.name = `uploaded_images[${index}][is_main]`;
            inputMain.value = (index === 0) ? '1' : '0';
            hiddenContainer.appendChild(inputMain);
        });

        // Submit Form
        form.submit();
    });
}

// Watchlist Function
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

// Watchlist Function
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

// Image Gallery & Scroller Functions
function initImageGallery(imageUrls) {
    // Initialization: Hide nav buttons if there's only one image
    if (!imageUrls || imageUrls.length <= 1) {
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

    // Check if gallery elements exist
    if (!mainImage || !prevButton || !nextButton || thumbnails.length === 0) {
        console.warn("Image gallery main elements not found. Gallery may not function.");
    }

    // Define child function
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

// Automatically fades out bootstrap alerts after 3 seconds
function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(function(alert) {
        // Check for the new custom name (camelCase in JS)
        if(alert.dataset.isDismissing === "true") return;

        // Set the new custom name
        alert.dataset.isDismissing = "true";

        setTimeout(function() {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";

            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 3000);
    });
}


