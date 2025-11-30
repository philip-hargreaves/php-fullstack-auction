
// Upload image to cloud
function initializeImageUploader(config) {
    const {
        submitBtnId, uploaderId, previewContainerId, formId, hiddenContainerId, alertContainerId,
        uploadUrl = 'ajax/upload-image.php',
        maxImages = 10,
        minImages = 1,
        initialImages = [] // <--- NEW: Accept existing images array
    } = config;

    const submitBtn = document.getElementById(submitBtnId);
    const uploader = document.getElementById(uploaderId);
    const container = document.getElementById(previewContainerId);
    const form = document.getElementById(formId);
    const hiddenContainer = document.getElementById(hiddenContainerId);
    console.log(initialImages);
    if (!submitBtn || !form || !hiddenContainer || !uploader || !container) return;

    // --- REUSABLE HELPER: Create the Card DOM Element ---
    function renderImageCard(url) {
        const div = document.createElement('div');
        div.className = 'img-card';
        div.dataset.url = url; // Important: Store URL for submission logic

        div.innerHTML = `
            <div class="main-badge">MAIN</div>
            <img src="${url}" alt="Preview">
            <div class="img-actions">
                <button type="button" class="btn btn-secondary btn-xs btn-move-left">&lt;</button>
                <button type="button" class="btn btn-danger btn-xs btn-remove">X</button>
                <button type="button" class="btn btn-secondary btn-xs btn-move-right">&gt;</button>
            </div>
        `;

        // Attach Events
        div.querySelector('.btn-remove').addEventListener('click', () => div.remove());

        div.querySelector('.btn-move-left').addEventListener('click', () => {
            if (div.previousElementSibling) container.insertBefore(div, div.previousElementSibling);
        });

        div.querySelector('.btn-move-right').addEventListener('click', () => {
            if (div.nextElementSibling) container.insertBefore(div.nextElementSibling, div);
        });

        return div;
    }

    // --- LOGIC: Upload New File ---
    function uploadFileToCloud(file) {
        // 1. Create Loading Card
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'img-card';
        loadingDiv.innerHTML = '<div class="p-4 text-muted">Uploading...</div>';
        container.appendChild(loadingDiv);

        // 2. AJAX
        const formData = new FormData();
        formData.append('file', file);

        fetch(uploadUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.url) {
                    // 3. Replace Loading Card with Real Card
                    const newCard = renderImageCard(data.url);
                    container.replaceChild(newCard, loadingDiv);
                } else {
                    alert('Upload failed');
                    loadingDiv.remove();
                }
            })
            .catch(err => {
                console.error(err);
                loadingDiv.remove();
            });
    }

    // --- INITIALIZATION: Load Existing Images ---

    if (initialImages && initialImages.length > 0) {
        initialImages.forEach(url => {
            const card = renderImageCard(url);
            container.appendChild(card);
        });
    }

    // --- LISTENERS ---
    uploader.addEventListener('change', (e) => {
        Array.from(e.target.files).forEach(file => uploadFileToCloud(file));
        uploader.value = '';
    });

    submitBtn.addEventListener('click', (e) => {
        e.preventDefault();
        hiddenContainer.innerHTML = '';
        const cards = container.querySelectorAll('.img-card');

        // Validation
        if (cards.length < minImages || cards.length > maxImages) {
            // ... (Your existing validation alert logic here) ...
            alert(`Please upload between ${minImages} and ${maxImages} images.`);
            return;
        }

        // Generate Hidden Inputs (Works for BOTH new and existing images)
        cards.forEach((card, index) => {
            const url = card.dataset.url;

            // URL Input
            const inputUrl = document.createElement('input');
            inputUrl.type = 'hidden';
            inputUrl.name = 'auction_image_urls[]';
            inputUrl.value = url;
            hiddenContainer.appendChild(inputUrl);
        });

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

// Initialize Category Selector in Create Auction Page
function initializeCategorySelector(config) {
    // 1. Destructure Config
    const {
        selectorId,
        backBtnId,
        hiddenInputId,
        breadcrumbsId,
        treeData,
        initialPath
    } = config;

    // 2. Get DOM Elements
    const selector = document.getElementById(selectorId);
    const backBtn = document.getElementById(backBtnId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const breadcrumbs = document.getElementById(breadcrumbsId);

    if (!selector || !backBtn || !hiddenInput || !breadcrumbs) return;

    let currentLevelCategories = treeData;
    let parentStack = [];

    // --- CORE FUNCTION: Render the dropdown ---
    function renderList(list, selectedValue = null, parentLabel = null) {
        // 1. Determine top text
        let topOptionText = parentLabel ? `Selected: ${parentLabel}` : 'Select a Category...';

        // 2. Set the placeholder
        selector.innerHTML = `<option value="">${topOptionText}</option>`;

        // 3. Render the options
        list.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;

            if (cat.children && cat.children.length > 0) {
                option.textContent += ' >';
            }

            // 4. Selection Logic (Use loose equality '==' for string/int safety)
            if (selectedValue && cat.id == selectedValue) {
                option.selected = true;
            }

            selector.appendChild(option);
        });

        // 5. Update UI
        backBtn.style.display = parentStack.length > 0 ? 'inline-block' : 'none';
        updateBreadcrumbs();
    }

    // --- EVENT: User selects a category ---
    selector.addEventListener('change', function() {
        const selectedId = this.value;
        if (!selectedId) return;

        const selectedObj = currentLevelCategories.find(c => c.id == selectedId);

        if (selectedObj && selectedObj.children && selectedObj.children.length > 0) {
            // CASE 1: Drill down

            // Get current label before we leave this level
            const currentHeaderLabel = parentStack.length > 0
                ? parentStack[parentStack.length - 1].itemName
                : null;

            // Push State (Save ID and Label so we can go back accurately)
            parentStack.push({
                list: currentLevelCategories,
                itemName: selectedObj.name,
                selectedId: selectedId,        // <--- SAVED: The ID we just clicked
                headerLabel: currentHeaderLabel // <--- SAVED: The label of the list we are leaving
            });

            currentLevelCategories = selectedObj.children;

            // Render next level
            // Arg 2 is null (nothing selected in new list yet)
            // Arg 3 is the name of the item we just clicked
            renderList(currentLevelCategories, null, selectedObj.name);

            hiddenInput.value = selectedId;
        } else {
            // CASE 2: Final Selection
            hiddenInput.value = selectedId;
        }
    });

    // --- EVENT: User clicks Back ---
    backBtn.addEventListener('click', function() {
        if (parentStack.length === 0) return;

        // Pop the state
        const previousState = parentStack.pop();
        currentLevelCategories = previousState.list;

        // Restore the list, highlighting what was previously selected
        renderList(
            currentLevelCategories,
            previousState.selectedId,  // <--- RESTORED
            previousState.headerLabel  // <--- RESTORED
        );

        hiddenInput.value = previousState.selectedId;
    });

    // --- HELPER: Breadcrumbs ---
    function updateBreadcrumbs() {
        if(parentStack.length === 0) {
            breadcrumbs.innerText = "";
            return;
        }
        // Use 'itemName' because that's what we saved in the stack object
        let text = parentStack.map(p => p.itemName).join(' > ');
        breadcrumbs.innerText = "Current path: " + text;
    }

    // --- INITIALIZATION ---
    if (initialPath && initialPath.length > 0) {
        initialPath.forEach((id, index) => {
            const isLast = index === initialPath.length - 1;

            if (!isLast) {
                // Find parent in current level to drill down
                const parentObj = currentLevelCategories.find(c => c.id == id);
                if (parentObj && parentObj.children) {

                    // Logic to populate stack for Breadcrumbs/Back button
                    const currentHeaderLabel = parentStack.length > 0
                        ? parentStack[parentStack.length - 1].itemName
                        : null;

                    parentStack.push({
                        list: currentLevelCategories,
                        itemName: parentObj.name,
                        selectedId: id,
                        headerLabel: currentHeaderLabel
                    });

                    currentLevelCategories = parentObj.children;
                }
            } else {
                // FINAL RENDER
                // Get the name of the immediate parent for the label
                const lastParent = parentStack.length > 0 ? parentStack[parentStack.length - 1] : null;
                const parentLabel = lastParent ? lastParent.itemName : null;

                renderList(currentLevelCategories, id, parentLabel);
                hiddenInput.value = id; // Ensure hidden input is set on load
            }
        });
    } else {
        renderList(currentLevelCategories);
    }
}


