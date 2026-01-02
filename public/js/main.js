$(document).ready(function () {
  window._token = $('meta[name="csrf-token"]').attr('content')

  moment.updateLocale('en', {
    week: {dow: 1} // Monday is the first day of the week
  })

  // Tempus Dominus DateTime Picker (Bootstrap 5 compatible)
  // Note: Tempus Dominus has a different API than bootstrap-datetimepicker
  // For now, keeping basic date/time functionality
  // Full Tempus Dominus integration can be added later if needed
  if (typeof tempusDominus !== 'undefined') {
    // Tempus Dominus initialization would go here
    // Example: new tempusDominus.TempusDominus(document.getElementById('date'), { ... })
  }

  // Legacy datetimepicker support (if still needed)
  if (typeof $.fn.datetimepicker !== 'undefined') {
  $('.date').datetimepicker({
    format: 'DD-MM-YYYY',
    locale: 'en',
    icons: {
      up: 'fas fa-chevron-up',
      down: 'fas fa-chevron-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right'
    }
  })

  $('.datetime').datetimepicker({
    format: 'DD-MM-YYYY HH:mm:ss',
    locale: 'en',
    sideBySide: true,
    icons: {
      up: 'fas fa-chevron-up',
      down: 'fas fa-chevron-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right'
    }
  })

  $('.timepicker').datetimepicker({
    format: 'HH:mm:ss',
    icons: {
      up: 'fas fa-chevron-up',
      down: 'fas fa-chevron-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right'
    }
  })
  }

  $('.select-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', 'selected')
    $select2.trigger('change')
  })
  
  $('.deselect-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', '')
    $select2.trigger('change')
  })

  $('.select2').select2()

  // AdminLTE 4 sidebar toggle
  $('a[data-lte-toggle="sidebar"]').click(function () {
    setTimeout(function() {
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    }, 350);
  })
  
  // Legacy support for data-widget (if needed during transition)
  $('a[data-widget^="pushmenu"]').click(function () {
    setTimeout(function() {
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    }, 350);
  })
})
