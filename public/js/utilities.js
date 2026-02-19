function initializeImageUploader(config) {
    const {
        submitBtnId, uploaderId, previewContainerId, formId, hiddenContainerId, alertContainerId,
        uploadUrl = 'ajax/upload-image.php',
        maxImages = 10,
        minImages = 1,
        initialImages = []
    } = config;

    const submitBtn = document.getElementById(submitBtnId);
    const uploader = document.getElementById(uploaderId);
    const container = document.getElementById(previewContainerId);
    const form = document.getElementById(formId);
    const hiddenContainer = document.getElementById(hiddenContainerId);
    if (!submitBtn || !form || !hiddenContainer || !uploader || !container) return;

    function renderImageCard(url) {
        const div = document.createElement('div');
        div.className = 'img-card';
        div.dataset.url = url;

        div.innerHTML = `
            <div class="main-badge">MAIN</div>
            <img src="${url}" alt="Preview">
            <div class="img-actions">
                <button type="button" class="btn btn-secondary btn-xs btn-move-left">&lt;</button>
                <button type="button" class="btn btn-danger btn-xs btn-remove">X</button>
                <button type="button" class="btn btn-secondary btn-xs btn-move-right">&gt;</button>
            </div>
        `;

        div.querySelector('.btn-remove').addEventListener('click', () => div.remove());

        div.querySelector('.btn-move-left').addEventListener('click', () => {
            if (div.previousElementSibling) container.insertBefore(div, div.previousElementSibling);
        });

        div.querySelector('.btn-move-right').addEventListener('click', () => {
            if (div.nextElementSibling) container.insertBefore(div.nextElementSibling, div);
        });

        return div;
    }

    function uploadFileToCloud(file) {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'img-card';
        loadingDiv.innerHTML = '<div class="p-4 text-muted">Uploading...</div>';
        container.appendChild(loadingDiv);

        const formData = new FormData();
        formData.append('file', file);

        fetch(uploadUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.url) {
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

    if (initialImages && initialImages.length > 0) {
        initialImages.forEach(url => {
            const card = renderImageCard(url);
            container.appendChild(card);
        });
    }

    uploader.addEventListener('change', (e) => {
        Array.from(e.target.files).forEach(file => uploadFileToCloud(file));
        uploader.value = '';
    });

    submitBtn.addEventListener('click', (e) => {
        e.preventDefault();
        hiddenContainer.innerHTML = '';
        const cards = container.querySelectorAll('.img-card');

        if (cards.length < minImages || cards.length > maxImages) {
            alert(`Please upload between ${minImages} and ${maxImages} images.`);
            return;
        }

        cards.forEach((card, index) => {
            const url = card.dataset.url;
            const inputUrl = document.createElement('input');
            inputUrl.type = 'hidden';
            inputUrl.name = 'auction_image_urls[]';
            inputUrl.value = url;
            hiddenContainer.appendChild(inputUrl);
        });

        form.submit();
    });
}

function initImageGallery(imageUrls) {
    if (!imageUrls || imageUrls.length <= 1) {
        const navElements = ['prev-image', 'next-image', 'thumbnail-viewport', 'thumb-prev', 'thumb-next'];
        navElements.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.style.display = 'none';
        });
        return;
    }

    let currentIndex = 0;
    const mainImage = document.getElementById('main-image');
    const prevButton = document.getElementById('prev-image');
    const nextButton = document.getElementById('next-image');
    const thumbnails = document.querySelectorAll('.gallery-thumb');

    if (!mainImage || !prevButton || !nextButton || thumbnails.length === 0) {
        console.warn("Image gallery elements not found.");
        return;
    }

    function showImage(index) {
        if (index >= imageUrls.length) index = 0;
        if (index < 0) index = imageUrls.length - 1;

        mainImage.src = imageUrls[index];
        currentIndex = index;

        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active-thumb', i === currentIndex);
        });

        centerThumbnailInView(index);
    }

    prevButton.addEventListener('click', () => showImage(currentIndex - 1));
    nextButton.addEventListener('click', () => showImage(currentIndex + 1));

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => {
            showImage(parseInt(thumb.dataset.index, 10));
        });
    });

    const viewport = document.getElementById('thumbnail-viewport');
    const thumbContainer = document.getElementById('thumbnail-container');
    const thumbPrev = document.getElementById('thumb-prev');
    const thumbNext = document.getElementById('thumb-next');

    if (!viewport || !thumbContainer || !thumbPrev || !thumbNext || !thumbnails[0]) {
        console.warn("Thumbnail scroller elements not found.");
        return;
    }

    let scrollAmount = 0;
    const thumbScrollWidth = thumbnails[0].offsetWidth + 8;

    function updateThumbNav() {
        thumbPrev.style.display = (scrollAmount <= 0) ? 'none' : 'block';
        const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
        thumbNext.style.display = (scrollAmount >= maxScroll) ? 'none' : 'block';
    }

    thumbPrev.addEventListener('click', () => {
        const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
        scrollAmount -= thumbScrollWidth * 3;
        if (scrollAmount < 0) scrollAmount = 0;
        if (scrollAmount > maxScroll && maxScroll > 0) scrollAmount = maxScroll;
        thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
        updateThumbNav();
    });

    thumbNext.addEventListener('click', () => {
        const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
        scrollAmount += thumbScrollWidth * 3;
        if (scrollAmount > maxScroll) scrollAmount = maxScroll;
        if (scrollAmount < 0) scrollAmount = 0;
        thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
        updateThumbNav();
    });

    function centerThumbnailInView(index) {
        const activeThumb = thumbnails[index];
        if (!activeThumb) return;

        const viewportWidth = viewport.clientWidth;
        let newScroll = activeThumb.offsetLeft - (viewportWidth / 2) + (activeThumb.offsetWidth / 2);

        const maxScroll = thumbContainer.scrollWidth - viewportWidth;
        if (newScroll < 0) newScroll = 0;
        if (newScroll > maxScroll) newScroll = maxScroll;

        scrollAmount = newScroll;
        thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
        updateThumbNav();
    }

    updateThumbNav();
}

function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(function(alert) {
        if(alert.dataset.isDismissing === "true") return;
        alert.dataset.isDismissing = "true";

        setTimeout(function() {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(function() { alert.remove(); }, 500);
        }, 3000);
    });
}

function initializeCategorySelector(config) {
    const {
        selectorId, backBtnId, hiddenInputId, breadcrumbsId, treeData, initialPath
    } = config;

    const selector = document.getElementById(selectorId);
    const backBtn = document.getElementById(backBtnId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const breadcrumbs = document.getElementById(breadcrumbsId);

    if (!selector || !backBtn || !hiddenInput || !breadcrumbs) return;

    let currentLevelCategories = treeData;
    let parentStack = [];

    function renderList(list, selectedValue = null, parentLabel = null) {
        let topOptionText = parentLabel ? `Selected: ${parentLabel}` : 'Select a Category...';
        selector.innerHTML = `<option value="">${topOptionText}</option>`;

        list.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;

            if (cat.children && cat.children.length > 0) {
                option.textContent += ' >';
            }

            // Loose equality for string/int safety
            if (selectedValue && cat.id == selectedValue) {
                option.selected = true;
            }

            selector.appendChild(option);
        });

        backBtn.style.display = parentStack.length > 0 ? 'inline-block' : 'none';
        updateBreadcrumbs();
    }

    selector.addEventListener('change', function() {
        const selectedId = this.value;
        if (!selectedId) return;

        const selectedObj = currentLevelCategories.find(c => c.id == selectedId);

        if (selectedObj && selectedObj.children && selectedObj.children.length > 0) {
            const currentHeaderLabel = parentStack.length > 0
                ? parentStack[parentStack.length - 1].itemName
                : null;

            parentStack.push({
                list: currentLevelCategories,
                itemName: selectedObj.name,
                selectedId: selectedId,
                headerLabel: currentHeaderLabel
            });

            currentLevelCategories = selectedObj.children;
            renderList(currentLevelCategories, null, selectedObj.name);
            hiddenInput.value = selectedId;
        } else {
            hiddenInput.value = selectedId;
        }
    });

    backBtn.addEventListener('click', function() {
        if (parentStack.length === 0) return;

        const previousState = parentStack.pop();
        currentLevelCategories = previousState.list;

        renderList(
            currentLevelCategories,
            previousState.selectedId,
            previousState.headerLabel
        );

        hiddenInput.value = previousState.selectedId;
    });

    function updateBreadcrumbs() {
        if(parentStack.length === 0) {
            breadcrumbs.innerText = "";
            return;
        }
        let text = parentStack.map(p => p.itemName).join(' > ');
        breadcrumbs.innerText = "Current path: " + text;
    }

    if (initialPath && initialPath.length > 0) {
        initialPath.forEach((id, index) => {
            const isLast = index === initialPath.length - 1;

            if (!isLast) {
                const parentObj = currentLevelCategories.find(c => c.id == id);
                if (parentObj && parentObj.children) {
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
                const lastParent = parentStack.length > 0 ? parentStack[parentStack.length - 1] : null;
                const parentLabel = lastParent ? lastParent.itemName : null;

                renderList(currentLevelCategories, id, parentLabel);
                hiddenInput.value = id;
            }
        });
    } else {
        renderList(currentLevelCategories);
    }
}
