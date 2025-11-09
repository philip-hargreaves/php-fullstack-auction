<?php
/**
 * @var $title string
 * @var $now DateTime
 * @var $end_time DateTime
 * @var $has_session
 * @var $watching
 * @var $description
 * @var $time_remaining
 * @var $current_price
 * @var $item_id
 */
?>

<?php require base_path("views/partials/header.php"); ?>

<div class="container">
    <div class="row"> <div class="col-sm-8"> <h2 class="my-3"><?= htmlspecialchars($title) ?></h2>
        </div>
        <div class="col-sm-4 align-self-center"> <?php
            /* The following watchlist functionality uses JavaScript, but could
               just as easily use PHP as in other places in the code */
            if ($now < $end_time):
                ?>
                <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?>>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
                </div>
                <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?>>
                    <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
                </div>
            <?php endif; /* Print nothing otherwise */ ?>
        </div>
    </div>

    <div class="row"> <div class="col-sm-8"> <div class="itemDescription">
                <?= htmlspecialchars($description) ?>
            </div>

        </div>

        <div class="col-sm-4"> <p>
                <?php if ($now > $end_time): ?>
                    This auction ended <?= date_format($end_time, 'j M H:i') ?>
                <?php else: ?>
                Auction ends <?= date_format($end_time, 'j M H:i') . $time_remaining ?></p>
            <p class="lead">Current bid: £<?= number_format($current_price, 2) ?></p>

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

        </div> </div> <?php require base_path("views/partials/footer.php"); ?>

    <script>
        // JavaScript functions: addToWatchlist and removeFromWatchlist.

        function addToWatchlist(button) {
            console.log("These print statements are helpful for debugging btw");

            // This performs an asynchronous call to a PHP function using POST method.
            // Sends item ID as an argument to that function.
            $.ajax('watchlist_funcs.php', {
                type: "POST",
                data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},

                success:
                    function (obj, textstatus) {
                        // Callback function for when call is successful and returns obj
                        console.log("Success");
                        var objT = obj.trim();

                        if (objT == "success") {
                            $("#watch_nowatch").hide();
                            $("#watch_watching").show();
                        }
                        else {
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
                data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

                success:
                    function (obj, textstatus) {
                        // Callback function for when call is successful and returns obj
                        console.log("Success");
                        var objT = obj.trim();

                        if (objT == "success") {
                            $("#watch_watching").hide();
                            $("#watch_nowatch").show();
                        }
                        else {
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