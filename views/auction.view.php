<?php
/**
 * @var $auctionId int
 * @var $title string
 * @var $sellerName string
 * @var $description string
 * @var $now DateTime
 * @var $startTime DateTime
 * @var $endTime DateTime
 * @var $startingPrice float
 * @var $reservePrice float
 * @var $currentPrice float
 * @var $auctionStatus string
 * @var $itemStatus string
 * @var $timeRemaining string
 * @var $hasSession bool
 * @var $isWatched bool
 */
?>

<?php require base_path("views/partials/header.php"); ?>

<div class="container">
    <div class="row">
        <div class="col-sm-8">
            <h2 class="my-3"><?= htmlspecialchars($title) ?></h2>
            <p>Seller: <?= $sellerName ?></p>
            <p>Item Description: <?= $description ?></p>
            <p>Started from: <?= date_format($startTime, 'j M H:i') ?></p>
            <p>Ended at: <?= date_format($endTime, 'j M H:i') ?></p>
            <p>Time Remaining: <?= $timeRemaining ?></p>
            <p>Starting Price: <?= $startingPrice ?></p>
            <p>Reserve Price: <?= $reservePrice ?></p>
            <p>Current Price: <?= $currentPrice ?></p>
            <p>Auction Status: <?= $auctionStatus ?></p>
        </div>
        <div class="col-sm-4 align-self-center"> <?php
            if ($now < $endTime):
                ?>
                <div id="watch_nowatch" <?php if ($hasSession && $isWatched) echo('style="display: none"'); ?>>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to
                        watchlist
                    </button>
                </div>
                <div id="watch_watching" <?php if (!$hasSession || !$isWatched) echo('style="display: none"'); ?>>
                    <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch
                    </button>
                </div>
            <?php endif; /* Print nothing otherwise */ ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">
            <div class="itemDescription">
                <?= htmlspecialchars($description) ?>
            </div>

        </div>

        <div class="col-sm-4"><p>
                <?php if ($now > $endTime): ?>
                    This auction ended <?= date_format($endTime, 'j M H:i') ?>
                <?php else: ?>
                Auction ends <?= date_format($endTime, 'j M H:i') . $timeRemaining ?></p>
            <p class="lead">Current bid: £<?= number_format($currentPrice, 2) ?></p>

            <form method="POST" action="/bid">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">£</span>
                    </div>
                    <input type="number" class="form-control" id="bid" name="bid_amount">
                </div>

                <!--                <input type="hidden" name="item_id" value="--><?php //= $item_id ?><!--">-->

                <button type="submit" class="btn btn-primary form-control">Place bid</button>
            </form>
            <?php endif; ?>

        </div>
    </div> <?php require base_path("views/partials/footer.php"); ?>

    <script>
        // JavaScript functions: addToWatchlist and removeFromWatchlist.

        function addToWatchlist(button) {
            console.log("These print statements are helpful for debugging btw");

            // This performs an asynchronous call to a PHP function using POST method.
            // Sends item ID as an argument to that function.
            $.ajax('watchlist_funcs.php', {
                type: "POST",
                data: {functionname: 'add_to_watchlist', arguments: [<?php echo($auctionId);?>]},

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
            }); // End of AJAX call

        } // End of addToWatchlist func

        function removeFromWatchlist(button) {
            // This performs an asynchronous call to a PHP function using POST method.
            // Sends item ID as an argument to that function.
            $.ajax('watchlist_funcs.php', {
                type: "POST",
                data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($auctionId);?>]},

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

        } // End of addToWatchlist func
    </script>