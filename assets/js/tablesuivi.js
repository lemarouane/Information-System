$(function () {

  langue = $('#langue').val() ;
  if(langue=='ar-AR'){langue='ar'}
  langue_file = "https://cdn.datatables.net/plug-ins/1.13.1/i18n/"+langue+".json" ;

    $('#example3').DataTable({
        language: {
          url: langue_file,
        },
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false
      })
    $('#example4').DataTable({
      language: {
        url: langue_file,
      },
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  });