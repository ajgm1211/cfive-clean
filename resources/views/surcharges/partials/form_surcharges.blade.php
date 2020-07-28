<div class="form-group row m-form__group">
    @if($is_admin)
        <div class="col-lg-5">
    @else
        <div class="col-lg-6">
    @endif
            {!! Form::label('name', 'Name Surharge') !!}
            {!! Form::text('name', null, ['placeholder' => 'Please enter surcharge name','class' => 'form-control
            m-input','required' => 'required']) !!}
        </div>

        @if($is_admin)
            <div class="col-lg-5">
        @else
            <div class="col-lg-6">
        @endif
                {!! Form::label('description', 'Description') !!}
                {!! Form::text('description', null, ['placeholder' => 'Please enter description of surcharfe','class' =>
                'form-control m-input','required' => 'required']) !!}
            </div>
            @if($is_admin)
            <div class="col-lg-2">
                <a href="#" class="btn btn-primary " onclick="agregarcampo()"> Add <span class="la la-plus"></span></a>
            </div>
            @endif
        </div>
        <input type="hidden" name="is_admin" value="{{$is_admin}}">
        @if($is_admin)
            <div class="form-group row" id="variatiogroup">
                @if(count($decodejosn) == 0)
                    <div class="col-lg-4">
                        <label for="DispNamMD" class="form-control-label">
                            Variation:
                        </label>
                        <input type="text" name="variation[]" class="variationMD form-control">
                    </div>
                @else
                    @foreach($decodejosn as $nameVaration)
                        @if($nameVaration != '')
                            <div class="col-lg-4">
                                <label for="DispNamMD" class="form-control-label">
                                    Variation:
                                </label>
                                <input type="text" name="variation[]" value="{{$nameVaration}}" class="form-control">
                                <a href="#" class="borrarInput"><samp class="la la-remove"></samp></a>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        @endif