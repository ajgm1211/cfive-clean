@section('js')
<!--begin::Base Scripts -->
<script src="{{ asset('/js/app.js')}}" type="text/javascript"></script>

<script src="{{ asset('/assets/vendors/base/vendors.bundle.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/demo/default/base/scripts.bundle.min.js')}}" type="text/javascript"></script>
<!--end::Base Scripts -->
<!--begin::Page Vendors -->
<script src="{{ asset('/assets/vendors/custom/fullcalendar/fullcalendar.bundle.min.js')}}" type="text/javascript"></script>
<!--end::Page Vendors -->
<!--begin::Page Snippets -->
<script src="{{ asset('/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/demo/default/custom/components/forms/validation/form-controls.js')}}" type="text/javascript"></script>

<script src="{{ asset('/assets/demo/default/custom/components/forms/wizard/wizard.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('js/jqueryui-editable.min.js')}}" type="text/javascript"></script>




@if(Auth::check())

<!--
<script type="text/javascript">
  $(document).ready(function() {
    $(".dropdown-toggle").dropdown();
  });
  $crisp = [];
  CRISP_TOKEN_ID = '{{ Auth::user()->people_key  }}';                      
  CRISP_WEBSITE_ID = '011f006f-3864-44b5-9443-d700e87df5f7';
  (function(){d=document;s=d.createElement('script');s.src='//client.crisp.chat/l.js';s.async=1;d.getElementsByTagName('head')[0].appendChild(s);})();
</script>

<script src="{{ asset('/js/crisp.js?v=1')}}" email="{{ Auth::user()->email }}" type="text/javascript"></script>-->



   

<script>
  $(document).ready(function() {
    $(".dropdown-toggle").dropdown();
  });
  var userId = {{ Auth::user()->id }}


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

    (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/s9q3w42n' ;var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();


  
    @if(Session::has('toastr'))
    var type = "{{ Session::get('alert-type', 'info') }}";
  toastr.options = {
    "progressBar": true,
    "positionClass": "toast-top-right"
  }    
  switch(type){
    case 'info':
      toastr.info("{{ Session::get('toastr') }}");
      break;

    case 'warning':
      toastr.warning("{{ Session::get('toastr') }}");
      break;

    case 'success':
      toastr.success("{{ Session::get('toastr') }}");
      break;

    case 'error':
      toastr.error("{{ Session::get('toastr') }}");
      break;
  }
  @endif

</script>
@endif
@show
