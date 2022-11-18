<?php

namespace App\Http\Controllers\Manager\Expense;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExpenseTypeController extends Controller
{
    public function index(){
        $data['types'] = ExpenseType::all();

        return view('themes.' . static::$tf . '.manager.expensetype.index', $data);
    }

    public function create(Request $request){

        if ($request->isMethod('post')) {
           $rules = [
                'type_name' => 'required|string|max:255',
            ];
            $this->validate($request, $rules);

            $NewType = new ExpenseType();
            $NewType->type_name = $request->type_name;
            $NewType->save();

            return 'ok';
        }
        return view('themes.' . static::$tf . '.manager.expensetype.create');
    }

    public function edit($expense_type_id  =  0, Request $request){

        $expenseType = ExpenseType::with('lastUpdated')->find($expense_type_id);
        if ($request->isMethod('post')) {
            $rules = [
                'type_name' => 'required|string|max:255',
            ];
            $this->validate($request, $rules);

         //   $NewType = new ExpenseType();
            $expenseType->type_name = $request->type_name;
            $expenseType->save();

            return 'ok';
        }
        return view('themes.' . static::$tf . '.manager.expensetype.update',['model'=>$expenseType]);
    }

   /* public function delete($expense_type_id){
        //VehicleModel::find($id)->delete();

       ExpenseType::where('id','=',$expense_type_id)->where('id','>',4)->delete();
        Expense::where('expense_type_id','=',$expense_type_id)
            ->update(['expense_type_id'=>3]);

        /*Expense::where('expense_type_id','=',$expense_type_id)->update(
          ['expense_type_id'=>0]
        );

        return 'ok';
    }*/

    public function delete(Request $request)
    {
        $expense_type_id = $request["expense_type_id"];
        $id = \Illuminate\Support\Facades\Auth::id();
        $password = $request["password"];
        if ($password != "") {
            if (\Illuminate\Support\Facades\Auth::attempt(['id' => $id, 'password' => $password, 'status' => 1, 'banned_until' => null], true)) {


                ExpenseType::where('id','=',$expense_type_id)->where('id','>',4)->delete();
                Expense::where('expense_type_id','=',$expense_type_id)
                    ->update(['expense_type_id'=>3]);

                return response("ok", 200);
            } else {

                return response("Please enter a valid password", 422);
            }
        } else {
            return response("Please enter password", 422);
        }

    }

}
