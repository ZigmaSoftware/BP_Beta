<!-- Vendor core (choose ONE approach) -->
<script src="assets/js/vendor.min.js<?php echo $js_css_file_comment; ?>"></script>
<?php /* If vendor.min.js does NOT include jQuery+Bootstrap, comment the line above and UNCOMMENT the two lines below:
<script src="https://code.jquery.com/jquery-3.7.1.min.js<?php echo $js_css_file_comment; ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js<?php echo $js_css_file_comment; ?>"></script>
*/ ?>

<!-- Flatpickr (local) -->
<script src="assets/libs/flatpickr/flatpickr.min.js<?php echo $js_css_file_comment; ?>"></script>

<?php if (session_id() AND ($user_id)) { ?>

  <!-- ✅ DataTables (official CDN; replaces all coderthemes links) -->
  <!--<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js<?//php //echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js<//?php //echo $js_css_file_comment; ?>"></script>-->

  <!--<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js<?//php //echo $js_css_file_comment; ?>"></script>-->

  <!--<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js<//?php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js<?//php echo $js_css_file_comment; ?>"></script>-->

  <!--<script src="https://cdn.datatables.net/keytable/2.11.0/js/dataTables.keyTable.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js<?//php echo $js_css_file_comment; ?>"></script>-->

  <!--<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!-- Export deps -->
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!--<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  <!-- ⚠️ Do NOT include buttons.flash.min.js (Flash is obsolete) -->

  <!-- Bootstrap Wizard (replace coderthemes demo URLs) -->
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap-wizard/1.4.2/jquery.bootstrap.wizard.min.js<?//php echo $js_css_file_comment; ?>"></script>-->
  
  <script src="../../assets/datatables/jquery.dataTables.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/dataTables1.bootstrap5.min.js<?php echo $js_css_file_comment; ?>"></script>

  <script src="../../assets/datatables/dataTables.responsive.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/responsive.bootstrap5.min.js<?php echo $js_css_file_comment; ?>"></script>

  <script src="../../assets/datatables/dataTables.fixedColumns.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/dataTables.fixedHeader.min.js<?php echo $js_css_file_comment; ?>"></script>

  <script src="../../assets/datatables/dataTables.keyTable.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/dataTables.select.min.js<?php echo $js_css_file_comment; ?>"></script>

  <script src="../../assets/datatables/dataTables.buttons.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/buttons.bootstrap5.min.js<?php echo $js_css_file_comment; ?>"></script>
  <!-- Export deps -->
  <script src="../../assets/datatables/jszip.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/pdfmake.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/vfs_fonts.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/buttons.html5.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="../../assets/datatables/buttons.print.min.js<?php echo $js_css_file_comment; ?>"></script>
  <!-- ⚠️ Do NOT include buttons.flash.min.js (Flash is obsolete) -->

  <!-- Bootstrap Wizard (replace coderthemes demo URLs) -->
  <script src="../../assets/datatables/jquery.bootstrap.wizard.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script>
    // Minimal init to replace the old demo.form-wizard.js
    (function ($) {
      $(function () {
        var $wizard = $('.bootstrap-wizard'); // adjust selector to your form wizard container
        if ($wizard.length && $.fn.bootstrapWizard) {
          $wizard.bootstrapWizard({
            onTabShow: function (tab, nav, index) {
              var total = nav.find('li').length;
              var current = index + 1;
              var pct = (current / total) * 100;
              $wizard.find('.progress-bar').css({ width: pct + '%' });
            }
          });
        }
      });
    })(jQuery);
  </script>

  <!-- Select2 / Autocomplete / SweetAlert (local) -->
  <script src="assets/libs/select2/js/select2.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="assets/libs/autocomplete/js/autocomplete.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="assets/libs/sweetalert2/sweetalert2.all.min.js<?php echo $js_css_file_comment; ?>"></script>

  <?php if ($folder_name_org == 'dashboard') { ?>
    <script src="assets/libs/morris/morris.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="assets/libs/raphael/raphael.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="assets/libs/chart.js/Chart.bundle.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="assets/libs/chart.js/chartjs-gauge.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="assets/libs/amcharts4/core.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="assets/libs/amcharts4/charts.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="assets/libs/amcharts4/themes/animated.js<?php echo $js_css_file_comment; ?>"></script>
  <?php } ?>

  <!-- Page-specific JS -->
  <script src="<?php echo 'folders/'.$folder_name_org.'/'.$folder_name_org; ?>.js<?php echo $js_css_file_comment; ?>"></script>

<?php } else { ?>

  <!-- Login-only libs -->
  <script src="assets/libs/particlesjs/js/lib/particles.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="assets/libs/sweetalert2/sweetalert2.all.min.js<?php echo $js_css_file_comment; ?>"></script>
  <script src="<?php echo 'folders/'.$folder_name_org.'/'.$folder_name_org; ?>.js<?php echo $js_css_file_comment; ?>"></script>

<?php } ?>

<!-- Google Maps (ok) -->
<script src="https://maps.google.com/maps/api/js?key=AIzaSyCEDMbFnE7uOxsVb5nzzZGTMImFZ_Fu7Ko&libraries=geometry"></script>
<script src="assets/libs/jquery_locationpicker/locationpicker.jquery.min.js<?php echo $js_css_file_comment; ?>"></script>

<!-- Common + App -->
<script src="assets/js/common.js<?php echo $js_css_file_comment; ?>"></script>
<script src="assets/js/app.min.js<?php echo $js_css_file_comment; ?>"></script>
