$(function () {
  // Read CSRF token from meta tag and attach to all AJAX requests
  var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

  $.ajaxSetup({
    headers: { 'X-CSRF-Token': csrfToken }
  });

  // =========================================================================
  //  ORDERS PAGE: live filter
  // =========================================================================
  $('#orderFilter').on('input', function () {
    var query = $(this).val().toString().toLowerCase();
    $('#ordersTable tbody tr').each(function () {
      var text = $(this).find('.filter-target').text().toLowerCase();
      $(this).toggle(text.includes(query));
    });
  });

  // =========================================================================
  //  USERS: Add
  // =========================================================================
  $('#saveUser').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Saving…');
    $.post('/panel/api/add-user.php', $('#addUserForm').serialize())
      .done(function (res) {
        $('#userFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
        $('#addUserForm')[0].reset();
        setTimeout(function () { location.reload(); }, 800);
      })
      .fail(function (xhr) {
        $('#userFeedback').removeClass('text-gold').addClass('text-danger')
          .text((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to add user');
      })
      .always(function () { $btn.prop('disabled', false).text('Save'); });
  });

  // =========================================================================
  //  USERS: Edit — populate modal
  // =========================================================================
  $(document).on('click', '.btn-edit-user', function () {
    var $b = $(this);
    $('#editUserId').val($b.data('id'));
    $('#editUserName').val($b.data('name'));
    $('#editUserEmail').val($b.data('email'));
    $('#editUserRole').val($b.data('role'));
    $('#editUserStatus').val($b.data('status'));
    $('#editUserFeedback').text('');
    new bootstrap.Modal('#editUserModal').show();
  });

  $('#updateUser').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Updating…');
    $.post('/panel/api/update-user.php', $('#editUserForm').serialize())
      .done(function (res) {
        $('#editUserFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
        setTimeout(function () { location.reload(); }, 800);
      })
      .fail(function (xhr) {
        $('#editUserFeedback').removeClass('text-gold').addClass('text-danger')
          .text((xhr.responseJSON && xhr.responseJSON.message) || 'Update failed');
      })
      .always(function () { $btn.prop('disabled', false).text('Update'); });
  });

  // =========================================================================
  //  USERS: Delete
  // =========================================================================
  $(document).on('click', '.btn-delete-user', function () {
    var id = $(this).data('id');
    var name = $(this).data('name');
    if (!confirm('Delete user "' + name + '"? This cannot be undone.')) return;
    $.post('/panel/api/delete-user.php', { id: id })
      .done(function () { location.reload(); })
      .fail(function (xhr) {
        alert((xhr.responseJSON && xhr.responseJSON.message) || 'Delete failed');
      });
  });

  // =========================================================================
  //  PRODUCTS: Add
  // =========================================================================
  $('#saveProduct').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Saving…');
    $.post('/panel/api/add-product.php', $('#addProductForm').serialize())
      .done(function (res) {
        $('#productFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
        $('#addProductForm')[0].reset();
        setTimeout(function () { location.reload(); }, 800);
      })
      .fail(function (xhr) {
        $('#productFeedback').removeClass('text-gold').addClass('text-danger')
          .text((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to add product');
      })
      .always(function () { $btn.prop('disabled', false).text('Save'); });
  });

  // =========================================================================
  //  PRODUCTS: Edit — populate modal
  // =========================================================================
  $(document).on('click', '.btn-edit-product', function () {
    var $b = $(this);
    $('#editProductOriginalId').val($b.data('id'));
    $('#editProductId').val($b.data('id'));
    $('#editProductName').val($b.data('name'));
    $('#editProductProvider').val($b.data('provider'));
    $('#editProductStock').val($b.data('stock'));
    $('#editProductPrice').val($b.data('price'));
    $('#editProductFeedback').text('');
    new bootstrap.Modal('#editProductModal').show();
  });

  $('#updateProduct').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Updating…');
    $.post('/panel/api/update-product.php', $('#editProductForm').serialize())
      .done(function (res) {
        $('#editProductFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
        setTimeout(function () { location.reload(); }, 800);
      })
      .fail(function (xhr) {
        $('#editProductFeedback').removeClass('text-gold').addClass('text-danger')
          .text((xhr.responseJSON && xhr.responseJSON.message) || 'Update failed');
      })
      .always(function () { $btn.prop('disabled', false).text('Update'); });
  });

  // =========================================================================
  //  PRODUCTS: Delete
  // =========================================================================
  $(document).on('click', '.btn-delete-product', function () {
    var id = $(this).data('id');
    var name = $(this).data('name');
    if (!confirm('Delete product "' + name + '"? This cannot be undone.')) return;
    $.post('/panel/api/delete-product.php', { id: id })
      .done(function () { location.reload(); })
      .fail(function (xhr) {
        alert((xhr.responseJSON && xhr.responseJSON.message) || 'Delete failed');
      });
  });

  // =========================================================================
  //  ORDERS: Add
  // =========================================================================
  $('#saveOrder').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Saving…');
    $.post('/panel/api/add-order.php', $('#addOrderForm').serialize())
      .done(function (res) {
        $('#orderFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
        $('#addOrderForm')[0].reset();
        setTimeout(function () { location.reload(); }, 800);
      })
      .fail(function (xhr) {
        $('#orderFeedback').removeClass('text-gold').addClass('text-danger')
          .text((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to add order');
      })
      .always(function () { $btn.prop('disabled', false).text('Save'); });
  });

  // =========================================================================
  //  ORDERS: Edit — populate modal
  // =========================================================================
  $(document).on('click', '.btn-edit-order', function () {
    var $b = $(this);
    $('#editOrderOriginalId').val($b.data('id'));
    $('#editOrderId').val($b.data('id'));
    $('#editOrderCustomer').val($b.data('customer'));
    $('#editOrderAmount').val($b.data('amount'));
    $('#editOrderDate').val($b.data('date'));
    $('#editOrderStatus').val($b.data('status'));
    $('#editOrderFeedback').text('');
    new bootstrap.Modal('#editOrderModal').show();
  });

  $('#updateOrder').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Updating…');
    $.post('/panel/api/update-order.php', $('#editOrderForm').serialize())
      .done(function (res) {
        $('#editOrderFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
        setTimeout(function () { location.reload(); }, 800);
      })
      .fail(function (xhr) {
        $('#editOrderFeedback').removeClass('text-gold').addClass('text-danger')
          .text((xhr.responseJSON && xhr.responseJSON.message) || 'Update failed');
      })
      .always(function () { $btn.prop('disabled', false).text('Update'); });
  });

  // =========================================================================
  //  ORDERS: Delete
  // =========================================================================
  $(document).on('click', '.btn-delete-order', function () {
    var id = $(this).data('id');
    if (!confirm('Delete order "' + id + '"? This cannot be undone.')) return;
    $.post('/panel/api/delete-order.php', { id: id })
      .done(function () { location.reload(); })
      .fail(function (xhr) {
        alert((xhr.responseJSON && xhr.responseJSON.message) || 'Delete failed');
      });
  });

  // =========================================================================
  //  DASHBOARD: refresh orders via AJAX
  // =========================================================================
  $('#refreshOrders').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Loading…');
    $.getJSON('/panel/api/orders.php')
      .done(function (orders) {
        var badgeMap = { Paid: 'success', Pending: 'warning', Failed: 'danger' };
        var body = orders.map(function (item) {
          var badge = badgeMap[item.status] || 'secondary';
          return '<tr>' +
            '<td>' + $('<span>').text(item.id).html() + '</td>' +
            '<td>' + $('<span>').text(item.customer).html() + '</td>' +
            '<td>Rp ' + new Intl.NumberFormat('id-ID').format(item.amount) + '</td>' +
            '<td><span class="badge text-bg-' + badge + '">' + item.status + '</span></td>' +
            '</tr>';
        }).join('');
        $('#ordersPreview tbody').html(body);
      })
      .fail(function (xhr) {
        alert((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to load orders');
      })
      .always(function () {
        $btn.prop('disabled', false).text('Refresh');
      });
  });

  // =========================================================================
  //  REPORTS: real CSV export download
  // =========================================================================
  $('#simulateExport').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Generating…');
    var link = document.createElement('a');
    link.href = '/panel/api/export-csv.php';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    $('#exportFeedback').text('CSV download started.');
    setTimeout(function () {
      $btn.prop('disabled', false).text('Generate CSV');
    }, 1500);
  });

  // =========================================================================
  //  SETTINGS: save via AJAX to real endpoint
  // =========================================================================
  $('#settingsForm').on('submit', function (event) {
    event.preventDefault();
    var $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).text('Saving…');
    var payload = $(this).serialize();
    payload += '&notifications=' + ($('#notifSwitch').is(':checked') ? '1' : '0');
    $.post('/panel/api/settings.php', payload)
      .done(function (res) {
        $('#settingsFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
      })
      .fail(function (xhr) {
        $('#settingsFeedback').removeClass('text-gold').addClass('text-danger')
          .text((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save settings');
      })
      .always(function () {
        $btn.prop('disabled', false).text('Save changes');
      });
  });
});
