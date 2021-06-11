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

<script src="{{ asset('/assets/demo/default/custom/components/forms/wizard/wizard.js')}}" type="text/javascript"></script>
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

-->



<script src="{{ asset('/js/intercom.js?v=4')}}" email="{{ \Auth::user()->email }}" name="{{ \Auth::user()->name }}  {{\Auth::user()->lastname }} " id="{{ \Auth::user()->id }}" company="{{ @\Auth::user()->company_user_id }}" companyName="{{ @\Auth::user()->companyUser->name }}"  type="text/javascript"></script>   


<script>
  $(document).ready(function() {
    
    $(".dropdown-toggle").dropdown();
  });
  var userId = {{ Auth::user()->id }}



    
 
  
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
