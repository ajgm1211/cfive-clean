            {{ Form::model($inland, array('route' => array('quotes-v2.inlands.update', $inland->id), 'method' => 'POST')) }}
                <div class="row">
                    <input  type="hidden" name="quote_id" value="{{$quote->id}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <input  type="hidden" name="quote_type" value="{{$quote->type}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <input  type="hidden" name="automatic_rate_id" value="{{$inland->automatic_rate_id}}" class="btn btn-sm btn-default btn-bold btn-upper automatic_rate_id">
                    <div class="col-md-4" >
                        <div id="origin_harbor_label">
                          <label>Port</label>
                          {{ Form::select('port_id',$harbors,$inland->port_id,['class'=>'m-select2-edit form-control','required'=>'true']) }}
                        </div>
                        <div id="origin_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                          <label>Airport</label>
                          <select id="origin_airport" name="origin_airport_id" class="form-control" {{$quote->type=='AIR' ? 'required':''}}></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="destination_harbor_label" >
                          <label>Type</label>
                          {{ Form::select('type',['Origin'=>'Origin','Destination'=>'Destination'],null,['class'=>'m-select2-edit form-control','required'=>'true']) }}
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div id="destination_harbor_label" >
                          <label>Charge</label>
                          <input type="text" name="provider" value="{{$inland->provider}}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4" class="" id="carrier_label"> 
                        <label>Currency</label>
                        {{ Form::select('currency_id',$currencies,$inland->currency_id,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-edit','required'=>'true']) }}
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