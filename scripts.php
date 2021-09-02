<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="plugins/morris/morris.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="plugins/datepicker/locales/bootstrap-datepicker.fr.js"></script>
<!-- bootstrap color picker -->
<script src="plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.js"></script>
<!-- Select2 -->
<script src="plugins/select2/select2.full.min.js"></script>
<!--Datetime picker-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/locale/fr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<!-- DatePicker PERSO -->
<script>
    $(function () {
        $('.input-datepicker').datepicker({format: 'yyyy-mm-dd', language: 'fr'});

        $('.input-datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            locale: 'fr'
        });

        $('body').on('click', '.modal-form', function (e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('href'),
                success: function (html) {
                    var modal = $(html);
                    $('body').append(modal);

                    modal.modal().on('hidden.bs.modal', function () {
                        $(this).remove();
                    });

		    modal.on('shown.bs.modal', function () {
                        $('.input-datepicker').datepicker({format: 'yyyy-mm-dd', language: 'fr'});

			$('.input-datetimepicker').datetimepicker({
			    format: 'YYYY-MM-DD HH:mm',
			    locale: 'fr'
			});

			//Initialize Select2 Elements
			$(".select2").select2();
			listenSelect('select[name="emplacementLot"]', 'select[name="libelleSac"]');
			$('select[name="libelleSac"]').children('option').prop('disabled', true);

			listenSelect('select[name="materielLot"]', 'select[name="materielSac"]');
			listenSelect('select[name="materielSac"]', 'select[name="libelleEmplacement"]');
			$('select[name="materielSac"], select[name="libelleEmplacement"]').children('option').prop('disabled', true);
                    });
                }
            });
            return false;
        });
        
        //Colorpicker
		$(".my-colorpicker1").colorpicker();
    });
</script>

<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>

<script>
    $(function () {
        $("#tri0").DataTable({
        	"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
        	"ordering": false,
        	"pageLength": <?= $_SESSION['tableRowPerso'] ?>,
        	lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
        	responsive: true
        	});
        $("#tri1").DataTable({
        	"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
        	"order": [[ 0, 'asc']],
        	"pageLength": <?= $_SESSION['tableRowPerso'] ?>,
        	lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
        	responsive: true});
        $("#tri1R").DataTable({
        	"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
        	"order": [[ 0, 'desc']],
        	"pageLength": <?= $_SESSION['tableRowPerso'] ?>,
        	lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
        	responsive: true});
        $("#tri2").DataTable({
        	"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
        	"order": [[ 1, 'asc']],
        	"pageLength": <?= $_SESSION['tableRowPerso'] ?>,
        	lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
        	responsive: true});
        $("#tri2R").DataTable({
        	"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
        	"order": [[ 1, 'desc']],
        	"pageLength": <?= $_SESSION['tableRowPerso'] ?>,
        	lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
        	responsive: true});

        $('#tri0F').DataTable( {
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
     
                    column.data().unique().sort().each( function ( d, j ) {
                        d = d.replace(/<[^>]+>/g, '');
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            },
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
            "ordering": false,
            "pageLength": <?= $_SESSION['tableRowPerso'] ?>,
            lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
            responsive: true
        } );
        $('#tri1F').DataTable( {
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
     
                    column.data().unique().sort().each( function ( d, j ) {
                        d = d.replace(/<[^>]+>/g, '');
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            },
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
            "order": [[ 0, 'asc']],
            "pageLength": <?= $_SESSION['tableRowPerso'] ?>,
            lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
            responsive: true
        } );
        $('#tri1RF').DataTable( {
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
     
                    column.data().unique().sort().each( function ( d, j ) {
                        d = d.replace(/<[^>]+>/g, '');
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            },
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
            "order": [[ 0, 'desc']],
            "pageLength": <?= $_SESSION['tableRowPerso'] ?>,
            lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
            responsive: true
        } );
        $('#tri2F').DataTable( {
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
     
                    column.data().unique().sort().each( function ( d, j ) {
                        d = d.replace(/<[^>]+>/g, '');
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            },
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
            "order": [[ 1, 'asc']],
            "pageLength": <?= $_SESSION['tableRowPerso'] ?>,
            lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
            responsive: true
        } );
        $('#tri2RF').DataTable( {
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
     
                    column.data().unique().sort().each( function ( d, j ) {
                        d = d.replace(/<[^>]+>/g, '');
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            },
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"},
            "order": [[ 1, 'desc']],
            "pageLength": <?= $_SESSION['tableRowPerso'] ?>,
            lengthMenu: [
                [ 10, 25, 50, 75, 100, -1 ],
                [ '10', '25', '50', '75', '100', 'Tous' ]
            ],
            responsive: true
        } );
    });

    $(function () {
        $('.table tr[data-href]').on('click', function(){
        //window.location.href = $(this).data('href'); // Désactivé pour l'accès aux boutons de fin de ligne
        });
    }); 
</script>


<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
//        listenSelect('select[name="emplacementLot"]', 'select[name="libelleSac"]');
        $('select[name="libelleSac"]').children('option').prop('disabled', true);

//        listenSelect('select[name="materielLot"]', 'select[name="materielSac"]');
//        listenSelect('select[name="materielSac"]', 'select[name="libelleEmplacement"]');
        $('select[name="materielSac"], select[name="libelleEmplacement"]').children('option').prop('disabled', true);
    });
</script>
<script>
    $('body').on('submit', '.spinnerAttenteSubmit', function() {
        $("body").prepend('<div class="modal-backdrop in" ><i class="fa fa-refresh fa-spin" style="margin: auto; z-index: 4000000; position: fixed; left: calc(50% - 25px); top: calc(50% - 25px); font-size: 50px; "></i></div>');
    });
    $('body').on('click', '.spinnerAttenteClick', function() {
        $("body").prepend('<div class="modal-backdrop in" ><i class="fa fa-refresh fa-spin" style="margin: auto; z-index: 4000000; position: fixed; left: calc(50% - 25px); top: calc(50% - 25px); font-size: 50px; "></i></div>');
    });
</script>

<script>
	$('.textareaHTML').wysihtml5();
</script>