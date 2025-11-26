<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container">

        <!-- logging errors and returning old input-->
        <?php
        $exception = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old']); // so errors only show once
        ?>

        <!-- Create auction form -->
        <div style="max-width: 800px; margin: 10px auto">
            <h2 class="my-3">Create new auction</h2>
            <div class="card">

                <div class="card-body">
                    <!-- Note: This form does not do any dynamic / client-side /
                    JavaScript-based validation of data. -->
                    <form method="POST" action="/create-auction" enctype="multipart/form-data">

                        <!-- item name-->
                        <div class="form-group row">
                            <label for="item_name" class="col-sm-2 col-form-label text-right">Item Name</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       class="form-control"
                                       id="item_name"
                                       name = "item_name"
                                       value="<?= htmlspecialchars($old['itemName'] ?? '') ?>">
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> The name of the item you're selling, which will display in listings.</small>
                            </div>
                        </div>
                        <!-- item name error -->
                        <?php if (!empty($exception['item_name'])): ?>
                            <div class = "text-danger">
                                <?= $exception['item_name'] ?>
                            </div>
                        <?php endif ?>

                        <!-- item description -->
                        <div class="form-group row">
                            <label for="item_description" class="col-sm-2 col-form-label text-right">Item Description</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       class="form-control"
                                       id="item_description"
                                       name = "item_description"
                                       value="<?= htmlspecialchars($old['itemDescription'] ?? '') ?>">
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A brief description of the item you're selling, which will display in listings.</small>
                            </div>
                        </div>
                        <!-- item description error handling -->
                        <?php if (!empty($exception['item_description'])): ?>
                            <div class = "text-danger">
                                <?= $exception['item_description'] ?>
                            </div>
                        <?php endif ?>

                        <!-- item condition -->
                        <div class="form-group row">
                            <label for="item_condition" class="col-sm-2 col-form-label text-right">Item Condition</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="item_condition" name="item_condition">
                                    <option value="">Select Condition</option>
                                    <option value="New">New</option>
                                    <option value="Like New">Like New</option>
                                    <option value="Used">Used</option>
                                </select>
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select the condition of your item.</small>
                            </div>
                        </div>
                        <!-- item condition error handling -->
                        <?php if (!empty($exception['item_condition'])): ?>
                            <div class = "text-danger">
                                <?= $exception['item_condition'] ?>
                            </div>
                        <?php endif ?>

                        <!-- add item category -->

                        <!-- starting price -->
                        <div class="form-group row">
                            <label for="starting_price" class="col-sm-2 col-form-label text-right">Starting price</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">£</span>
                                    </div>
                                    <input
                                            type="number"
                                            class="form-control"
                                            id="starting_price"
                                            name = "starting_price"
                                            value="<?= htmlspecialchars($old['auctionStartPrice'] ?? '') ?>">
                                </div>
                                <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
                            </div>
                        </div>
                        <!-- starting price error handling -->
                        <?php if (!empty($exception['starting_price'])): ?>
                            <div class = "text-danger">
                                <?= $exception['starting_price'] ?>
                            </div>
                        <?php endif ?>

                        <!-- reserve price -->
                        <div class="form-group row">
                            <label for="reserve_price" class="col-sm-2 col-form-label text-right">Reserve price</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">£</span>
                                    </div>
                                    <input
                                            type="number"
                                            class="form-control"
                                            id="reserve_price"
                                            name="reserve_price"
                                            value="<?= htmlspecialchars($old['auctionReservePrice'] ?? '') ?>">
                                </div>
                                <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
                            </div>
                        </div>
                        <!-- auction reserve price error handling -->
                        <?php if (!empty($exception['reserve_price'])): ?>
                            <div class = "text-danger">
                                <?= $exception['reserve_price'] ?>
                            </div>
                        <?php endif ?>

                        <!-- start datetime -->
                        <div class="form-group row">
                            <label for="start_datetime" class="col-sm-2 col-form-label text-right">Start date</label>
                            <div class="col-sm-10">
                                <input  type="datetime-local"
                                        class="form-control"
                                        id="start_datetime"
                                        name="start_datetime" >
                                <small class="form-text text-muted"><span class="text-danger">* Required.</span> Start date for the auction.</small>
                            </div>
                        </div>
                        <!-- start datetime error handling -->
                        <?php if (!empty($exception['start_datetime'])): ?>
                            <div class = "text-danger">
                                <?= $exception['start_datetime'] ?>
                            </div>
                        <?php endif ?>

                        <!-- end datetime -->
                        <div class="form-group row">
                            <label for="end_datetime" class="col-sm-2 col-form-label text-right">End date</label>
                            <div class="col-sm-10">
                                <input
                                        type="datetime-local"
                                        class="form-control"
                                        id="end_datetime"
                                        name="end_datetime">
                                <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
                            </div>
                        </div>
                        <!-- end datetime error handling -->
                        <?php if (!empty($exception['end_datetime'])): ?>
                            <div class = "text-danger">
                                <?= $exception['end_datetime'] ?>
                            </div>
                        <?php endif ?>

                        <!-- image upload -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label text-right">Item Images</label>
                            <div class="col-sm-10">
                                <input type="file" 
                                       id="image_uploader" 
                                       class="form-control" 
                                       multiple accept="image/*">

                                <small class="form-text text-muted">
                                    <span class="text-danger">* Required.</span>
                                    Select images. The <strong>first image</strong> in the list below will be the Main Image.
                                    Use arrows to reorder.
                                </small>
                                <div id="image-preview-container"></div>
                                <div id="hidden-inputs-container"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary form-control">Create Auction</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require infrastructure\Utilities::basePath('views/partials/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploader = document.getElementById('image_uploader');
    const container = document.getElementById('image-preview-container');
    const form = document.querySelector('form'); // Your main form
    const hiddenContainer = document.getElementById('hidden-inputs-container');

    // 1. Handle File Selection
    uploader.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);

        files.forEach(file => {
            uploadFileToCloud(file);
        });

        // Clear input so user can select same file again if needed
        uploader.value = '';
    });

    // 2. Upload Logic (Simulated)
    function uploadFileToCloud(file) {
        // Create a placeholder card while uploading
        const card = createCard(null, true);
        container.appendChild(card);

        // PREPARE FORM DATA
        const formData = new FormData();
        formData.append('file', file);

        // EXECUTE FETCH (AJAX)
        // You need a PHP route that accepts this file, uploads to S3/Cloudinary,
        // and returns JSON: { "url": "https://cloud.com/img123.jpg" }
        fetch('/ajax/upload-image', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.url) {
                // Update the card with the real image
                updateCardWithImage(card, data.url);
            } else {
                alert('Upload failed');
                card.remove();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading image');
            card.remove();
        });
    }

    // 3. Create Visual Elements
    function createCard(url, isLoading = false) {
        const div = document.createElement('div');
        div.className = 'img-card';

        if (isLoading) {
            div.innerHTML = '<div class="p-4">Uploading...</div>';
            return div;
        }
    }

    function updateCardWithImage(card, url) {
        card.dataset.url = url; // Store URL in data attribute for later
        card.innerHTML = `
            <div class="main-badge">MAIN</div>
            <img src="${url}" alt="Preview">
            <div class="img-actions">
                <button type="button" class="btn btn-secondary btn-xs btn-move-left">&lt;</button>
                <button type="button" class="btn btn-danger btn-xs btn-remove">X</button>
                <button type="button" class="btn btn-secondary btn-xs btn-move-right">&gt;</button>
            </div>
        `;

        // Add Event Listeners for buttons inside this card
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

    // 4. Intercept Form Submit to Generate Hidden Inputs
    form.addEventListener('submit', function(e) {
        // Clear previous hidden inputs
        hiddenContainer.innerHTML = '';

        const cards = container.querySelectorAll('.img-card');

        if (cards.length === 0) {
            e.preventDefault();
            alert('Please upload at least one image.');
            return;
        }

        // Loop through visual cards and create hidden inputs
        cards.forEach((card, index) => {
            const url = card.dataset.url;
            if(!url) return;

            // First item (index 0) is Main
            const isMain = (index === 0) ? '1' : '0';

            // Create Input: Image URL
            // name="images[0][image_url]"
            const inputUrl = document.createElement('input');
            inputUrl.type = 'hidden';
            inputUrl.name = `images[${index}][image_url]`;
            inputUrl.value = url;

            // Create Input: Is Main
            // name="images[0][is_main]"
            const inputMain = document.createElement('input');
            inputMain.type = 'hidden';
            inputMain.name = `uploaded_images[${index}][is_main]`;
            inputMain.value = isMain;

            hiddenContainer.appendChild(inputUrl);
            hiddenContainer.appendChild(inputMain);
        });

        // The form will now submit normally with the hidden inputs
    });
});
</script>