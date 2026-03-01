$(function () {
  // Read CSRF token from meta tag and attach to all AJAX requests
  var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

  $.ajaxSetup({
    headers: { 'X-CSRF-Token': csrfToken }
  });

  // --- Orders page: live filter ---
  $('#orderFilter').on('input', function () {
    var query = $(this).val().toString().toLowerCase();
    $('#ordersTable tbody tr').each(function () {
      var text = $(this).find('.filter-target').text().toLowerCase();
      $(this).toggle(text.includes(query));
    });
  });

  // --- Users page: add user via AJAX ---
  $('#saveUser').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Saving…');
    var payload = $('#addUserForm').serialize();
    $.post('/panel/api/add-user.php', payload)
      .done(function (res) {
        $('#userFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
        $('#addUserForm')[0].reset();
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to add user';
        $('#userFeedback').removeClass('text-gold').addClass('text-danger').text(msg);
      })
      .always(function () {
        $btn.prop('disabled', false).text('Save');
      });
  });

  // --- Dashboard: refresh orders via AJAX ---
  $('#refreshOrders').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Loading…');
    $.getJSON('/panel/api/orders.php')
      .done(function (orders) {
        var badgeMap = { Paid: 'success', Pending: 'warning', Failed: 'danger' };
        var body = orders.map(function (item) {
          var badge = badgeMap[item.status] || 'secondary';
          return '<tr>' +
            '<td>' + item.id + '</td>' +
            '<td>' + $('<span>').text(item.customer).html() + '</td>' +
            '<td>Rp ' + new Intl.NumberFormat('id-ID').format(item.amount) + '</td>' +
            '<td><span class="badge text-bg-' + badge + '">' + item.status + '</span></td>' +
            '</tr>';
        }).join('');
        $('#ordersPreview tbody').html(body);
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to load orders';
        alert(msg);
      })
      .always(function () {
        $btn.prop('disabled', false).text('Refresh via jQuery');
      });
  });

  // --- Reports: real CSV export download ---
  $('#simulateExport').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).text('Generating…');
    // Trigger a real file download via a hidden iframe/link
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

  // --- Settings: save via AJAX to real endpoint ---
  $('#settingsForm').on('submit', function (event) {
    event.preventDefault();
    var $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).text('Saving…');
    var payload = $(this).serialize();
    // Add notification toggle value explicitly
    payload += '&notifications=' + ($('#notifSwitch').is(':checked') ? '1' : '0');
    $.post('/panel/api/settings.php', payload)
      .done(function (res) {
        $('#settingsFeedback').removeClass('text-danger').addClass('text-gold').text(res.message);
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save settings';
        $('#settingsFeedback').removeClass('text-gold').addClass('text-danger').text(msg);
      })
      .always(function () {
        $btn.prop('disabled', false).text('Save changes');
      });
  });
});
