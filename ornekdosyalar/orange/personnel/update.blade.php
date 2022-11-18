<link href="{{asset('/robust-assets/css/plugins/extensions/toggle.css')}}" rel="stylesheet">
<script src="{{asset('/robust-assets/js/plugins/extensions/toggle.js')}}"></script>

<div class="col-xs-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">

                @if(empty($user_info->photo))
                    <i class="icon-head"></i>
                @else
                    <div class="media-left"><span
                                class="avatar avatar-sm avatar-online rounded-circle"><img
                                    src=" {{makePrivateFileUrl($user_info->photo,150,150,1)}}"
                                    alt="avatar"></span></div>
                @endif
                {{__('personnel.update_personnel')}}</h4>
            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
        </div>



        <div class="card-body collapse in">
            <div class="card-block">

                <form class="form" id="update_personnel" action="#"
                      method="post" enctype="multipart/form-data">
                    {{csrf_field()}}


                    <h4 class="form-section"><i class="icon-person-add"></i>{{__('personnel.personnel_profile_info')}}
                    </h4>
                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="first_name">{{__('drivers_companies.title')}}</label>
                                <input type="text" id="title" class="form-control"
                                       placeholder="{{__('drivers_companies.title')}}"
                                       name="title" value="{{ old('title',$user_info->title) }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="first_name">{{__('drivers_companies.first_name')}}</label>
                                <input type="text" id="first_name" class="form-control"
                                       placeholder="{{__('drivers_companies.first_name')}}" required
                                       name="first_name" value="{{ old('first_name',$user_info->user->name) }}">
                            </div>
                        </div>



                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="last_name">{{__('drivers_companies.last_name')}}</label>
                                <input type="text" id="last_name" class="form-control"
                                       placeholder="{{__('drivers_companies.last_name')}}" name="last_name" required
                                       value="{{ old('last_name',$user_info->user->last_name) }}"/>
                            </div>
                        </div>


                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="gender">{{__('drivers_companies.gender')}}</label>
                                @include("components.toggle",["id"=>"gender","name"=>"gender","dataon"=>__('department.male'),"dataoff"=>__('department.female'),"value"=>old('gender',$user_info->user->gender),"data_set"=>[
                                        "true"=>__('department.male'),"false"=>__('department.female')
                                        ]])

                            </div>
                        </div>



                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="birthofdate">{{__('drivers_companies.birth_of_date')}}</label>

                                @include("components.date",["id"=>"birthday","name"=>"birthday","value"=>$user_info->user->birth_date ])
                            </div>
                        </div>


                        <div class="col-md-2">
                            <label for="password">{{__('drivers_companies.password')}}</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="password" class="form-control" id="password"  />
                                    <span class="input-group-btn">
                                        <a href="javascript:addPassword();" class="btn btn-warning" type="button"
                                           style="padding-left: 2px;padding-right: 2px;">{{__('drivers_companies.generate')}}</a>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="first_name">{{__('department.svnr_number')}}</label>
                                <input type="text" id="svnr" class="form-control"
                                       placeholder="{{__('department.svnr_number')}}"
                                       name="svnr" value="{{ old('svnr',$user_info->svnr) }}">
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">

                                <label for="last_name">{{__('department.residense_permit')}}</label><br>


                                @include("components.toggle",["id"=>"residense_permit","name"=>"residense_permit","dataon"=>__('department.citizen'),"dataoff"=>__('department.foreigner'),
                                "value"=>old('residense_permit',$user_info->residense_permit),"data_set"=>[
                               // "true"=>strtolower(__('department.citizen')),"false"=>strtolower(__('department.foreigner'))
                               "true"=>'citizen',"false"=>'foreigner'
                                ]])


                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="residense_permit_date">{{__('department.residense_permit_date')}}</label>
                                @include("components.date",["id"=>"residense_permit_ends","name"=>"residense_permit_ends","view"=>"true","value"=>$user_info->residense_permit_ends])
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">

                                <label for="last_name">{{__('department.work_place')}}</label><br>

                                @include("components.toggle",["id"=>"work_place","name"=>"work_place","dataon"=>__('department.office'),"dataoff"=>__('department.field'),
                                "value"=>old('work_place',$user_info->work_place),"data_set"=>[
                                "true"=>strtolower(__('department.office')),"false"=>strtolower(__('department.field'))
                                ]])


                            </div>
                        </div>
                        <?php


                        $lang=(!empty(old('lang_dizi')))? old('lang_dizi'): $user_info->languages;
                        $langDizi = explode(',', $user_info->languages);


                        ?>
                        <div class="col-md-4">

                            <div class="form-group">
                                <label for="last_name">{{__('department.languages')}}




                                </label><br>
                                <select id="languages[]" name="languages[]" multiple="multiple" class="select2">
                                    @foreach($languages as $language)
                                        <option value="{{$language}}" @if(in_array($language,$langDizi)) selected @endif>{{$language}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>


                    </div>

                   <div class="row">

                        <div class="col-md-4">
                            <label for="address">{{__('personnel.departments')}}   </label>
                            <div class="form-group">

                                <select name="department_id" id="department_id" required class="select2">

                                    @foreach($departments as $department)
                                        <option value="{{$department->id}}" @if($user_info->department_id==$department->id) selected @endif>{{$department->department_name}}</option>

                                    @endforeach

                                </select>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="role_id">{{__('personnel.roles')}} </label>
                            <select id="role_id" name="role_id" class="form-control" onchange="getPermissions()">
                                <option value="0">{{__('personnel.select')}} </option>
                                @foreach($roles as $role)
                                    <option @if($role_id==$role->id) selected @endif value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="address">{{__('department.comments')}}   </label>
                            <div class="form-group">
                                <textarea name="comments" id="comments"  class="form-control">{{$user_info->comments}}</textarea>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <label for="address">{{__('department.photo')}}</label>
                            <div class="form-group">
                                <input name="photo_file" id="photo_file" type="file" class="form-control"
                                       value=""/>

                            </div>
                        </div>


                    </div>


                    <h4 class="form-section"><i class="icon-ios-telephone"></i>{{__('department.contact_information')}}</h4>

                    <div class="row">
                        <div class="col-md-5">
                            <label for="address">{{__('drivers_companies.address')}}</label>
                            <div class="form-group">
                                @if($user_info->address_id>0)

                                    @include("components.select2_address",["id"=>"address_id_update","name"=>"address_id_update",
                                                                 "autocomplete_url"=>"/common/autocomplate/select2","modelname"=>\App\Models\Location::class,"functionname"=>'Address'
                                                                 ,"value"=>['id'=>$user_info->address_id,'text'=>$user_info->address->address,'hint'=>$user_info->address->address_note]
                                                                   ])


                                @else
                                    @include("components.select2_address",["id"=>"address_id_update","name"=>"address_id_update",
                       "autocomplete_url"=>"/common/autocomplate/select2","modelname"=>\App\Models\Location::class,"functionname"=>'Address'])
                                @endif

                            </div>
                        </div>

                        <div class="col-md-3">
                            <label>{{__('drivers_companies.telephone')}}</label>
                            <div class="form-group">
                                @include("components.phone_number",["id"=>"gsm_phone","name"=>"gsm_phone","class"=>"form-control input-lg","required"=>false,"obj"=>"tel_1"
                                ,"value"=>$user_info->user->gsm_phone
                                ])
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="email">{{__('drivers_companies.email')}}</label>
                                <input type="email" id="email" class="form-control" placeholder="{{__('drivers_companies.email')}}"
                                       name="email" value="{{ old('email',$user_info->user->email) }}" required/>
                            </div>
                        </div>
                        <input type="hidden" name="residential_id" id="residential_id" value="{{$user_info->residential_id}}">
                        <input type="hidden" name="nationality_id" id="nationality_id" value="{{$user_info->nationality_id}}">

                    </div>
                    <div class="form-actions">


                        <button class="btn btn-danger pull-right" type="button" data-dismiss="modal" style="margin-left: 15px;">
                            <i class="icon-cross"></i> {{__('vehicles.cancel')}}
                        </button>

                        <button class="btn btn-green pull-right" type="submit" id="saveBtn">
                            <i class="icon-check"></i> {{__('personnel.update_department')}}
                        </button>


                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>


    $('#address_id_update').on('change',function(){
        $.get('/find_country_code/'+$('#address_id').val(),function(data){
            $('#nationality_id').val(data['country_code']).trigger('change');
            $('#residential_id').val(data['country_code']).trigger('change');
            tel_1.intlTelInput("setCountry",data['country_code']);

        }).fail({!! config("view.ajax_error") !!});
    });
    $(document).ready(function () {




    });


    $('#update_personnel').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        save(formData);
    });



    function save(formData) {
        $("#loading").show();
        $.ajax({
            type: 'POST',
            url: '/manager/personnel/update/{{$user_info->id}}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                swal("{{__('personnel.personnel_is_updated')}}", "{{__('personnel.personnel_is_updated')}}", "success");
                location.reload();
            },
            error: {!!config("view.ajax_error")!!}
        });
        $("#loading").hide();

    }



    function generatePassword() {
        var length = 6,
            charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
            retVal = "";
        for (var i = 0, n = charset.length; i < length; ++i) {
            retVal += charset.charAt(Math.floor(Math.random() * n));
        }
        return retVal
    }

    function addPassword() {
        var val = generatePassword();
        $("#password").val(val);
    }




</script>
@yield('scripts')