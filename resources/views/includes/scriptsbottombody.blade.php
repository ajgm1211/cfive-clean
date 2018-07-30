@section('js')
<!--begin::Base Scripts -->

<script src="/assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
<script src="/assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
<!--end::Base Scripts -->
<!--begin::Page Vendors -->
<script src="/assets/vendors/custom/fullcalendar/fullcalendar.bundle.js" type="text/javascript"></script>
<!--end::Page Vendors -->
<!--begin::Page Snippets -->
<script src="/assets/app/js/dashboard.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/validation/form-controls.js" type="text/javascript"></script>

<script src="/assets/demo/default/custom/components/forms/wizard/wizard.js" type="text/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>

<script>
  $(document).ready(function(){
    $.get('/users/notifications', function (data) {
    
      data.map(function (notification) {
        //alert(notification.data.id_user);
        
          $('.notifications').html("<div class='m-list-timeline__item'> <span class='m-list-timeline__badge'></span><span class='m-list-timeline__text'>El usuario "+notification.data.name_user+" Agrego el contrato numero "+notification.data.number_contract+" </span> <span class='m-list-timeline__time'> </span> </div>");
      });

    });

  });
</script>

<!--end::Page Snippets -->

@show
