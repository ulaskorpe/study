<div class="col-xs-12">
    <div class="card">
        <div class="card-header">


            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title"><i class="icon-coin-dollar"></i> {{__('expenses.expense_details')}}</h4>
                    <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>




                </div>
                <div class="col-md-4">
                    @if(!empty($expense->updated_by))
                        ( Last Updated By: {{$expense->lastUpdated->fullname()}} )
                    @endif
                </div>
            </div>

        </div>
        <div class="card-body collapse in">
            <div class="card-block">



                <div class="form-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.amount')}}</label>
                                <br>{{$expense->amount}}

                            </div>
                        </div>
                    </div>
                    <div class="row">


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.driver_company')}}</label>

                                <br>{{$expense->driverCompany->name}}



                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.driver')}} </label>
                                <br>{{$expense->driver->user->fullname()}}


                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_type')}}</label>
                                <br>{{$expense->type->type_name}}


                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_title')}}</label>
                                <br>{{$expense->expense_title}}


                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_vehicle')}}</label>
                                @if($expense->vehicle_id>0)
                                <br>{{$expense->vehicle->vehiclemodel->vehiclebrand->name}}
                                ,{{$expense->vehicle->vehiclemodel->name}}
                                ,{{$expense->vehicle->plate}}
                                    @endif


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_description')}}</label>
                                <br>{{$expense->expense_description}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.expense_document')}}</label>


                                @if(!empty($expense->expense_document))

                                    @php
                                        $dz=explode("/",$expense->expense_document);
                                    $filename = $dz[count($dz)-1];

                                    @endphp

                                    <a href="{{makePrivateFileUrl($expense->expense_document)}}" target="_blank">{{$filename}}</a>
                                @endif


                            </div>
                        </div>

                        @php

                            $date=explode(' ',$expense->date);

                        @endphp
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expense_date">{{__('expenses.expense_date')}}</label>
                                <br>{{$date[0]}}
                            </div>
                        </div>
                    </div>

                    <div class="row" id="tank_card_div" @if(empty($tank_cards)) style="display: none" @endif>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('expenses.tank_card')}}</label>
                                @if($expense->tankcard)
                                    <br>{{$expense->tankcard->company->company_name}} - {{$expense->tankcard->card_no}}
                                @endif

                            </div>
                        </div>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@yield('scripts')