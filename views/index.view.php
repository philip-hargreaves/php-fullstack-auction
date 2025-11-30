<?php use infrastructure\Utilities;
require Utilities::basePath('views/partials/header.php');
/**
 * @var $processed_auctions array
 * @var $curr_page
 * @var $querystring
 * @var $low_page
 * @var $high_page
 * @var $max_page
 */
?>

    <!-- Main Content with Filters -->
    <div class="container-fluid mt-5 px-4">
        <div class="main-content-wrapper">
            <!-- Left Sidebar Filters -->
            <div class="filter-sidebar">
                <form method="GET" action="/" id="filterForm">
                    <!-- Preserve sort order -->
                    <input type="hidden" name="order_by" value="<?= htmlspecialchars($activeFilters['ordering']) ?>">
                    
                    <!-- Categories Accordion -->
                    <div class="filter-section">
                        <button type="button" class="accordion-button" id="categoryAccordion" aria-expanded="true" aria-controls="categoryContent">
                            <span>Categories</span>
                            <div class="accordion-icon">
                                <svg width="16" height="16" class="icon-primary" fill-rule="evenodd" viewBox="0 0 24 24"><path d="M13.67,6.45a2.46,2.46,0,0,0-3.42,0l-8,8a1,1,0,0,0,0,1.42,1,1,0,0,0,1.41,0l8-8a.35.35,0,0,1,.58,0l8,8.1a1,1,0,1,0,1.42-1.41Z"></path></svg>
                            </div>
                        </button>
                        <div id="categoryContent" class="accordion-content" aria-labelledby="categoryAccordion">
                            <div id="categoryDropdowns">
                                <!-- Level 1: Root Categories -->
                                <select class="filter-select category-level" id="categoryLevel1" data-level="1">
                                    <option value="">All Categories</option>
                                    <?php if (!empty($activeFilters['categoryTree'])): ?>
                                        <?php foreach ($activeFilters['categoryTree'] as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" 
                                                <?= (!empty($activeFilters['selectedCategoryPath'][0]) && $activeFilters['selectedCategoryPath'][0] == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>

                                <!-- Level 2: Subcategories (dynamically populated) -->
                                <select class="filter-select category-level" id="categoryLevel2" data-level="2" style="display: none; margin-top: 8px;">
                                    <option value="">-- Select Subcategory --</option>
                                </select>

                                <!-- Level 3: Sub-subcategories (dynamically populated) -->
                                <select class="filter-select category-level" id="categoryLevel3" data-level="3" style="display: none; margin-top: 8px;">
                                    <option value="">-- Select Subcategory --</option>
                                </select>

                                <!-- Hidden field for final category ID -->
                                <input type="hidden" name="category" id="selectedCategoryId" value="<?= htmlspecialchars($activeFilters['categoryId'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Condition Accordion -->
                    <div class="filter-section">
                        <button type="button" class="accordion-button" id="conditionAccordion" aria-expanded="true" aria-controls="conditionContent">
                            <span>Item condition</span>
                            <div class="accordion-icon">
                                <svg width="16" height="16" class="icon-primary" fill-rule="evenodd" viewBox="0 0 24 24"><path d="M13.67,6.45a2.46,2.46,0,0,0-3.42,0l-8,8a1,1,0,0,0,0,1.42,1,1,0,0,0,1.41,0l8-8a.35.35,0,0,1,.58,0l8,8.1a1,1,0,1,0,1.42-1.41Z"></path></svg>
                            </div>
                        </button>
                        <div id="conditionContent" class="accordion-content" aria-labelledby="conditionAccordion">
                            <div class="checkbox-group">
                                <?php
                                // Map database condition values back to form values for checkboxes
                                $activeConditions = $activeFilters['conditions'] ?? [];
                                $hasNew = in_array('New', $activeConditions);
                                $hasLikeNew = in_array('Like New', $activeConditions);
                                $hasUsed = in_array('Used', $activeConditions);
                                ?>
                                <label class="checkbox-label">
                                    <input class="mer-checkbox" type="checkbox" value="new" name="item_condition_id[]"
                                        <?= $hasNew ? 'checked' : '' ?>>
                                    <span class="checkbox-text">New</span>
                                </label>
                                <label class="checkbox-label">
                                    <input class="mer-checkbox" type="checkbox" value="like_new" name="item_condition_id[]"
                                        <?= $hasLikeNew ? 'checked' : '' ?>>
                                    <span class="checkbox-text">Like new</span>
                                </label>
                                <label class="checkbox-label">
                                    <input class="mer-checkbox" type="checkbox" value="used" name="item_condition_id[]"
                                        <?= $hasUsed ? 'checked' : '' ?>>
                                    <span class="checkbox-text">Used</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Item Status Accordion -->
                    <div class="filter-section">
                        <button type="button" class="accordion-button" id="statusAccordion" aria-expanded="true" aria-controls="statusContent">
                            <span>Item status</span>
                            <div class="accordion-icon">
                                <svg width="16" height="16" class="icon-primary" fill-rule="evenodd" viewBox="0 0 24 24"><path d="M13.67,6.45a2.46,2.46,0,0,0-3.42,0l-8,8a1,1,0,0,0,0,1.42,1,1,0,0,0,1.41,0l8-8a.35.35,0,0,1,.58,0l8,8.1a1,1,0,1,0,1.42-1.41Z"></path></svg>
                            </div>
                        </button>
                        <div id="statusContent" class="accordion-content" aria-labelledby="statusAccordion">
                            <div class="checkbox-group">
                                <?php
                                // Map database status values back to form values for checkboxes
                                $activeStatuses = $activeFilters['statuses'] ?? ['Active'];
                                $hasActive = in_array('Active', $activeStatuses);
                                $hasCompleted = $activeFilters['completedFilter'] ?? false;
                                $hasSold = $activeFilters['soldFilter'] ?? false;
                                ?>
                                <label class="checkbox-label">
                                    <input class="mer-checkbox" type="checkbox" value="active" name="auction_status[]"
                                        <?= $hasActive ? 'checked' : '' ?>>
                                    <span class="checkbox-text">Active</span>
                                </label>
                                <label class="checkbox-label">
                                    <input class="mer-checkbox" type="checkbox" value="completed" name="auction_status[]"
                                        <?= $hasCompleted ? 'checked' : '' ?>>
                                    <span class="checkbox-text">Completed</span>
                                </label>
                                <label class="checkbox-label">
                                    <input class="mer-checkbox" type="checkbox" value="sold" name="auction_status[]"
                                        <?= $hasSold ? 'checked' : '' ?>>
                                    <span class="checkbox-text">Sold</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Price Range Accordion -->
                    <div class="filter-section">
                        <button type="button" class="accordion-button" id="priceAccordion" aria-expanded="true" aria-controls="priceContent">
                            <span>Price</span>
                            <div class="accordion-icon">
                                <svg width="16" height="16" class="icon-primary" fill-rule="evenodd" viewBox="0 0 24 24"><path d="M13.67,6.45a2.46,2.46,0,0,0-3.42,0l-8,8a1,1,0,0,0,0,1.42,1,1,0,0,0,1.41,0l8-8a.35.35,0,0,1,.58,0l8,8.1a1,1,0,1,0,1.42-1.41Z"></path></svg>
                            </div>
                        </button>
                        <div id="priceContent" class="accordion-content" aria-labelledby="priceAccordion">
                            <div class="price-range">
                                <input type="number" class="price-input" name="min_price" placeholder="Min" min="0" 
                                    value="<?= htmlspecialchars($activeFilters['minPrice'] ?? '') ?>">
                                <span class="price-separator">-</span>
                                <input type="number" class="price-input" name="max_price" placeholder="Max" min="0"
                                    value="<?= htmlspecialchars($activeFilters['maxPrice'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="filter-section">
                        <button type="submit" class="btn btn-outline-secondary btn-apply-filters w-100">Apply Filters</button>
                        <a href="/" class="btn btn-outline-secondary w-100 mt-2">Clear All</a>
                    </div>
                </form>
            </div>

            <!-- Main Content Area -->
            <div class="main-content-area">
                <div class="sort-container mb-3">
                    <div class="sort-dropdown">
                        <button type="button" class="sort-button" id="sortButton">
                            <svg width="20" height="20" class="sort-icon" fill-rule="evenodd" viewBox="0 0 24 24">
                                <path d="M7.75,5.34A1.16,1.16,0,0,0,7,5l-.1,0L6.8,5a1.24,1.24,0,0,0-.74.32L3.21,8.13a.7.7,0,0,0,0,1,.7.7,0,0,0,.5.21.67.67,0,0,0,.49-.2l2-2V18.3a.7.7,0,1,0,1.4,0V7.16l2,1.94a.7.7,0,0,0,1,0,.71.71,0,0,0,0-1Z"></path>
                                <path d="M20.8,14.88a.7.7,0,0,0-1,0l-2,2V5.7a.7.7,0,1,0-1.4,0V16.84l-2-1.95a.7.7,0,0,0-1,1l2.83,2.76a1.28,1.28,0,0,0,.62.3.85.85,0,0,0,.22,0,.6.6,0,0,0,.24-.05,1.2,1.2,0,0,0,.61-.29l2.85-2.79A.7.7,0,0,0,20.8,14.88Z"></path>
                            </svg>
                            <span class="sort-text">Ending Soonest</span>
                        </button>
                        <div class="sort-menu" id="sortMenu">
                            <form method="get" action="/">
                                <?php
                                // Preserve all active filters when sorting
                                if (!empty($activeFilters['categoryId'])): ?>
                                    <input type="hidden" name="category" value="<?= htmlspecialchars($activeFilters['categoryId']) ?>">
                                <?php endif;
                                
                                if (!empty($activeFilters['conditions'])): ?>
                                    <?php foreach ($activeFilters['conditions'] as $condition): ?>
                                        <?php
                                        // Map database values back to form values
                                        $formValue = match($condition) {
                                            'New' => 'new',
                                            'Like New' => 'like_new',
                                            'Used' => 'used',
                                            default => null
                                        };
                                        if ($formValue): ?>
                                            <input type="hidden" name="item_condition_id[]" value="<?= htmlspecialchars($formValue) ?>">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif;

                                // Preserve status filters - use filter flags to determine which checkboxes were selected
                                if (in_array('Active', $activeFilters['statuses'])): ?>
                                    <input type="hidden" name="auction_status[]" value="active">
                                <?php endif;
                                
                                if ($activeFilters['completedFilter'] ?? false): ?>
                                    <input type="hidden" name="auction_status[]" value="completed">
                                <?php endif;
                                
                                if ($activeFilters['soldFilter'] ?? false): ?>
                                    <input type="hidden" name="auction_status[]" value="sold">
                                <?php endif; ?>
                                
                                <?php if (!empty($activeFilters['minPrice'])): ?>
                                    <input type="hidden" name="min_price" value="<?= htmlspecialchars($activeFilters['minPrice']) ?>">
                                <?php endif; ?>
                                
                                <?php if (!empty($activeFilters['maxPrice'])): ?>
                                    <input type="hidden" name="max_price" value="<?= htmlspecialchars($activeFilters['maxPrice']) ?>">
                                <?php endif; ?>
                                
                                <button type="submit" name="order_by" value="recommended" class="sort-option">Recommended order</button>
                                <button type="submit" name="order_by" value="date" class="sort-option active">Newest</button>
                                <button type="submit" name="order_by" value="ending_soonest" class="sort-option">Ending Soonest</button>
                                <button type="submit" name="order_by" value="pricelow" class="sort-option">Lowest price first</button>
                                <button type="submit" name="order_by" value="pricehigh" class="sort-option">Highest price first</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="auction-gallery">
                    <?php if (empty($processed_auctions)): ?>
                        <!-- No Results Message -->
                        <div style="text-align: center; padding: 60px 20px; width: 100%; grid-column: 1 / -1;">
                            <i class="fa fa-search" style="font-size: 64px; color: #ccc; margin-bottom: 20px;" aria-hidden="true"></i>
                            <h3 style="color: #333; margin-bottom: 10px; font-weight: 600;">No auctions found</h3>
                            <p style="color: #666; margin-bottom: 25px; font-size: 16px;">
                                We couldn't find any auctions matching your filters. Please try adjusting your search criteria.
                            </p>
                            <a href="/" class="btn btn-primary">Clear All Filters</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($processed_auctions as $auction):
                            // Use image from database, or default placeholder
                            $imageUrl = $auction['image_url'] ?? 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=300&h=300&fit=crop';
                            ?>
                        <div class="auction-card card h-100">
                            <div class="auction-image-container">
                                <a href="/auction?auction_id=<?= htmlspecialchars($auction['auction_id']) ?>">
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                         alt="<?= htmlspecialchars($auction['title']) ?>" 
                                         class="auction-image card-img-top">
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-0">
                                    <div>
                                        <h6 class="card-title mb-0">
                                            <a href="/auction?auction_id=<?= htmlspecialchars($auction['auction_id']) ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($auction['title']) ?>
                                            </a>
                                        </h6>
                                        <?php if (isset($auction['condition'])): ?>
                                            <div class="auction-condition"><?= htmlspecialchars($auction['condition']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="auction-info">
                                        <?= htmlspecialchars($auction['bid_text']) ?>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="auction-price">
                                        <span class="price-amount">£<?= number_format($auction['current_price'], 2) ?></span>
                                    </div>
                                    <div class="auction-time">
                                        <?php if ($auction['show_time_icon'] ?? true): ?>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        <?php endif; ?>
                                        <span class="<?= htmlspecialchars($auction['status_class'] ?? '') ?>">
                                            <?= htmlspecialchars($auction['time_remaining']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if (!empty($processed_auctions)): ?>
                <div class="pagination-container mt-5">
        <nav aria-label="Search results pages">
            <ul class="pagination justify-content-center">
            <!-- Previous button -->
            <?php if ($curr_page != 1) : ?>
                <li class="page-item">
                    <a class="page-link" href="/?<?php echo $querystring; ?>page=<?php echo $curr_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Numbered Pages -->
            <?php for ($i = $low_page; $i <= $high_page; $i++) : ?>
                <?php if ($i == $curr_page) : ?>
                    <li class="page-item active">
                <?php else : ?>
                    <li class="page-item">
                <?php endif; ?>

                <a class="page-link" href="/?<?php echo $querystring; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next Button -->
            <?php if ($curr_page < $max_page && $num_results > 0) : ?>
                <li class="page-item">
                    <a class="page-link" href="/?<?php echo $querystring; ?>page=<?php echo $curr_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            <?php endif; ?>
            </ul>
        </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Pagination -->
            <?php require Utilities::basePath('views/partials/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortButton = document.getElementById('sortButton');
    const sortMenu = document.getElementById('sortMenu');
    const sortDropdown = document.querySelector('.sort-dropdown');
    const sortText = document.querySelector('.sort-text');
    
    // Get current sort option from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('order_by') || 'ending_soonest';
    
    // Update sort text based on current selection
    const sortLabels = {
        'recommended': 'Recommended order',
        'date': 'Newest',
        'ending_soonest': 'Ending Soonest',
        'pricelow': 'Lowest price first',
        'pricehigh': 'Highest price first'
    };
    
    if (sortLabels[currentSort]) {
        sortText.textContent = sortLabels[currentSort];
    }
    
    // Mark active option
    sortMenu.querySelectorAll('.sort-option').forEach(btn => btn.classList.remove('active'));
    const activeOption = sortMenu.querySelector(`button[value="${currentSort}"]`);
    if (activeOption) {
        activeOption.classList.add('active');
    }
    
    // Toggle dropdown
    sortButton.addEventListener('click', function(e) {
        e.stopPropagation();
        sortDropdown.classList.toggle('active');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!sortDropdown.contains(e.target)) {
            sortDropdown.classList.remove('active');
        }
    });

    // Accordion functionality
    const conditionAccordion = document.getElementById('conditionAccordion');
    if (conditionAccordion) {
        conditionAccordion.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
        });
    }

    const priceAccordion = document.getElementById('priceAccordion');
    if (priceAccordion) {
        priceAccordion.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
        });
    }

    const categoryAccordion = document.getElementById('categoryAccordion');
    if (categoryAccordion) {
        categoryAccordion.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
        });
    }

    const statusAccordion = document.getElementById('statusAccordion');
    if (statusAccordion) {
        statusAccordion.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
        });
    }

    // Cascading Category Dropdowns
    (function() {
        const categoryTree = <?= json_encode($activeFilters['categoryTree'] ?? []) ?>;
        const selectedPath = <?= json_encode($activeFilters['selectedCategoryPath'] ?? []) ?>;
        
        // Build a flat map for quick lookup
        const categoryMap = {};
        function buildCategoryMap(categories, parentId = null) {
            categories.forEach(cat => {
                categoryMap[cat.id] = {
                    id: cat.id,
                    name: cat.name,
                    parent_id: parentId,
                    children: cat.children || []
                };
                if (cat.children && cat.children.length > 0) {
                    buildCategoryMap(cat.children, cat.id);
                }
            });
        }
        buildCategoryMap(categoryTree);
        
        // Get children of a category
        function getChildren(categoryId) {
            const cat = categoryMap[categoryId];
            return cat ? cat.children : [];
        }
        
        // Populate a dropdown level
        function populateLevel(level, categoryId) {
            const select = document.getElementById('categoryLevel' + level);
            if (!select) return;
            
            // Clear existing options except first
            select.innerHTML = '<option value="">-- Select Subcategory --</option>';
            
            if (!categoryId) {
                select.style.display = 'none';
                // Hide all lower levels
                for (let i = level + 1; i <= 3; i++) {
                    const lowerSelect = document.getElementById('categoryLevel' + i);
                    if (lowerSelect) {
                        lowerSelect.style.display = 'none';
                        lowerSelect.innerHTML = '<option value="">-- Select Subcategory --</option>';
                    }
                }
                return;
            }
            
            const children = getChildren(categoryId);
            if (children.length > 0) {
                select.style.display = 'block';
                children.forEach(child => {
                    const option = document.createElement('option');
                    option.value = child.id;
                    option.textContent = child.name;
                    // Preserve selection if in path
                    if (selectedPath[level - 1] === child.id) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            } else {
                // No children, this is a leaf category
                select.style.display = 'none';
                // Hide all lower levels
                for (let i = level + 1; i <= 3; i++) {
                    const lowerSelect = document.getElementById('categoryLevel' + i);
                    if (lowerSelect) {
                        lowerSelect.style.display = 'none';
                    }
                }
            }
        }
        
        // Function to sync category ID from dropdowns to hidden field
        function syncCategoryId() {
            const level1 = document.getElementById('categoryLevel1');
            const level2 = document.getElementById('categoryLevel2');
            const level3 = document.getElementById('categoryLevel3');
            const hiddenField = document.getElementById('selectedCategoryId');
            
            if (!hiddenField) return;
            
            // Priority: level3 > level2 > level1 (use deepest selected category)
            let selectedCategoryId = '';
            if (level3 && level3.value && level3.value !== '') {
                selectedCategoryId = level3.value;
            } else if (level2 && level2.value && level2.value !== '') {
                selectedCategoryId = level2.value;
            } else if (level1 && level1.value && level1.value !== '') {
                selectedCategoryId = level1.value;
            }
            
            hiddenField.value = selectedCategoryId;
        }
        
        // Initialize on page load
        if (selectedPath.length > 0) {
            // Level 1 is already set in HTML
            if (selectedPath.length > 1) {
                populateLevel(2, selectedPath[0]);
                if (selectedPath.length > 2) {
                    populateLevel(3, selectedPath[1]);
                }
            }
            // Set final category ID
            document.getElementById('selectedCategoryId').value = selectedPath[selectedPath.length - 1];
        }
        
        // Event listeners for cascading
        document.getElementById('categoryLevel1')?.addEventListener('change', function() {
            const categoryId = this.value;
            syncCategoryId();
            populateLevel(2, categoryId);
            populateLevel(3, null);
        });
        
        document.getElementById('categoryLevel2')?.addEventListener('change', function() {
            const categoryId = this.value;
            syncCategoryId();
            populateLevel(3, categoryId);
        });
        
        document.getElementById('categoryLevel3')?.addEventListener('change', function() {
            syncCategoryId();
        });
        
        // Ensure category ID is set before form submission
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                syncCategoryId();
            });
        }
    })();
});
</script>
