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
                            <label for="itemName" class="col-sm-2 col-form-label text-right">Item Name</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       class="form-control"
                                       id="itemName"
                                       name = "itemName"
                                       value="<?= htmlspecialchars($old['itemName'] ?? '') ?>">
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> The name of the item you're selling, which will display in listings.</small>
                            </div>
                        </div>
                        <!-- item name error -->
                        <?php if (!empty($exception['itemName'])): ?>
                            <div class = "text-danger">
                                <?= $exception['itemName'] ?>
                            </div>
                        <?php endif ?>


                        <!-- item description -->
                        <div class="form-group row">
                            <label for="itemDescription" class="col-sm-2 col-form-label text-right">Item Description</label>
                            <div class="col-sm-10">
                                <input type="text"
                                       class="form-control"
                                       id="itemDescription"
                                       name = "itemDescription"
                                       value="<?= htmlspecialchars($old['itemDescription'] ?? '') ?>">
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A brief description of the item you're selling, which will display in listings.</small>
                            </div>
                        </div>
                        <!-- item description error handling -->
                        <?php if (!empty($exception['itemDescription'])): ?>
                            <div class = "text-danger">
                                <?= $exception['itemDescription'] ?>
                            </div>
                        <?php endif ?>

                        <!-- item condition -->
                        <div class="form-group row">
                            <label for="itemCondition" class="col-sm-2 col-form-label text-right">Item Condition</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="itemCondition" name="itemCondition">
                                    <option value="">Select Condition</option>
                                    <option value="New">New</option>
                                    <option value="Like New">Like New</option>
                                    <option value="Used">Used</option>
                                </select>
                                <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select the condition of your item.</small>
                            </div>
                        </div>
                        <!-- item condition error handling -->
                        <?php if (!empty($exception['itemCondition'])): ?>
                            <div class = "text-danger">
                                <?= $exception['itemCondition'] ?>
                            </div>
                        <?php endif ?>

                        <!-- need item category? can have drop-down list -->

                        <!-- auctionStartPrice -->
                        <div class="form-group row">
                            <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">£</span>
                                    </div>
                                    <input
                                            type="number"
                                            class="form-control"
                                            id="auctionStartPrice"
                                            name = "auctionStartPrice"
                                            value="<?= htmlspecialchars($old['auctionStartPrice'] ?? '') ?>">
                                </div>
                                <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
                            </div>
                        </div>
                        <!-- auction start price error handling -->
                        <?php if (!empty($exception['auctionStartPrice'])): ?>
                            <div class = "text-danger">
                                <?= $exception['auctionStartPrice'] ?>
                            </div>
                        <?php endif ?>

                        <!-- auctionReservePrice -->
                        <div class="form-group row">
                            <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">£</span>
                                    </div>
                                    <input
                                            type="number"
                                            class="form-control"
                                            id="auctionReservePrice"
                                            name="auctionReservePrice"
                                            value="<?= htmlspecialchars($old['auctionReservePrice'] ?? '') ?>">
                                </div>
                                <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
                            </div>
                        </div>
                        <!-- auction reserve price error handling -->
                        <?php if (!empty($exception['auctionReservePrice'])): ?>
                            <div class = "text-danger">
                                <?= $exception['auctionReservePrice'] ?>
                            </div>
                        <?php endif ?>

                        <!-- need to have constraint: start date cannot be before today -->
                        <div class="form-group row">
                            <label for="auctionStartDate" class="col-sm-2 col-form-label text-right">Start date</label>
                            <div class="col-sm-10">
                                <input
                                        type="datetime-local"
                                        class="form-control"
                                        id="auctionStartDate"
                                        name="auctionStartDate"
                                >
                                <small class="form-text text-muted"><span class="text-danger">* Required.</span> Start date for the auction.</small>
                            </div>
                        </div>
                        <!-- auction start date error handling -->
                        <?php if (!empty($exception['auctionStartDate'])): ?>
                            <div class = "text-danger">
                                <?= $exception['auctionStartDate'] ?>
                            </div>
                        <?php endif ?>

                        <!-- auctionEndDate -->
                        <div class="form-group row">
                            <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
                            <div class="col-sm-10">
                                <input
                                        type="datetime-local"
                                        class="form-control"
                                        id="auctionEndDate"
                                        name="auctionEndDate">
                                <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
                            </div>
                        </div>
                        <!-- auction end date error handling -->
                        <?php if (!empty($exception['auctionEndDate'])): ?>
                            <div class = "text-danger">
                                <?= $exception['auctionEndDate'] ?>
                            </div>
                        <?php endif ?>

                        <!-- image upload -->
                        <div class="form-group row">
                            <label for="images" class="col-sm-2 col-form-label text-right">Item Images</label>
                            <div class="col-sm-10">
                                <input type="file" id="images" name="images[]" class="form-control" multiple required>
                                <small class="form-text text-muted">
                                    <span class="text-danger">* Required.</span> Upload one or more images of your item, showing different angles.
                                </small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary form-control">Create Auction</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require \infrastructure\Utilities::basePath('views/partials/footer.php');