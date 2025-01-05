langue = $('#langue').val() ;
if(langue=='ar-AR'){langue='ar'}
langue_file = "https://cdn.datatables.net/plug-ins/1.13.1/i18n/"+langue+".json" ;
//langue_file = "https://localhost:8000/build/"+langue+".json" ;

$('#example').DataTable( {
    language: {
        url: langue_file,
    },
    stateSave: true,
    "bDestroy": true,
    order: [[0, 'desc']]
} );

$('#example7').DataTable( {
    language: {
        url: langue_file,
    },
    stateSave: true,
    "bDestroy": true,
    order: [[0, 'desc']]
} );
$('#example_rh').DataTable( {
    language: {
        url: langue_file,
    },
    stateSave: true,
    "bDestroy": true,
    order: [[0, 'desc']]
} );

$('#example_om_v').DataTable( {
    language: {
        url: langue_file,
    },
    stateSave: true,
    "bDestroy": true,
    order: [[0, 'desc']]
} );

$('#example_conge_v').DataTable( {
    language: {
        url: langue_file,
    },
    stateSave: true,
    "bDestroy": true,
    order: [[0, 'desc']]
} );
$('#example_fh_v').DataTable( {
    language: {
        url: langue_file,
    },
    stateSave: true,
    "bDestroy": true,
    order: [[0, 'desc']]
} );
$('#example_auto_v').DataTable( {
    language: {
        url: langue_file,
    },
    stateSave: true,
    "bDestroy": true,
    order: [[0, 'desc']]
} );


