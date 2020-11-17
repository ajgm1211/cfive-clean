<div class="m-portlet__body">
    <div class="row">
        @if( Auth::user()->type == 'admin' || Auth::user()->type == 'company')
        <div class="form-group m-form__group">
            <div class="col-md-12">
                <label>User</label>
                {{ Form::select('user', $users, @$user->id,['class'=>'custom-select form-control','id'=>'user']) }}
            </div>
        </div>
        @endif
        <div class="form-group m-form__group">
            <div class="col-md-12">
                <label>Date</label>
                <div class="input-group date">
                    {!! Form::text('pick_up_date', @$pick_up_date, ['placeholder' => 'Select date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1']) !!}
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar-check-o"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group">
            <div class="col-md-1">
                <br>
                <div class="input-group date">
                    <button type="button" id="filter_by_user" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill button">
                        Filter
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group">
            <div class="col-md-1">
                <br>
                <div class="input-group date">
                    <a href="{{route('dashboard.index')}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill button">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>