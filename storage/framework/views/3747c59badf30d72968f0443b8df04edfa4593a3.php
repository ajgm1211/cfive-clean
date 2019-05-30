<div class="m-portlet__body">
    <div class="row">
        <?php if( Auth::user()->type == 'admin' || Auth::user()->type == 'company'): ?>
        <div class="form-group m-form__group">
            <div class="col-md-12">
                <label>User</label>
                <?php echo e(Form::select('user', $users, null,['class'=>'custom-select form-control','placeholder'=>'Select an user'])); ?>

            </div>
        </div>
        <?php endif; ?>
        <div class="form-group m-form__group">
            <div class="col-md-12">
                <label>Date</label>
                <div class="input-group date">
                    <?php echo Form::text('pick_up_date', null, ['placeholder' => 'Select date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1']); ?>

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
                    <button type="submit" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill button">
                        Filter
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group">
            <div class="col-md-1">
                <br>
                <div class="input-group date">
                    <a href="<?php echo e(route('dashboard.index')); ?>" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill button">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>