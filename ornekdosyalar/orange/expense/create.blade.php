<div class="col-xs-12">
    <div class="row match-height" style="padding-top: 10px"> </div>
    <div class="card-header">
        <h4 class="card-title"><i class="icon-coin-euro"></i> {{__('expenses.create_expense')}}</h4>
        <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
    </div>
    <div class="card-body collapse in">
        <div class="card-block">


            <form class="form" id="create-expense" action="{{route('driver_company.expenses.create')}}"
                  method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.amount')}}</label>
                                @include("components.money",[
                               "id"=>"amount",
                               "name"=>"amount",
                               "value"=>"0"
                               ])

                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.driver_company')}}</label><br>
                                @if(isset($driver_company))
                                <input type="hidden" id="expense_driver_company_id" name="expense_driver_company_id" value="{{$driver_company->id}}">
                                <b>{{$driver_company->name}}</b>
                                @else
                                    <input type="hidden" id="expense_driver_company_id" name="expense_driver_company_id" value="0">
                                @endif

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.driver')}}</label><br>
                                <input type="hidden" id="expense_driver_id" name="expense_driver_id" value="{{$driver->id}}">
                                <b>{{$driver->user->fullname()}}</b>


                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_type')}}</label>

                                <select name="expense_type" id="expense_type" class="form-control" required onchange="typeControl(this.value)">
                                    <option value="">{{__('expenses.select_type')}}</option>
                                    @foreach($types as $type)
                                        <option value="{{$type->id}}"
                                                @if(old('expense_type')==$type->id)selected @endif>     {{$type->type_name}}  </option>

                                    @endforeach


                                </select>


                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_title')}}</label>
                                <input class="form-control" required type="text" name="expense_title"
                                       value="{{old('expense_title')}}" id="expense_title"
                                       placeholder="{{__('expenses.expense_title')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_vehicle')}}</label>

                                <select name="expense_vehicle_id" id="expense_vehicle_id" class="vehicle_id" required>
                                    <option value=""></option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{$vehicle->id}}">
                                            {{$vehicle->plate }}
                                            ,
                                            {{ $vehicle->vehiclemodel->vehiclebrand->name }}
                                            {{ $vehicle->vehiclemodel->name }} -
                                            {{ $vehicle->vehiclemodel->vehicleclass->name }}
                                        </option>
                                        @endforeach
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_description')}}</label>
                                <input class="form-control" type="text" name="expense_description"
                                       id="expense_description" value="{{old('expense_description')}}"
                                       placeholder="{{__('expenses.expense_description')}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_document')}}</label>
                                <input class="form-control" type="file" name="expense_document"
                                       value="{{old('expense_document')}}"
                                       id="expense_document" placeholder="{{__('expenses.expense_document')}}">
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_date')}}</label>

                                @include("components.date",["id"=>"expense_date","name"=>"expense_date","value"=>""])
                            </div>
                        </div>
                    </div>


                    <div class="row" id="tank_card_div" style="display: none">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.tank_card')}}</label>
                                <select class="form-control" name="tank_card_id" id="tank_card_id"></select>
                            </div>
                        </div>
                    </div>




                    <div class="form-actions" style="height: 200px;">


                        <button class="btn btn-danger pull-right" type="button" data-dismiss="modal" style="margin-left: 15px;">
                            <i class="icon-cross"></i> {{__('common.cancel')}}
                        </button>

                        <button class="btn btn-green pull-right" type="submit">
                            <i class="icon-check"></i>  {{__('expenses.create_expense')}}
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#amount").inputmask();
    $(document).ready(function() {

        $("#expense_date").inputmask();
    });
    function typeControl(type){
        if ((type == 1) && ($('#expense_driver_id').val()>0)) {
            $('#tank_card_div').show(500);

            $.get('expenses/get_tank_cards/' + $('#expense_driver_id').val(), function (data) {
                ///    console.log(data);
                var html = "<option value=''> {{__('expenses.select_tank_card')}} </option>";

                for (var i = 0; i < data.length; i++) {

                    html += '<option value="' + data[i].id + '">'+ data[i]['company']['company_name'] +'-'+ data[i].card_no +
                        '</option>';
                }

                $('#tank_card_id').html(html);

            }).fail({!! config("view.ajax_error") !!});

        }else{
            $('#tank_card_div').hide(500);
            $('#tank_card_id').html();
        }
    }////typecontrol

    function getCompanyDrivers(company_id){

        $.get('{{asset('/driver_company/expenses/get_company_drivers/')}}'+'/' + $('#expense_driver_company_id').val(), function (data) {

            var html = "<option value=''> {{__('expenses.select_driver')}} </option>";

            for (var i = 0; i < data.length; i++) {

                html += '<option value="' + data[i].id + '">'+ data[i]['user']['name']+' '+data[i]['user']['last_name'] +
                    '</option>';
            }

            $('#expense_driver_id').html(html);

        }).fail({!! config("view.ajax_error") !!});


        /* $vehicle->vehiclemodel->vehiclebrand->name
                $vehicle->vehiclemodel->name  -
                 $vehicle->vehiclemodel->vehicleclass->name */

        $.get('{{asset('/driver_company/expenses/get_company_vehicles/')}}'+'/' + $('#expense_driver_company_id').val(), function (data) {

            var html = "<option value=''> {{__('expenses.select_vehicle')}} </option>";

            for (var i = 0; i < data.length; i++) {

                html += '<option value="' + data[i].id + '">'+data[i]['plate']+' '+data[i]['vehiclemodel']['vehiclebrand']['name']+' '+data[i]['vehiclemodel']['name'] +' '+
                    data[i]['vehiclemodel']['vehicleclass']['name']  +'</option>';
            }

            $('#expense_vehicle_id').html(html);

        }).fail({!! config("view.ajax_error") !!});

    }


    $("#expense_vehicle_id").select2({ });
    $("#tank_card_id").select2({ });


    $('#create-expense').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        save(formData);
    });

    function save(formData) {
        $("#loading").show();
        $.ajax({
            type: 'POST',
            url: '/driver/expenses/create',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                swal("{{__('expenses.expense_is_created')}}", "{{__('expenses.expense_is_created')}}", "success");

                location.reload();
            },
            error: {!!config("view.ajax_error")!!}
        });
        $("#loading").hide();

    }


</script>
@yield('scripts')