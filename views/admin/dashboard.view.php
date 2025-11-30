<?php
require \infrastructure\Utilities::basePath('views/partials/admin-header.php');
?>

<div class="container my-5">
    <h1 class="page-title">User Management</h1>
    

    <!-- User Table -->
    <?php if (empty($users)): ?>
        <div class="alert alert-info mt-4">
            No users found.
        </div>
    <?php else: ?>
        <div class="card mt-4">
            <div class="card-body" style="padding: 0; overflow: visible;">
                <table class="table table-striped bids-table" style="width: 100%; margin-bottom: 0;">
                    <thead class="thead-dark">
                        <tr>
                            <th style="white-space: nowrap; padding: 12px 16px;">ID</th>
                            <th style="white-space: nowrap; padding: 12px 16px;">Username</th>
                            <th style="white-space: nowrap; padding: 12px 16px;">Email</th>
                            <th style="white-space: nowrap; padding: 12px 16px;">Roles</th>
                            <th style="white-space: nowrap; padding: 12px 16px;">Status</th>
                            <th style="white-space: nowrap; padding: 12px 16px;">Created</th>
                            <th style="white-space: nowrap; padding: 12px 16px;">Actions</th>
                        </tr>
                    </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td style="white-space: nowrap; padding: 12px 16px;"><?= htmlspecialchars($user->getUserId()) ?></td>
                                    <td style="white-space: nowrap; padding: 12px 16px;"><?= htmlspecialchars($user->getUsername()) ?></td>
                                    <td style="white-space: nowrap; padding: 12px 16px;"><?= htmlspecialchars($user->getEmail()) ?></td>
                                    <td style="white-space: nowrap; padding: 12px 16px;">
                                        <?php
                                        $roleNames = $user->getRoleNames();
                                        if (empty($roleNames)): ?>
                                            <span class="badge bg-secondary">No roles</span>
                                        <?php else: ?>
                                            <?php foreach ($roleNames as $roleName): ?>
                                                <span class="badge bg-primary mr-1"><?= htmlspecialchars($roleName) ?></span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="white-space: nowrap; padding: 12px 16px;">
                                        <?php if ($user->isActive()): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="white-space: nowrap; padding: 12px 16px;">
                                        <?php 
                                        $createdDate = $user->getCreatedDatetime();
                                        if ($createdDate instanceof DateTime): 
                                            echo htmlspecialchars($createdDate->format('Y-m-d H:i'));
                                        else: 
                                            echo 'N/A';
                                        endif; 
                                        ?>
                                    </td>
                                    <td style="white-space: nowrap; padding: 12px 16px;">
                                        <?php if ($user->isAdmin()): ?>
                                            <span class="text-muted" style="font-style: italic;">Admin accounts cannot be modified</span>
                                        <?php else: ?>
                                            <div class="d-flex" style="flex-wrap: nowrap; gap: 8px;">
                                                <!-- Activate/Deactivate Toggle -->
                                                <form method="POST" action="/admin/user/update-status" style="display: inline; flex-shrink: 0;">
                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->getUserId()) ?>">
                                                    <input type="hidden" name="is_active" value="<?= $user->isActive() ? '0' : '1' ?>">
                                                    <button type="submit" class="btn btn-sm <?= $user->isActive() ? 'btn-warning' : 'btn-success' ?>" style="white-space: nowrap;">
                                                        <?= $user->isActive() ? 'Deactivate' : 'Activate' ?>
                                                    </button>
                                                </form>

                                                <!-- Role Management Dropdown -->
                                                <div class="dropdown" style="display: inline-block; flex-shrink: 0;">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="roleDropdown<?= $user->getUserId() ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;">
                                                        Manage Roles
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="roleDropdown<?= $user->getUserId() ?>" style="min-width: 200px;">
                                                        <?php if (isset($allRoles) && is_array($allRoles)): ?>
                                                            <?php foreach ($allRoles as $role): ?>
                                                            <?php
                                                            $roleName = $role->getName();
                                                            // Skip admin role - it cannot be assigned/revoked through the UI
                                                            if ($roleName === 'admin') {
                                                                continue;
                                                            }
                                                            $hasRole = $user->hasRoles($roleName);
                                                            ?>
                                                            <?php if ($hasRole): ?>
                                                                <!-- Revoke Role -->
                                                                <form method="POST" action="/admin/user/manage-role" class="dropdown-item-form">
                                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->getUserId()) ?>">
                                                                    <input type="hidden" name="role_name" value="<?= htmlspecialchars($roleName) ?>">
                                                                    <input type="hidden" name="action" value="revoke">
                                                                    <button type="submit" class="dropdown-item text-danger" style="cursor: pointer; border: none; background: none; width: 100%; text-align: left; padding: 8px 16px; display: block;">
                                                                        <i class="fa fa-minus-circle"></i> Revoke <?= htmlspecialchars($roleName) ?>
                                                                    </button>
                                                                </form>
                                                            <?php else: ?>
                                                                <!-- Assign Role -->
                                                                <form method="POST" action="/admin/user/manage-role" class="dropdown-item-form">
                                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->getUserId()) ?>">
                                                                    <input type="hidden" name="role_name" value="<?= htmlspecialchars($roleName) ?>">
                                                                    <input type="hidden" name="action" value="assign">
                                                                    <button type="submit" class="dropdown-item text-success" style="cursor: pointer; border: none; background: none; width: 100%; text-align: left; padding: 8px 16px; display: block;">
                                                                        <i class="fa fa-plus-circle"></i> Assign <?= htmlspecialchars($roleName) ?>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="dropdown-item text-muted">No roles available</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (isset($max_page) && $max_page > 1): ?>
            <div class="pagination-container mt-4">
                <nav aria-label="User list pages">
                    <ul class="pagination justify-content-center">
                        <!-- Previous button -->
                        <?php if ($curr_page != 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="/admin?<?= htmlspecialchars($querystring) ?>page=<?= $curr_page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Page numbers -->
                        <?php
                        $low_page = max(1, $curr_page - 2);
                        $high_page = min($max_page, $curr_page + 2);
                        for ($i = $low_page; $i <= $high_page; $i++):
                        ?>
                            <?php if ($i == $curr_page): ?>
                                <li class="page-item active">
                            <?php else: ?>
                                <li class="page-item">
                            <?php endif; ?>
                                <a class="page-link" href="/admin?<?= htmlspecialchars($querystring) ?>page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next button -->
                        <?php if ($curr_page < $max_page): ?>
                            <li class="page-item">
                                <a class="page-link" href="/admin?<?= htmlspecialchars($querystring) ?>page=<?= $curr_page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

        <!-- Results count -->
        <div class="mt-3 text-muted text-center">
            Showing <?= count($users) ?> of <?= $total ?> users (Page <?= $curr_page ?> of <?= $max_page ?>)
        </div>
    <?php endif; ?>
</div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>

<script>
// Fix dropdown behavior for role management
$(document).ready(function() {
    // Prevent dropdown from closing when clicking on form buttons
    $('.dropdown-item-form').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Prevent dropdown from closing when clicking on buttons inside forms
    $('.dropdown-item-form button').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Close dropdown after form submission
    $('.dropdown-item-form').on('submit', function(e) {
        var dropdown = $(this).closest('.dropdown');
        setTimeout(function() {
            dropdown.find('.dropdown-toggle').dropdown('hide');
        }, 100);
    });
    
    // Ensure dropdown menu has proper z-index
    $('.dropdown-menu').css('z-index', '1050');
    
    // Fix dropdown clipping at bottom of table
    $('.dropdown-toggle').on('show.bs.dropdown', function() {
        var $dropdown = $(this).next('.dropdown-menu');
        var $row = $(this).closest('tr');
        var isLastRow = $row.is(':last-child');
        var tableBottom = $row.closest('table').offset().top + $row.closest('table').outerHeight();
        var rowBottom = $row.offset().top + $row.outerHeight();
        var viewportBottom = $(window).scrollTop() + $(window).height();
        
        // If it's the last row or near the bottom, open upward
        if (isLastRow || (rowBottom + $dropdown.outerHeight() > viewportBottom)) {
            $dropdown.addClass('dropdown-menu-top');
            $dropdown.css({
                'top': 'auto',
                'bottom': '100%',
                'margin-bottom': '0.125rem'
            });
        } else {
            $dropdown.removeClass('dropdown-menu-top');
            $dropdown.css({
                'top': '',
                'bottom': '',
                'margin-bottom': ''
            });
        }
    });
});
</script>

