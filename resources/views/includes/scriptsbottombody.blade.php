@section('js')
  <!--begin::Base Scripts -->
  <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
  <script src="/assets/vendors/base/vendors.bundle.min.js" type="text/javascript"></script>
  <script src="/assets/demo/default/base/scripts.bundle.min.js" type="text/javascript"></script>
  <!--end::Base Scripts -->
  <!--begin::Page Vendors -->
  <script src="/assets/vendors/custom/fullcalendar/fullcalendar.bundle.min.js" type="text/javascript"></script>
  <!--end::Page Vendors -->
  <!--begin::Page Snippets -->
  <script src="/assets/app/js/dashboard.min.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/forms/validation/form-controls.js" type="text/javascript"></script>

  <script src="/assets/demo/default/custom/components/forms/wizard/wizard.min.js" type="text/javascript"></script>
  @if(Auth::check())
    <script>
      var userId = {{ Auth::user()->id }}
    </script>

    <script>

      var APP_ID = "s9q3w42n";
      var current_user_email =  '{{\Auth::user()->email}}';
      var current_user_name = '{{ \Auth::user()->name }} {{\Auth::user()->lastname }}';
      var current_user_id =  '{{ \Auth::user()->id }}';

      window.intercomSettings = {
        app_id: APP_ID,
        name: current_user_name, // Full name
        email: current_user_email, // Email address
        user_id: current_user_id // current_user_id
      };
      (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/' + APP_ID;var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();

    </script>
  @endif
@show
