// admin.js
// Handles client-side search and filtering for the admin dashboard
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('adminSearchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#dashboard-table-body tr[data-role]');
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-select');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            const rowRole = row.getAttribute('data-role').toLowerCase();
            const rowStatus = row.getAttribute('data-status').toLowerCase();

            const matchesSearch = textContent.includes(searchTerm);
            const matchesRole = roleValue === '' || rowRole === roleValue;
            const matchesStatus = statusValue === '' || rowStatus === statusValue;

            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (roleFilter) roleFilter.addEventListener('change', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);

    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
        });
    }

    // Modal Trigger for User Metrics
    $('.user-row').on('click', function(e) {
        if ($(e.target).closest('input[type="checkbox"]').length) return; // Ignore checkbox clicks

        const userId = $(this).data('userid');
        $('#userMetricsModal').modal('show');
        $('#metrics-loading').show();
        $('#metrics-content').hide();

        $.ajax({
            url: 'admin_action.php',
            method: 'GET',
            data: { action: 'get_metrics', user_id: userId },
            success: function(response) {
                if(response.success) {
                    $('#m_bought').text(response.metrics.total_bought);
                    $('#m_sold').text(response.metrics.total_sold);
                    $('#m_category').text(response.metrics.preferred_category);
                    $('#metrics-loading').hide();
                    $('#metrics-content').fadeIn();
                } else {
                    $('#metrics-loading').html('<span class="text-danger">Failed to load metrics.</span>');
                }
            },
            error: function() {
                $('#metrics-loading').html('<span class="text-danger">Failed to contact server.</span>');
            }
        });
    });

    // Moderation Queue Actions
    $('.approve-btn').on('click', function() {
        const listingId = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: 'admin_action.php',
            method: 'POST',
            data: { action: 'approve_listing', listing_id: listingId },
            success: function(response) {
                if(response.success) {
                    $('#listing-row-' + listingId).fadeOut();
                } else {
                    alert('Failed to approve listing.');
                    btn.prop('disabled', false).text('Approve');
                }
            }
        });
    });

    $('.reject-btn').on('click', function() {
        const listingId = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: 'admin_action.php',
            method: 'POST',
            data: { action: 'reject_listing', listing_id: listingId },
            success: function(response) {
                if(response.success) {
                    $('#listing-row-' + listingId).fadeOut();
                } else {
                    alert('Failed to reject listing.');
                    btn.prop('disabled', false).text('Reject');
                }
            }
        });
    });

    // Seller Application Actions
    $('.approve-seller-btn').on('click', function() {
        const userId = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: 'admin_action.php',
            method: 'POST',
            data: { action: 'approve_seller', user_id: userId },
            success: function(response) {
                if (response.success) {
                    $('#seller-row-' + userId).fadeOut(400, function() { $(this).remove(); });
                } else {
                    alert('Failed to approve seller.');
                    btn.prop('disabled', false).text('Approve');
                }
            }
        });
    });

    $('.reject-seller-btn').on('click', function() {
        const userId = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: 'admin_action.php',
            method: 'POST',
            data: { action: 'reject_seller', user_id: userId },
            success: function(response) {
                if (response.success) {
                    $('#seller-row-' + userId).fadeOut(400, function() { $(this).remove(); });
                } else {
                    alert('Failed to reject application.');
                    btn.prop('disabled', false).text('Reject');
                }
            }
        });
    });
});
