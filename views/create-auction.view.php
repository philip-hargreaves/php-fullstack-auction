<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container">
        <!-- logging errors and returning old input-->
        <?php
        $oldInput = $_SESSION['create_auction_old_input'] ?? [];
        ?>

        <!-- Create auction form -->
        <div style="max-width: 800px; margin: 10px auto">
            <h2 class="my-3">Create new auction</h2>

            <!-- Flash errors -->
            <div id="alert-container">
                <?php if (!empty($_SESSION['create_auction_error'])): ?>
                    <div class="alert alert-danger shadow-sm" role="alert" id="create-auction-alert">
                        <i class="fa fa-exclamation-circle"></i>
                        <?php echo $_SESSION['create_auction_error']; ?>
                    </div>
                    <?php
                    unset($_SESSION['create_auction_error']);
                    unset($_SESSION['create_auction_old_input']);
                    ?>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" id="create-auction-form" action="/create-auction" enctype="multipart/form-data">
                        <!-- item name-->
                        <div class="form-group row">
                            <label for="item_name" class="col-sm-2 col-form-label text-left">Item Name</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       class="form-control"
                                       id="item_name"
                                       name = "item_name"
                                       value="<?= htmlspecialchars($oldInput['item_name'] ?? '') ?>"
                                       placeholder="Item name"
                                       required>
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Item name cannot be changed after created.</small>
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
                            <label for="auction_description" class="col-sm-2 col-form-label text-left">Item Description</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       class="form-control"
                                       id="auction_description"
                                       name = "auction_description"
                                       value="<?= htmlspecialchars($oldInput['auction_description'] ?? '') ?>"
                                       placeholder="Item Description"
                                       required>
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
                            </div>
                        </div>
                        <!-- item description error handling -->
                        <?php if (!empty($exception['auction_description'])): ?>
                            <div class = "text-danger">
                                <?= $exception['auction_description'] ?>
                            </div>
                        <?php endif ?>

                        <!-- item condition -->
                        <div class="form-group row">
                            <label for="auction_condition" class="col-sm-2 col-form-label text-left">Item Condition</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="auction_condition" name="auction_condition">
                                    <option value="">Select a condition</option>
                                    <option value="New">New</option>
                                    <option value="Like New">Like New</option>
                                    <option value="Used">Used</option>
                                </select>
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
                            </div>
                        </div>
                        <!-- item condition error handling -->
                        <?php if (!empty($exception['auction_condition'])): ?>
                            <div class = "text-danger">
                                <?= $exception['auction_condition'] ?>
                            </div>
                        <?php endif ?>

                        <!-- add item category -->

                        <!-- starting price -->
                        <div class="form-group row">
                            <label for="starting_price" class="col-sm-2 col-form-label text-left">Starting price</label>
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
                                            value="<?= htmlspecialchars($oldInput['starting_price'] ?? '') ?>"
                                            placeholder="<?= number_format(20, 2) ?>"
                                            step="0.01"
                                            min="<?= 1 ?>"
                                            required>
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
                            <label for="reserve_price" class="col-sm-2 col-form-label text-left">Reserve price</label>
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
                                            value="<?= htmlspecialchars($oldInput['reserve_price'] ?? '') ?>">
                                </div>
                                <small id="reservePriceHelp" class="form-text text-muted">* Optional. Auctions that end below this price will not go through</small>
                                <small id="reservePriceHelp" class="form-text text-muted">* This value is not displayed in the auction listing.</small>
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
                            <label for="start_datetime" class="col-sm-2 col-form-label text-left">Start date</label>
                            <div class="col-sm-10">
                                <input  type="datetime-local"
                                        class="form-control"
                                        id="start_datetime"
                                        name="start_datetime" >
                                <small class="form-text text-muted"><span class="text-danger">* Required.</span></small>
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
                            <label for="end_datetime" class="col-sm-2 col-form-label text-left">End date</label>
                            <div class="col-sm-10">
                                <input
                                        type="datetime-local"
                                        class="form-control"
                                        id="end_datetime"
                                        name="end_datetime">
                                <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> The duration has to be at least 24 hours. </small>
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
                            <label class="col-sm-2 col-form-label text-left">Item Images</label>
                            <div class="col-sm-10">
                                <input type="file"
                                       id="image_uploader"
                                       style="display: none;"
                                       multiple accept="image/*">

                                <label for="image_uploader" class="btn btn-secondary">
                                    <i class="fa fa-cloud-upload"></i> Choose Files
                                </label>

                                <small class="form-text text-muted">
                                    <span class="text-danger">* Required.</span>
                                    The <strong>first image</strong> in the list below will be the Main Image.
                                    Use arrows to reorder.
                                </small>
                                <div id="image-preview-container"></div>
                                <div id="hidden-inputs-container"></div>
                            </div>
                        </div>

                        <button type="button" id="btn-create-auction" class="btn btn-primary form-control">Create Auction</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require infrastructure\Utilities::basePath('views/partials/footer.php'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. Run Alerts FIRST (As requested, keeping this separate)
        if (typeof autoDismissAlerts === 'function') {
            autoDismissAlerts();
        } else {
            console.error("autoDismissAlerts is not loaded.");
        }

        // 2. Initialize the Image Uploader
        initializeImageUploader({
            submitBtnId:        'btn-create-auction',
            uploaderId:         'image_uploader',
            previewContainerId: 'image-preview-container',
            formId:             'create-auction-form',
            hiddenContainerId:  'hidden-inputs-container',
            alertContainerId:   'alert-container',
            uploadUrl:          'ajax/upload-image.php',
            minImages:          1,
            maxImages:          10
        });

    });

</script>