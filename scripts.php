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
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/select2.full.min.js"></script>
<!--Datetime picker-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/locale/fr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<!--Select2 dépendants-->
<script>
    function listenSelect(listenedSelector, listenerSelector) {
        $(listenedSelector).change(function () {
            $(listenerSelector).val(null).trigger('change');

            $(listenerSelector).children().prop('disabled', true);
            $(listenerSelector).children('[value=""], [data-id="'+$(listenedSelector).val()+'"]').prop('disabled', false);
            $(listenerSelector).select2('destroy').select2();
        });
    }
</script>

<!-- DatePicker PERSO -->
<script>
    $(function () {
        $('.input-datepicker').datepicker({format: 'yyyy-mm-dd', language: 'fr'});

        $('.input-datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            locale: 'fr'
        });

        $('.modal-form').click(function (e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('href'),
                success: function (html) {
                    var modal = $(html);
                    $('body').append(modal);

                    modal.modal().on('hidden.bs.modal', function () {
                        $(this).remove();
                    });
                }
            });
        });
    });
</script>

<script language="JavaScript">
        function montrer_cacher(laCase,leCalk,leCalk2)
        {
            if (laCase.checked) //la case est cochée -> on montre le calque
            {
                document.getElementById(leCalk).style.visibility="visible";
                document.getElementById(leCalk2).style.visibility="visible";
            }
            else //la case n'est pas cochée -> on cache le calque
            {
                document.getElementById(leCalk).style.visibility="hidden";
                document.getElementById(leCalk2).style.visibility="hidden";
            }
        }
</script>

<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>

<script>
    $(function () {
        $("#tri0").DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"}, "ordering": false, responsive: true});
        $("#tri1").DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"}, "order": [[ 0, 'asc']], responsive: true});
        $("#tri2").DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"}, "order": [[ 1, 'asc']], responsive: true});
        $("#tri2R").DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"}, "order": [[ 1, 'desc']], responsive: true});
    });
</script>


<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
        listenSelect('select[name="emplacementLot"]', 'select[name="libelleSac"]');
        $('select[name="libelleSac"]').children('option').prop('disabled', true);

        listenSelect('select[name="materielLot"]', 'select[name="materielSac"]');
        listenSelect('select[name="materielSac"]', 'select[name="libelleEmplacement"]');
        $('select[name="materielSac"], select[name="libelleEmplacement"]').children('option').prop('disabled', true);
    });
</script>
<script>
    $(function () {
        $(".checkAll").change(function () {
            $("input:checkbox."+ $(this).attr('id')).prop('checked', $(this).prop("checked"));
        });
    });
</script>
