{{ Form::model($sale_term, array('route' => array('quotes-v2.saleterms.update', $saleterm->id), 'method' => 'POST')) }}
<div class="row">
    <input  type="hidden" name="quote_id" value="{{$quote->id}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
    <input  type="hidden" name="quote_type" value="{{$quote->type}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
    <div class="col-md-4" >
        <div id="origin_harbor_label">
            <label>Type</label>
            {{ Form::select('type',['origin'=>'Origin','destination'=>'Destination'],$sale_term->type,['placeholder'=>'Select an option','class'=>'m-select2-general form-control','id'=>'saleterm_type']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div id="destination_harbor_label" >
            <label>Port</label>
            <div  {{$quote->type=='AIR' ? 'hidden':''}}>
                <div class="origin_port hide">
                    <label>Origin Port</label>
                    {{ Form::select('port_id',$rate_origin_ports,$sale_term->port_id,['class'=>'m-select2-general form-control origin_port_select','placeholder'=>'Select an option']) }}
                </div>                        
                <div class="destination_port hide">
                    <label>Destination Port</label>
                    {{ Form::select('port_id',$rate_destination_ports,$sale_term->port_id,['class'=>'m-select2-general form-control destination_port_select','placeholder'=>'Select an option']) }}
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="form-group m-form__group">
    <button type="submit" class="btn btn-primary">Update</button>
</div>
<br>
{!! Form::close() !!}
<script type="text/javascript">
    $('.m-select2-edit').select2({
        placeholder: "Select an option"
    });
</script>