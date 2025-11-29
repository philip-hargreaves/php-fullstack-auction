<?php
use infrastructure\DIContainer;
require \infrastructure\Utilities::basePath('views/partials/header.php');
/**
 * @var $auctionId int
 * @var $auctionMode string
 * @var $prevAuction array
 * @var $titleText string
 * @var $itemConditions array
 * @var $StartingPriceText string
 * @var $jsonCategoryPath
 * @var $jsonCategoryTree
 * @var $ReservePriceText string
 * @var $categoryServ
 */
?>

<div class="container">
    <!-- Fill the placeholders -->
    <?php
    $placeHolder = [];
    if (isset($_SESSION['create_auction_old_input'])) {
        // placing old input
        $placeHolder = $_SESSION['create_auction_old_input'];

        $categoryId = $placeHolder["category_id"] ?? null;
        if ($categoryId) {
            $parents = $categoryServ->getAllParentId((int)$categoryId);
            $flatPath = array_merge($parents, [(int)$categoryId]);
            $jsonCategoryPath = json_encode($flatPath);
        }
    } elseif ($auctionMode == "create") {

    } elseif ($auctionMode == "update" || $auctionMode == "relist") {
        $placeHolder = $prevAuction;
    }
    ?>

    <?php $isItemNameLocked = ($auctionMode == 'update' || $auctionMode == 'relist'); ?>
    <?php $isStartDatetimeLocked = ($auctionMode == 'update'); ?>

    <!-- Content -->
    <div style="max-width: 800px; margin: 10px auto">
        <h2 class="my-3"><?= $titleText ?></h2>

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

        <!-- Input Form -->
        <div class="card">
            <div class="card-body">
                <form method="POST" id="create-auction-form" action="/create-auction" enctype="multipart/form-data">
                    <!-- POST Auction Mode -->
                    <input type="hidden"
                           name="auction_mode"
                           value="<?= $auctionMode ?>">
                    <input type="hidden"
                           name="auction_id"
                           value="<?= $auctionId ?? null ?>">
                    <!-- Item Name-->
                    <div class="form-group row">
                        <label for="item_name" class="col-sm-2 col-form-label text-left">Item Name</label>
                        <div class="col-sm-10">
                            <input type="text"
                                   class="form-control"
                                   id="item_name"
                                   name = "item_name"
                                   value="<?= htmlspecialchars($placeHolder['item_name'] ?? '') ?>"
                                   placeholder="Item name"
                                   required
                                   <?= $isItemNameLocked ? 'readonly style="color: #6A6A6A;"' : '' ?> >
                            <small id="titleHelp" class="form-text text-muted">
                                <span class="text-danger"><?php if (!$isItemNameLocked) {echo "* Required.";} ?>
                                </span><?php if ($isItemNameLocked) {echo "* ";} ?> Item name cannot be changed after created.
                            </small>
                        </div>
                    </div>
                    <!-- Item Description -->
                    <div class="form-group row">
                        <label for="auction_description" class="col-sm-2 col-form-label text-left">Item Description</label>
                        <div class="col-sm-10">
                            <input type="text"
                                   class="form-control"
                                   id="auction_description"
                                   name = "auction_description"
                                   value="<?= htmlspecialchars($placeHolder['auction_description'] ?? '') ?>"
                                   placeholder="Item Description"
                                   required>
                            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
                        </div>
                    </div>
                    <!-- Item Condition -->
                    <div class="form-group row">
                        <label for="auction_condition" class="col-sm-2 col-form-label text-left">Item Condition</label>
                        <div class="col-sm-10">
                            <select class="form-control"
                                    id="auction_condition"
                                    name="auction_condition"
                                    required>
                                <option value="" disabled <?= empty($placeHolder['auction_condition']) ? 'selected' : '' ?>>
                                    Select a condition
                                </option>
                                <?php foreach ($itemConditions as $condition): ?>
                                    <?php
                                    // Check if this condition matches the placeholder value
                                    $isSelected = (isset($placeHolder['auction_condition']) && $placeHolder['auction_condition'] == $condition) ? 'selected' : '';
                                    ?>
                                    <option value="<?= htmlspecialchars($condition) ?>" <?= $isSelected ?>>
                                        <?= htmlspecialchars($condition) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small id="conditionHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
                        </div>
                    </div>
                    <!-- Item Category -->
                    <div class="form-group row">
                        <label for="cat-selector" class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">
                            <div class="d-flex align-items-center mb-2">
                                <button type="button"
                                        id="cat-back-btn"
                                        class="btn btn-secondary btn-sm mr-2"
                                        style="display:none;">
                                    &laquo;
                                </button>
                                <select id="cat-selector" class="form-control flex-grow-1">
                                    <option value="">Select a Category...</option>
                                </select>
                            </div> <small id="titleHelp" class="form-text text-muted">
                                <span class="text-danger">* Required.</span>
                            </small>
                            <!-- Display current cat path -->
                            <small id="cat-breadcrumbs" class="text-muted mb-2"></small>
                            <input type="hidden"
                                   name="category_id"
                                   id="real-category-id"
                                   value="<?= htmlspecialchars($currentCategoryId ?? null) ?>">
                        </div>
                    </div>
                    <!-- Starting Price -->
                    <div class="form-group row">
                        <label for="starting_price" class="col-sm-2 col-form-label text-left">Starting price</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">£</span>
                                </div>
                                <input  type="number"
                                        class="form-control"
                                        id="starting_price"
                                        name = "starting_price"
                                        value="<?= htmlspecialchars($placeHolder['starting_price'] ?? '') ?>"
                                        placeholder="<?= number_format(20, 2) ?>"
                                        step="0.01"
                                        min="<?= 1 ?>"
                                        required>
                            </div>
                            <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> <?= $StartingPriceText ?? "" ?></small>
                        </div>
                    </div>
                    <!-- Reserve Price -->
                    <div class="form-group row">
                        <label for="reserve_price" class="col-sm-2 col-form-label text-left">Reserve price</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">£</span>
                                </div>
                                <input  type="number"
                                        class="form-control"
                                        id="reserve_price"
                                        name="reserve_price"
                                        value="<?= htmlspecialchars($placeHolder['reserve_price'] ?? '') ?>">
                            </div>
                            <small id="reservePriceHelp" class="form-text text-muted">* Optional. This value is not displayed in the auction listing.</small>
                            <small id="reservePriceHelp" class="form-text text-muted"><?= !empty($ReservePriceText) ? "* " . $ReservePriceText : "" ?></small>
                        </div>
                    </div>
                    <!-- Start Datetime -->
                    <div class="form-group row">
                        <label for="start_datetime" class="col-sm-2 col-form-label text-left">Start date</label>
                        <div class="col-sm-10">
                            <input  type="datetime-local"
                                    class="form-control"
                                    id="start_datetime"
                                    name="start_datetime"
                                    value="<?= $placeHolder['start_datetime'] ?? formatForInput(date("Y-m-d H:i:s")) ?>"
                                    <?= $isStartDatetimeLocked ? 'readonly style="color: #6A6A6A;"' : '' ?>>
                            <small class="form-text text-muted">
                                <span class="text-danger"><?php if (!$isStartDatetimeLocked) {echo "* Required. ";} ?>
                                </span><?php if ($isStartDatetimeLocked) {echo "*";} ?> Start Date cannot be changed after created
                            </small>
                        </div>
                    </div>
                    <!-- End Datetime -->
                    <div class="form-group row">
                        <label for="end_datetime" class="col-sm-2 col-form-label text-left"">End date</label>
                        <div class="col-sm-10">
                            <input  type="datetime-local"
                                    class="form-control"
                                    id="end_datetime"
                                    name="end_datetime"
                                    value="<?= $placeHolder['end_datetime'] ?? formatForInput(date("Y-m-d H:i:s")) ?>">
                            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> The duration has to be at least 24 hours. </small>
                            <small id="endDateHelp" class="form-text text-muted"><?= $EndDatetimeText ?? "" ?> </small>
                        </div>
                    </div>
                    <!-- Image Upload -->
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
                    <!-- Create Button -->
                    <button type="button" id="btn-create-auction" class="btn btn-primary form-control"><?= $titleText ?></button>
                </form>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['create_auction_old_input']); ?>
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
            maxImages:          10,
            initialImages:      <?= json_encode($placeHolder['auction_image_urls'] ?? []) ?>
        });

        // 3. Initialize Category Selector
        initializeCategorySelector({
            selectorId:    'cat-selector',
            backBtnId:     'cat-back-btn',
            hiddenInputId: 'real-category-id',
            breadcrumbsId: 'cat-breadcrumbs',

            // Inject PHP data directly here
            treeData:      <?= $jsonCategoryTree ? $jsonCategoryTree : "[]" ?>,
            initialPath:   <?= isset($jsonCategoryPath) ? $jsonCategoryPath : "[]" ?>
        });
    });

</script>