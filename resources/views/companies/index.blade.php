@extends('layouts.app')
@section('title', 'Companies')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/plugins/button-dropdown/css/bootstrap.css">
<script src="/assets/plugins/button-dropdown/js/jquery3.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/assets/plugins/button-dropdown/js/bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('content')

<div class="m-content">
    <div class="dropdown show" align="right" style="margin-bottom:20px;margin-right:20px;">
        <a class="dropdown-toggle" style="font-size:16px" href="#" role="button" id="helpOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            See how it works
        </a>

        <div 
            class="dropdown-menu" 
            aria-labelledby="helpOptions"
        >
            <a class="dropdown-item" target="_blank" href="https://support.cargofive.com/clients-companies-and-contacts/"> 
                Companies and Contacts 
            </a>
            <a class="dropdown-item" target="_blank" href="https://support.cargofive.com/how-to-add-a-customer/" style="overflow-x:hidden;"> 
                How to add a customer 
            </a>
        </div>
    </div>
    <div class="m-portlet m-portlet--mobile">
        @if(Session::has('message.nivel'))
            <div class="col-md-12">
                <br>
                <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
                    <div class="m-alert__icon">
                        <i class="la la-warning"></i>
                    </div>
                    <div class="m-alert__text">
                        <strong>
                            {{ session('message.title') }}
                        </strong>
                        {{ session('message.content') }}
                    </div>
                    <div class="m-alert__close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    
                    <div class="col-12 order-1 order-xl-2 m--align-right">
                        <button type="button" dusk="addCompany" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                            <span>
                                <span>
                                    Add Company
                                </span>
                                &nbsp;
                                <i class="la la-plus"></i>
                            </span>
                        </button>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Importation
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -136px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalupload">
                                <span>
                                    <i class="la la-upload"></i>
                                    &nbsp;
                                    Upload Companies
                                </span>
                            </a>      
                            <a href="{{route('DownLoad.Files',1)}}" class="dropdown-item" >
                                <span>
                                    <i class="la la-download"></i>
                                    &nbsp;
                                    Download File
                                </span>
                            </a>
                            <a href="{{route('view.fail.company')}}" class="dropdown-item" >
                                <span>
                                    <i class="la la-search"></i>
                                    &nbsp;
                                    Failed Companies
                                </span>
                            </a>
                        </div>
                        @if(!empty($api) && $api->enable==1)
                            @if(@$api->status==0)
                                <a href="javascript:void(0)" id="syncCompanies" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                    <span>
                                        <span>
                                            Fetch from API
                                        </span>
                                        &nbsp;
                                        <i class="la la-refresh"></i>
                                    </span>
                                </a>
                                <a href="#" id="syncCompaniesLoading" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill hide disabled">
                                    <span>
                                        <span>
                                            Fetching data
                                        </span>
                                        &nbsp;
                                        <i class="la la-refresh la-spin"></i>
                                    </span>
                                </a>
                            @else
                                <a href="#" id="syncCompaniesLoading" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill disabled">
                                    <span>
                                        <span>
                                            Fetching data
                                        </span>
                                        &nbsp;
                                        <i class="la la-refresh la-spin"></i>
                                    </span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <table class="table tableData" id="table-company" width="100%">
                <thead >
                    <tr class="title-quote">
                        <th title="Id">
                            Company Id
                        </th>
                        <th title="business_name">
                            Business name
                        </th>
                        <th title="phone">
                            Phone
                        </th>
                        <th title="email">
                            Email
                        </th>
                        <th title="tax_number">
                            Tax number
                        </th>
                        <th title="address">
                            Address
                        </th>
                        <th title="extra">
                            Extra
                        </th>
                        <th title="action">
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="modalupload"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Upload Companies
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div id="edit-modal-body-E" class="modal-body-E">
                    <br>
                    {!! Form::open(['route' => 'Upload.Company', 'method' => 'POST', 'files' => 'true'])!!}

                    <div class="form-group row pull-right">
                        <div class="col-md-3 ">

                        </div>
                    </div>
                    <div class="form-group row ">
                        <div class="col-md-1 "></div>
                        <div class="col-md-6 ">
                            <input type="file" name="file" value="Load File" required />
                        </div>
                    </div>
                </div>
                <div id="edit-modal-body" class="modal-footer">
                    {!! Form::submit('Load', ['class'=> 'btn btn-primary']) !!}
                    <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">Cancel</span>
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@include('companies.partials.companiesModal')
@include('companies.partials.deleteCompaniesModal')
@endsection

@section('js')
@parent

<script type="text/javascript" charset="utf8" src="{{ asset('/assets/datatable/jquery.dataTables.js')}}"></script>
<script src="{{ asset('/assets/demo/default/custom/components/datatables/base/html-table-companies.js')}}" type="text/javascript"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="{{asset('js/companies.js')}}" type="text/javascript"></script>
<script>
    function AbrirModal(action,id){
        if(action == "edit"){
            var url = '{{ route("companies.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#companyModal').modal({show:true});
            });
        }if(action == "add"){
            var url = '{{ route("companies.add") }}';
            $('.modal-body').load(url,function(){
                $('#companyModal').modal({show:true});
            });
        }
        if(action == "delete"){
            var url = '{{ route("companies.delete", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#deleteCompanyModal').modal({show:true});
            });
        }
    }

    $(function() {
        $('#table-company').DataTable({
            ordering: true,
            searching: true,
            processing: false,
            serverSide: false,
            order: [[ 3, "desc" ]],
            ajax:  "{{ route('companies.index.datatable') }}",
            "columnDefs": [
                { "width": "20%", "targets": 0 },
                { "width": "15%", "targets": 5 },
            ],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'business_name', name: 'business_name'},
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'tax_number', name: 'tax_number'},
                {data: 'address', name: 'address'},
                {data: 'extra', name: 'extra'},
                {data: 'action', name: 'action', orderable: false, searchable: false },
            ] ,
            "autoWidth": true,
            'overflow':false,
            "paging":true,
            "sScrollY": "490px",
            "bPaginate": false,
            "bJQueryUI": true,
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ]


        });

        // Add event listener for opening and closing details
        $('#tablequote tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        } );
    });

</script>
<script>    
    //parseamos las compañias que son inyectadas desde el controlador
    const companies = @json($companies);

    //escuchamos el evento input sobre el campo que corresponde al business_name
    document.querySelector('body').addEventListener('input', function(event) {
        if (event.target.getAttribute('id') === 'business_name') {
            
            let valueInputCompany = event.target.value;
            let similarityList = document.getElementById('similarityList');
            let companyMessage = document.getElementById('companyMessage');
            companyMessage.style.display = "none";            
            companyMessage.innerHTML = '';
            similarityList.style.display = "none";            
            similarityList.innerHTML = '';               
            similarityList.innerHTML = `<p style="margin-bottom:4px">Similar companies:</p>`;  

            let compSimilares = [];
            
            companies.forEach(company => {

                let businessName = company.business_name.toLowerCase().trim();
                let valueInput = valueInputCompany.toLowerCase().trim();

                //calculamos la similitud del valor escrito con cada uno de las compañías
                let similarityValue = similarity(valueInput, businessName);                
                
                //Si es 1, existe una compaía igual
                if(similarityValue === 1){   
                    companyMessage.style.display = "block";    
                    companyMessage.innerHTML = `Alert: A company with the same name already exists! If it is what you want you can continue. <br><br>`;
                } else {
                    //se muestra las compañías con similitud superior a 0.5. Donde 1 es idéntico y 0 representa ninguna similitud.
                    if(similarityValue > 0.6){                         
                        compSimilares.push(businessName);
                    }
                }    
                //Validamos q se haya ingresado al menos 4 caracteres y buscamos coincidencias en ls compañías
                if(valueInput.length >= 4){
                    if(businessName.indexOf(valueInput) >= 0) {
                        compSimilares.push(businessName);
                    }
                }             
            });

            renderSimilarCompanies(removeDuplicate(compSimilares), similarityList);
        }
    });
    //eliminar compañías imilares
    function removeDuplicate(compSimilares){
        const dataArr = new Set(compSimilares);
        return [...dataArr];
    }
    //render compañías similares
    function renderSimilarCompanies(compSimilares, similarityList){
        similarityList.style.display = "block"; 
        let comp =  compSimilares.slice(0, 20);
        comp.forEach(c => {
            let p = document.createElement("p");
            p.style.margin = "1px";    
            p.textContent = `${c}`;
            similarityList.appendChild(p);
        });
        
    }
    //función que recibe dos valores y devuelve la similitud entre ellos (0 a 1)
    function similarity(s1, s2) {
        var longer = s1;
        var shorter = s2;
        if (s1.length < s2.length) {
            longer = s2;
            shorter = s1;
        }
        var longerLength = longer.length;
        if (longerLength == 0) {
            return 1.0;
        }
        return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
    }
    //medir la distancia de edición 
    function editDistance(s1, s2) {
        s1 = s1.toLowerCase();
        s2 = s2.toLowerCase();

        var costs = new Array();
        for (var i = 0; i <= s1.length; i++) {
            var lastValue = i;
            for (var j = 0; j <= s2.length; j++) {
            if (i == 0)
                costs[j] = j;
            else {
                if (j > 0) {
                var newValue = costs[j - 1];
                if (s1.charAt(i - 1) != s2.charAt(j - 1))
                    newValue = Math.min(Math.min(newValue, lastValue),
                    costs[j]) + 1;
                costs[j - 1] = lastValue;
                lastValue = newValue;
                }
            }
            }
            if (i > 0)
            costs[s2.length] = lastValue;
        }
        return costs[s2.length];
    }


</script>
@stop