@extends('layouts.app')
@section('title', 'Usuario')
@section('content')
<div class="page-content">
    <div class="container">
        <!-- BEGIN PAGE BREADCRUMBS -->
        <ul class="page-breadcrumb breadcrumb">
            <li>
                <a href="index.html">Usuarios</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Agregar</span>
            </li>
        </ul>
        <div class="page-content-inner">
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light ">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Default Form</span>
                            </div>
                            <div class="actions">
                                <div class="btn-group">
                                    <a class="btn btn-sm green dropdown-toggle" href="javascript:;" data-toggle="dropdown"> Actions
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li>
                                            <a href="javascript:;">
                                                <i class="fa fa-pencil"></i> Edit </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="fa fa-trash-o"></i> Delete </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="fa fa-ban"></i> Ban </a>
                                        </li>
                                        <li class="divider"> </li>
                                        <li>
                                            <a href="javascript:;"> Make admin </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="Email Address"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Circle Input</label>
                                        <div class="input-group">
                                            <span class="input-group-addon input-circle-left">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                            <input type="text" class="form-control input-circle-right" placeholder="Email Address"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                                            <span class="input-group-addon">
                                                <i class="fa fa-user font-red"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Left Icon</label>
                                        <div class="input-icon">
                                            <i class="fa fa-bell-o font-green"></i>
                                            <input type="text" class="form-control" placeholder="Left icon"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Left Icon(.input-sm)</label>
                                        <div class="input-icon input-icon-sm">
                                            <i class="fa fa-bell-o"></i>
                                            <input type="text" class="form-control input-sm" placeholder="Left icon"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Left Icon(.input-lg)</label>
                                        <div class="input-icon input-icon-lg">
                                            <i class="fa fa-bell-o"></i>
                                            <input type="text" class="form-control input-lg" placeholder="Left icon"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Right Icon</label>
                                        <div class="input-icon right">
                                            <i class="fa fa-microphone fa-spin font-blue"></i>
                                            <input type="text" class="form-control" placeholder="Right icon"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Right Icon(.input-sm)</label>
                                        <div class="input-icon input-icon-sm right">
                                            <i class="fa fa-bell-o"></i>
                                            <input type="text" class="form-control input-sm" placeholder="Left icon"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Right Icon(.input-lg)</label>
                                        <div class="input-icon input-icon-lg right">
                                            <i class="fa fa-bell-o font-green"></i>
                                            <input type="text" class="form-control input-lg" placeholder="Left icon"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Circle Input</label>
                                        <div class="input-icon right">
                                            <i class="fa fa-microphone"></i>
                                            <input type="text" class="form-control input-circle" placeholder="Right icon"> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Input with Icon</label>
                                        <div class="input-group input-icon right">
                                            <span class="input-group-addon">
                                                <i class="fa fa-envelope font-purple"></i>
                                            </span>
                                            <i class="fa fa-exclamation tooltips" data-original-title="Invalid email." data-container="body"></i>
                                            <input id="email" class="input-error form-control" type="text" value=""> </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Input With Spinner</label>
                                        <input class="form-control spinner" type="text" placeholder="Process something" /> </div>
                                    <div class="form-group">
                                        <label>Static Control</label>
                                        <p class="form-control-static"> email@example.com </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Disabled</label>
                                        <input type="text" class="form-control" placeholder="Disabled" disabled> </div>
                                    <div class="form-group">
                                        <label>Readonly</label>
                                        <input type="text" class="form-control" placeholder="Readonly" readonly> </div>
                                    <div class="form-group">
                                        <label>Dropdown</label>
                                        <select class="form-control">
                                            <option>Option 1</option>
                                            <option>Option 2</option>
                                            <option>Option 3</option>
                                            <option>Option 4</option>
                                            <option>Option 5</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Multiple Select</label>
                                        <select multiple class="form-control">
                                            <option>Option 1</option>
                                            <option>Option 2</option>
                                            <option>Option 3</option>
                                            <option>Option 4</option>
                                            <option>Option 5</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Textarea</label>
                                        <textarea class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile1">File input</label>
                                        <input type="file" id="exampleInputFile1">
                                        <p class="help-block"> some help text here. </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Checkboxes</label>
                                        <div class="mt-checkbox-list">
                                            <label class="mt-checkbox"> Checkbox 1
                                                <input type="checkbox" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-checkbox"> Checkbox 2
                                                <input type="checkbox" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-checkbox"> Checkbox 3
                                                <input type="checkbox" value="1" name="test" />
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Outline Checkboxes</label>
                                        <div class="mt-checkbox-list">
                                            <label class="mt-checkbox mt-checkbox-outline"> Checkbox 1
                                                <input type="checkbox" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-checkbox mt-checkbox-outline"> Checkbox 2
                                                <input type="checkbox" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-checkbox mt-checkbox-outline"> Checkbox 3
                                                <input type="checkbox" value="1" name="test" />
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Inline Checkboxes</label>
                                        <div class="mt-checkbox-inline">
                                            <label class="mt-checkbox">
                                                <input type="checkbox" id="inlineCheckbox1" value="option1"> Checkbox 1
                                                <span></span>
                                            </label>
                                            <label class="mt-checkbox">
                                                <input type="checkbox" id="inlineCheckbox2" value="option2"> Checkbox 2
                                                <span></span>
                                            </label>
                                            <label class="mt-checkbox mt-checkbox-disabled">
                                                <input type="checkbox" id="inlineCheckbox3" value="option3" disabled> Disabled
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Radios</label>
                                        <div class="mt-radio-list">
                                            <label class="mt-radio"> Radio 1
                                                <input type="radio" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-radio"> Radio 2
                                                <input type="radio" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-radio"> Radio 3
                                                <input type="radio" value="1" name="test" />
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Outline Radios</label>
                                        <div class="mt-radio-list">
                                            <label class="mt-radio mt-radio-outline"> Radio 1
                                                <input type="radio" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-radio mt-radio-outline"> Radio 2
                                                <input type="radio" value="1" name="test" />
                                                <span></span>
                                            </label>
                                            <label class="mt-radio mt-radio-outline"> Radio 3
                                                <input type="radio" value="1" name="test" />
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Inline Radio</label>
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" name="optionsRadios" id="optionsRadios4" value="option1" checked> Option 1
                                                <span></span>
                                            </label>
                                            <label class="mt-radio">
                                                <input type="radio" name="optionsRadios" id="optionsRadios5" value="option2"> Option 2
                                                <span></span>
                                            </label>
                                            <label class="mt-radio mt-radio-disabled">
                                                <input type="radio" name="optionsRadios" id="optionsRadios6" value="option3" disabled> Disabled
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn blue">Submit</button>
                                    <button type="button" class="btn default">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                 </div>
                
        </div>
    </div>
</div>
@endsection