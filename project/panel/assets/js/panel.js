$(function () {
  $('#orderFilter').on('input', function () {
    const query = $(this).val().toString().toLowerCase();
    $('#ordersTable tbody tr').each(function () {
      const text = $(this).find('.filter-target').text().toLowerCase();
      $(this).toggle(text.includes(query));
    });
  });

  $('#saveUser').on('click', function () {
    const payload = $('#addUserForm').serialize();
    $.post('/panel/api/add-user.php', payload)
      .done((res) => {
        $('#userFeedback').text(res.message);
      })
      .fail((xhr) => {
        $('#userFeedback').text(xhr.responseJSON?.message || 'Unable to add user');
      });
  });

  $('#refreshOrders').on('click', function () {
    $.getJSON('/panel/api/orders.php', function (orders) {
      const body = orders.map((item) => `<tr>
        <td>${item.id}</td>
        <td>${item.customer}</td>
        <td>Rp ${new Intl.NumberFormat('id-ID').format(item.amount)}</td>
        <td><span class="badge text-bg-secondary">${item.status}</span></td>
      </tr>`).join('');
      $('#ordersPreview tbody').html(body);
    });
  });

  $('#simulateExport').on('click', function () {
    $('#exportFeedback').text('CSV generated successfully (simulation).');
  });

  $('#settingsForm').on('submit', function (event) {
    event.preventDefault();
    $('#settingsFeedback').text('Settings saved successfully.');
  });
});
