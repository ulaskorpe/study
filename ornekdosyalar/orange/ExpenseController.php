<?php
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

namespace App\Http\Controllers\Manager\Expense;

use App\Models\RoleUser;
use App\Models\Driver;
use App\Models\DriverCompany;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\TankCard;
use App\Models\Vehicle;
use App\Models\CalculateItem;
use App\Models\Calculate;
use App\User;
use Carbon\Carbon;
use PhpParser\ErrorHandler\Collecting;
use Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use App\Helpers\DepartmentHelper;

class ExpenseController extends Controller
{

    public function index()
    {
        return view('themes.' . static::$tf . '.manager.expense.index');
    }


private  $izinliDizi = array('doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt', 'png','jpg','jpeg','gif');
    public function create(Request $request)
    {

        if ($request->isMethod('post')) {

            $messages = [

//'expense_date.before'=>'sdfasdfsdf'
            ];
            $rules = [
                'amount' => 'required',
                'expense_type' => 'required',
                'expense_title' => 'required',
                'expense_date' => 'required|before:tomorrow',
                'expense_document' => 'mimes:doc,docx,xls,xlsx,pdf,txt,png,jpg,jpeg'
            ];
            $this->validate($request, $rules, $messages);


            \DB::transaction(function () use ($request) {

                $NewExpense = new Expense();
                $NewExpense->creator_id = \Auth::id();
                $NewExpense->amount = $request->amount;
                $NewExpense->driver_id = (!empty($request->expense_driver_id)) ? $request->expense_driver_id : 0;
                $NewExpense->driver_company_id = $request->expense_driver_company_id;//(!empty($request->driver_company_id))?$request->driver_company_id:0;;
                $NewExpense->vehicle_id = $request->expense_vehicle_id;
                $NewExpense->expense_title = $request->expense_title;
                $NewExpense->expense_description = $request->expense_description;
                //$NewExpense->expense_document = $request->expense_document;
                $NewExpense->expense_type_id = $request->expense_type;
                $NewExpense->tank_card_id = ($request->expense_type == 1) ? (($request->tank_card_id) ? $request->tank_card_id : 0) : 0;

                if ($request->expense_type == 1 && (!empty($request->tank_card_id))) {
                    $NewExpense->tank_card_id = $request->tank_card_id;
                } else {
                    $NewExpense->tank_card_id = 0;
                }


                $NewExpense->date = Carbon::parse($request->expense_date)->toDateTimeString();
                $dosya = $request->file('expense_document');
                $dh= new DepartmentHelper\DepartmentHelper();


                if (!empty($dosya)) {

                    $path = storage_path("user_files/expenses");
                    $filename =$dh->fixetiket($request->expense_title). rand(1000, 99999).$dosya->getClientOriginalName();
                    $dosya->move($path, $filename);
                    $filename = "user_files/expenses/". $filename;
                    $NewExpense->expense_document = $filename;

                }




                $NewExpense->save();

            });

            return 'ok';
        }////post


        /*$roles = Role::with('users')->whereIn('name', ['driver', 'driver_company'])->get();
        $users = new Collection();
        foreach ($roles as $role) {
            $users = $users->merge($role->users);
        }*/

        $drivers = Driver::with('user')->get();
        $driverCompanies = DriverCompany::with('user')->get();
        $data = [

            'drivers' => $drivers,
            'driverCompanies' => $driverCompanies,
            'types' => ExpenseType::all()
            // 'vehicles' => Vehicle::with('company', 'vehiclemodel.vehiclebrand', 'vehiclemodel.vehicleclass')->get()
        ];

        return view('themes.' . static::$tf . '.manager.expense.create', $data);
    }

    public function edit(Request $request, $id)
    {

        $expense = Expense::where('id', '=', $id)->with('driver.user')->first();
        $oldDocument = $expense->expense_document;
        if ($request->isMethod('post')) {


            $rules = [
                'amount' => 'required|numeric',
                'expense_type' => 'required',
                'expense_title' => 'required',
                'expense_date' => 'required',
               // 'expense_document' => 'mimes:doc,docx,xls,xlsx,pdf,txt,png,jpg,jpeg'
            ];
            $this->validate($request, $rules);

            \DB::transaction(function () use ($request, $expense, $oldDocument) {

                $expense->driver_id = (!empty($request->expense_driver_id)) ? $request->expense_driver_id : 0;
                $expense->driver_company_id = $request->expense_driver_company_id;
                $expense->vehicle_id = $request->expense_vehicle_id;
                $expense->amount = $request->amount;
                $expense->expense_title = $request->expense_title;
                $expense->expense_description = $request->expense_description;
                $expense->expense_type_id = $request->expense_type;
                if ($request->expense_type == 1 && (!empty($request->tank_card_id))) {
                    $expense->tank_card_id = $request->tank_card_id;
                    /*  if($expense->amount != $request->amount){
                       if($expense->tank_card_id != $request->tank_card_id){//card diff && amount diff
                              $oldCard=TankCard::find($expense->tank_card_id);
                              $oldCard->amount=$oldCard->amount+$expense->amount;
                              $oldCard->save();

                              $tankCard=TankCard::find($request->tank_card_id);
                              $tankCard->amount = $tankCard->amount  - $request->amount ;
                              $tankCard->save();

                          }else{///amountdiff
                           $tankCard=TankCard::find($request->tank_card_id);
                           $tankCard->amount = ($tankCard->amount + $expense->amount) - $request->amount ;
                           $tankCard->save();

                          }
                   }///!=amount diff*/

                } else {///type!=1
                    /* if(!empty($expense->tank_card_id)){
                         $oldCard=TankCard::find($expense->tank_card_id);
                         $oldCard->amount=$oldCard->amount+$expense->amount;
                         $oldCard->save();

                     }*/

                    $expense->tank_card_id = 0;
                }

                $expense->date = $request->expense_date;

                $dosya = $request->file('expense_document');
                $dh= new DepartmentHelper\DepartmentHelper();
                if (!empty($dosya)) {
                    $path = storage_path("user_files/expenses");
                    $filename =$dh->fixetiket($request->expense_title). rand(1000, 99999).$dosya->getClientOriginalName();
                    $dosya->move($path, $filename);
                    $filename = "user_files/expenses/". $filename;
                    $expense->expense_document = $filename;
                }
                /////
                $expense->save();
            });
            return 'ok';
        }////post


        $roles = Role::with('users')->whereIn('name', ['driver', 'driver_company'])->get();
        $users = new Collection();
        foreach ($roles as $role) {
            $users = $users->merge($role->users);
        }

        if ($expense->expense_type_id == 1) {
            $tank_cards = TankCard::where('driver_id', '=', $expense->driver_id)->with('company')->get();
        } else {
            $tank_cards = null;
        }


        $drivers = Driver::with('user')->where('driver_company_id', '=', $expense->driver_company_id)->get();

        $driverCompanies = DriverCompany::with('user')->get();
        if(!empty($expense->expense_document)){
            $dz=explode("/",$expense->expense_document);
            $filename=$dz[count($dz)-1];
        }else{
            $filename="";
        }
        $data = [
            'tank_cards' => $tank_cards,
            'drivers' => $drivers,
            'driverCompanies' => $driverCompanies,
            'types' => ExpenseType::all(),
            'vehicles' => Vehicle::where('company_id', '=', $expense->driver_company_id)->with('company', 'vehiclemodel.vehiclebrand', 'vehiclemodel.vehicleclass')->get(),
            'expense' => $expense,
            'filename'=>$filename

        ];

        return view('themes.' . static::$tf . '.manager.expense.update', $data);
    }
    public function profile($id)
    {

        $expense = Expense::where('id', '=', $id)->with('driver.user')->first();
        $oldDocument = $expense->expense_document;
        $roles = Role::with('users')->whereIn('name', ['driver', 'driver_company'])->get();
        $users = new Collection();
        foreach ($roles as $role) {
            $users = $users->merge($role->users);
        }

        if ($expense->expense_type_id == 1) {
            $tank_cards = TankCard::where('driver_id', '=', $expense->driver_id)->with('company')->get();
        } else {
            $tank_cards = null;
        }


        $drivers = Driver::with('user')->where('driver_company_id', '=', $expense->driver_company_id)->get();

        $driverCompanies = DriverCompany::with('user')->get();
        if(!empty($expense->expense_document)){
            $dz=explode("/",$expense->expense_document);
            $filename=$dz[count($dz)-1];
        }else{
            $filename="";
        }
        $data = [
            'tank_cards' => $tank_cards,
            'drivers' => $drivers,
            'driverCompanies' => $driverCompanies,
            'types' => ExpenseType::all(),
            'vehicles' => Vehicle::where('company_id', '=', $expense->driver_company_id)->with('company', 'vehiclemodel.vehiclebrand', 'vehiclemodel.vehicleclass')->get(),
            'expense' => $expense,
            'filename'=>$filename

        ];

        return view('themes.' . static::$tf . '.manager.expense.profile', $data);
    }

    public function delete(Request $request)
    {
        $expense_id = $request["expense_id"];
        $id = \Illuminate\Support\Facades\Auth::id();
        $password = $request["password"];
        if ($password != "") {
            if (\Illuminate\Support\Facades\Auth::attempt(['id' => $id, 'password' => $password, 'status' => 1, 'banned_until' => null], true)) {
                $Expense = Expense::find($expense_id);
                $is_calculated = $Expense->is_calculated;
                Expense::find($expense_id)->delete();
                if ($is_calculated > 0) {
                    $calculate_item=CalculateItem::where("expense_id","=",$expense_id)->first();
                    $calculate=Calculate::find($calculate_item->calculate_id);
                    $calculate->total_expense=$calculate->total_expense-$Expense->amount;
                    $calculate->result=$calculate->result+$Expense->amount;
                    $calculate->save();
                    $calculate_item->delete();
                }
                return response("ok", 200);
            } else {

                return response("Please enter a valid password", 422);
            }
        } else {
            return response("Please enter password", 422);
        }

    }

    function downloadfile($fileId=0){
        //     $privateFileHelper = new PrivateFileHelper();
        $file=Expense::find($fileId);
        return  $file->expense_document;//$privateFileHelper->makeUrl($file->file);


    }

//////////////////////////ajax fonksiyonu//////////////////////////////////////////////
    public function getTankCards($driver_id)
    {

       return TankCard::where('driver_id', '=', $driver_id)->with('company')->get();

    }
    public function getCompanyDrivers($company_id = 0)
    {


        if (!empty($company_id)) {
            return Driver::where('driver_company_id', '=', $company_id)->with('user')->get();
        } else {
            return null;//Driver::with('user')->get();
        }

    }
    public function getCompanyVehicles($company_id = 0)
    {


        if (!empty($company_id)) {
            return Vehicle::where('company_id', '=', $company_id)->with('company', 'vehiclemodel.vehiclebrand', 'vehiclemodel.vehicleclass')->get();
        } else {
            return null;// Driver::with('user')->get();
        }

    }
//////////////////////////ajax fonksiyonu//////////////////////////////////////////////
}
