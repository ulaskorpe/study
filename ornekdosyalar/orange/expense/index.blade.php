@php
    /** * OrangeTech Soft Taxi & Mietwagen Verwaltungssystem
     *
     * @package   Premium
     * @author    OrangeTech Soft <support@orangetechsoft.at>
     * @link      http://www.orangetechsoft.at/
     * @copyright 2017 OrangeTech Soft
     */

    /**
     * @package OrangeTech Soft Taxi & Mietwagen Verwaltungssystem
     * @author  OrangeTech Soft <support@orangetechsoft.at>
     */
@endphp


@extends('themes.robust.layouts.default')

@section('pageTitle', trans('pageTitle.opRecover'))
@section('metaDescription', '...')
@section('metaKeywords', '...')

@section('cssParts')

@stop

@section('content-body')


    <section id="basic-form-layouts">
        <div  style="padding-top: 10px;">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-head">
                        <div class="card-header">
                            <h4 class="card-title">{{__('expenses.expense_list')}}</h4>
                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <a href="javascript:createExpense()">
                                    <button type="button" class="btn btn-green btn-sm"><i
                                                class="icon-money white"></i>
                                        {{__('expenses.create_expense')}}
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body collapse in">
                        <div class="card-block">
                            @include( "components.datatable",
                                                   [   "id"=>"expenses"
                                                   ,"name"=>"expenses"
                                                   ,"datatable_url"=>"/common/datatable/main"
                                                   ,"modelname"=>\App\Models\Expense::class
                                                   ,"tablename"=>"expenses"
                                                    ,"params_json"=>null
                                                   ,"functionname"=>"ExpensesForDriver"
                                                   ,"buttons"=>"components.buttons_expense"
                                                           ,"columns"=>[
                                                                   "actions"=>           __("expenses.table_expense_actions")
                                                                 ,"amount"=>      __("expenses.table_expense_amount")
                                                                 ,"expense_title"=>     __("expenses.table_expense_description")
                                                                 ,"type_name"=>         __("expenses.table_expense_type")
                                                                 ,"companyname"=>       __("expenses.table_expense_belongs_to")
                                                                 ,"plate"=>             __("expenses.table_expense_vehicle_plate")
                                                                 ,"date"=>              __("expenses.table_expense_date")
                                                                 ,"expense_document"=>  __("expenses.table_expense_document")
                                                                 ,"last_updatedby"=>    __("expenses.table_expense_created_by")

                                                                 ]
                                                   ])



                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" >
                <div class="card" style="min-height:790px !important;">
                    <div class="card-head">
                        <div class="card-header">
                            <h4 class="card-title"><i class="icon-eye"></i>{{__('expenses.expense_details')}}</h4>
                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="detail" class="card-body collapse in">


                    </div>
                </div>
            </div>
        </div>
    </section>



    <div class="modal fade modal-lg" id="saveModal" role="dialog" style="background-color: #fffffc;margin: auto;height: 900px">
        <div id="loading" class="folding-cube loader-blue-grey" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0;  margin: auto;">
            <div class="cube1 cube"></div>
            <div class="cube2 cube"></div>
            <div class="cube4 cube"></div>
            <div class="cube3 cube"></div>
        </div>
        <div class="modal-header bg-info">
            <a class="close" data-dismiss="modal" style="color: #fffffc">×</a>
            <!--//TODO: Translate docs must be edited...-->
            <h4 style="color: #fffffc" id="modal_title">{{__('expenses.create_update_expense')}}</h4>
        </div>
        <div id="modal-body" class="modal-body">

        </div>
    </div>
    @include("components.delete",["post_url"=>'/driver/expenses/delete',"id"=>"expense_id"])
@stop

@section('scriptParts')

    <!-- BEGIN PAGE LEVEL JS-->
    <script type="text/javascript">


        function createExpense() {
            $("#loading").show();
            $("#modal_title").html('{{__('expenses.create_expense')}}');
            $.get('/driver/expenses/create', function (mdata) {
                $("#loading").hide();
                $("#modal-body").html(mdata);
            });
            $('#saveModal').modal();
        }




        function createFile(userId) {
            $("#loading").show();
            $.get('/driver/department/files/create/'+userId, function (mdata) {
                $("#loading").hide();
                $("#modal-body").html(mdata);
            });
            $('#saveModal').modal();
        }


        function downloadElement(fileId){
            $.get('/driver/department/files/downloadfile/'+fileId,function (data){
                //window.open(data,'_blank');//  alert(data);

                var linkx = '{{route('pfiles')}}/?u='+data;
                window.open(linkx,'_self');

            });
        }

        function downloadElement(fileId){
            $.get('/driver/expenses/downloadfile/'+fileId,function (data){
                var downloadlink = '{{route('pfiles')}}/?u='+data;
                downloadlink = downloadlink.toString();
                window.open(downloadlink,'_self');
            });
        }

        function loadElement(expenseId) {

            $.get('/driver/expenses/profile/'+expenseId, function (mdata) {
                //   $("#loading").hide();
                $("#detail").html(mdata);
            }).fail({!! config("view.ajax_error") !!});

        }

        function updateElement(expenseId) {
            $("#loading").show();
            $("#modal_title").html('{{__('expenses.update_expense')}}');
            $.get('/driver/expenses/edit/'.concat(expenseId), function (mdata) {
                $("#loading").hide();
                $("#modal-body").html(mdata);
                $(".select2").select2();
            });
            $('#saveModal').modal();
        }


    </script>
    <!-- END PAGE LEVEL JS-->
@stop