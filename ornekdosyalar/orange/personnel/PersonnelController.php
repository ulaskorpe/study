<?php

namespace App\Http\Controllers\Manager\Personnel;

use App\Helpers\DepartmentHelper\DepartmentHelper;
use App\Models\Country;
use App\Models\Department;
use App\Models\DriverCompany;
use App\Models\Language;
use App\Models\Log\DriverStateChangeLog;
use App\Models\RoleUser;
use App\Models\UserFile;
use App\Models\UserVacation;
use App\Models\UserWork;
use App\Models\UserInfo;
use App\Role;
use App\User;
use Carbon\Carbon;
use Enum\UserStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CacheHelper\Cache;
use App\Helpers\CacheHelper\Functions\PhoneCodeFunc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class PersonnelController extends Controller
{
    public function index($department_id = 0)
    {

        return view('themes.' . static::$tf . '.manager.personnel.index', [
            'departments' => Department::all(),
            'selected_department' => $department_id
        ]);
    }

    public function departments_list()
    {
        return view('themes.' . static::$tf . '.manager.personnel.department.index');
    }

    public function personnel_files($personnel_id = 0)
    {
        $user_info = UserInfo::select('user_id')->where('id', '=', $personnel_id)->first();
        $user = User::find($user_info->user_id);
        return view('themes.' . static::$tf . '.manager.personnel.files.index', ['user_id' => $user_info->user_id, 'user' => $user, 'params' => json_encode(['user_id' => $user->id])]);
    }

    public function vacations(Request $request,$personnel_id=0){
        $user_info=UserInfo::select('user_id')->where('id','=',$personnel_id)->first();
        $user=User:: find($user_info->user_id);

        $title = $user->fullname() ."  ".  __('department.vacations');
        //      return $role->role_id;
        $ilk = UserVacation::where('user_id', '=', $user->id)->orderBy('start_at', 'asc')->first();
        $son = UserVacation::where('user_id', '=', $user->id)->orderBy('start_at', 'desc')->first();

        $start = (!empty($request->input('start'))) ? $request->input('start') : ($ilk ? $ilk->start_at : date('Y-m-d'));
        $end = (!empty($request->input('end'))) ? $request->input('end') : ($son ? $son->start_at : date('Y-m-d'));
        $all = UserVacation::distinct('start_at')->where('user_id', '=', $user->id)->orderBy('start_at', 'desc')->get();

        if (!empty($request->input('start')) && !empty($request->input('end')) && ($end > $start)) {
            $vacations = UserVacation::where('user_id', '=', $user->id)->whereBetween('start_at', [$start, $end])->orderBy('start_at', 'desc')->get();
        } else {
            $vacations = UserVacation::where('user_id', '=', $user->id)->orderBy('id', 'desc')->get();
        }


        return view('themes.' . static::$tf . '.manager.personnel.vacation.index', array(
            'title' => $title, 'vacations' => $vacations, 'user' => $user, 'start' => $start, 'end' => $end, 'all' => $all));

    }


    public function department_create(Request $request)
    {
        if ($request->isMethod('post')) {

            $rules = [
                'department_name' => 'required'
            ];
            $this->validate($request, $rules);

            \DB::transaction(function () use ($request) {
                $department = new Department();
                $department->department_name = $request->input('department_name');
                $department->save();

            });

        }


        return view('themes.' . static::$tf . '.manager.personnel.department.create');
    }

    public function department_update(Request $request, $id = 0)
    {

        $department = Department::find($id);

        if ($request->isMethod('post')) {

            $rules = [
                'department_name' => 'required'
            ];
            $this->validate($request, $rules);
            \DB::transaction(function () use ($request, $department) {

                $department->department_name = $request->input('department_name');
                $department->save();

            });

        }
        return view('themes.' . static::$tf . '.manager.personnel.department.update', ['department' => $department]);
    }

    public function department_delete(Request $request)
    {


        if ($request->isMethod("POST")) {
            $id = Auth::id();
            $password = $request["password"];
            $department_id = $request["department_id"];
            if ($password != "") {
                if (Auth::attempt(['id' => $id, 'password' => $password, 'status' => 1, 'banned_until' => null], true)) {
                    Department::find($department_id)->delete();

                    return response("ok", 200);
                } else {

                    return response("Please enter a valid password", 422);
                }
            } else {
                return response("Please enter password", 422);
            }
        }

    }


    public function personnel_create(Request $request)
    {


        if ($request->isMethod('post')) {
            $rules = [
                'email' => 'required|string|email|max:255|unique:users',
                'first_name' => 'required|string|min:3',
                'last_name' => 'required|string|min:3',
                //'birthday' => 'required|date',
                //'gender' => 'required',
                //  'gsm_phone' => 'required|string|max:15|unique:users',
                'password' => 'required|string|min:6',
                'photo_file' => 'mimes:png,jpg,jpeg',
                //  'nationality_id'=>'required'
                //  'postal_code' => 'required',
                //    'address'=>'required',
            ];
            if ($request->input('residense_permit') == 'foreigner') {
                $rules = array_merge($rules, ['residense_permit_ends' => 'required']);
            }
            $this->validate($request, $rules);

            DB::transaction(function () use ($request) {
                //after validation Save user

                $NewUser = User::where('email', '=', $request->input('email'))->first();
                if (empty($NewUser->id)) {
                    $NewUser = new User();
                }

                $NewUser->name = $request->input('first_name');
                $NewUser->last_name = $request->input('last_name');
                $NewUser->email = $request->input('email');
                if (empty($NewUser->password)) {
                    $NewUser->password = (!empty($request->input('password'))) ? Hash::make($request->input('password')) : Hash::make('secret');
                }
                $NewUser->gender = $request->input('gender');

                $NewUser->birth_date = $request->input('birthday');

                if (!empty($request->input('old-gsm_phone')) && empty($request->input('gsm_phone'))) {
                    $NewUser->gsm_phone = $request->input('old-gsm_phone');
                } else {
                    $NewUser->gsm_phone = DepartmentHelper::fixnumber($request->input('gsm_phone'));//str_replace_first("-","",trim($request->input('gsm_phone')));
                }
                $NewUser->status = UserStatus::APPROVED;
                //TODO: Roles will be add
                // $NewUser->role_id = 1;
                $NewUser->save();
                $dosya = $request->file('photo_file');
                if (!empty($dosya)) {
                    $path = storage_path("user_files/user_" . $NewUser->id . "/");
                    $filename = "PF_" . date('YmdHis') . $dosya->getClientOriginalName();
                    $dosya->move($path, $filename);
                    $NewUser->profile_image = "user_files/user_" . $NewUser->id . "/" . $filename;
                    $NewUser->save();
                }

                ////////////////////userinfo/////////////////////////////////////////////////
                $languages = "";
                $classes = "";
                if (!empty($request->input('languages'))) {
                    foreach ($request->input('languages') as $language) {
                        $languages .= "," . $language;
                    }
                }
                if (!empty($request->input('licenseclasses'))) {
                    foreach ($request->input('licenseclasses') as $class) {
                        $classes .= "," . $class;
                    }
                }
                $user_info = UserInfo::where('user_id', '=', $NewUser->id)->first();
                if (empty($user_info->id)) {
                    $user_info = new UserInfo();
                    $user_info->user_id = $NewUser->id;
                }
                $user_info->languages = $languages;
                $user_info->classes = $classes;
                $user_info->photo = $NewUser->profile_image;
                $user_info->department_id = $request->input('department_id');
                $user_info->svnr = (!empty($request->input('svnr'))) ? $request->input('svnr') : null;
                $user_info->title = (!empty($request->input('title'))) ? $request->input('title') : "";
                $user_info->nationality = (!empty($request->input('nationality'))) ? $request->input('nationality') : "";
                //$user_info->license_number=(!empty($request->input('license_number')))?$request->input('license_number'):0;
                // $user_info->license_date=(!empty($request->input('license_date')))?$request->input('license_date'):null;


                $residense_permit=(!empty($request->input('residense_permit'))) ? $request->input('residense_permit') : "citizen";
                $user_info->residense_permit = $residense_permit;
                if($residense_permit=='citizen'){
                    $user_info->residense_permit_ends = null;
                }else{
                    $user_info->residense_permit_ends = (!empty($request->input('residense_permit_ends'))) ? $request->input('residense_permit_ends') : null;
                }

                //$user_info->residense_permit = (!empty($request->input('residense_permit'))) ? $request->input('residense_permit') : "citizen";
                //$user_info->residense_permit_ends = (!empty($request->input('residense_permit_ends'))) ? $request->input('residense_permit_ends') : null;
                $user_info->work_place = (!empty($request->input('work_place'))) ? $request->input('work_place') : "office";
                $user_info->address_id = (!empty($request->input('address_id'))) ? $request->input('address_id') : 0;
                $user_info->residential_id = (!empty($request->input('residential_id'))) ? $request->input('residential_id') : null;
                $user_info->comments = (!empty($request->input('comments'))) ? $request->input('comments') : null;
                //$user_info->gps_color=(!empty($request->input('gps_color')))?$request->input('gps_color'):null;

                $user_info->save();

                ////////////////////userinfo/////////////////////////////////////////////////
                if ($request->input('role_id') > 0) {
                    $role = Role::find($request->input('role_id'));
                    $NewUser->attachRole($role);
                }


            });


            return 'ok';


        }///postcountry

        $roles = Role::where("system_user", "=", 0)->select(["name", "description", "id"])->get();

        $departments = Department::all();

        return view('themes.' . static::$tf . '.manager.personnel.create',

            [
                'title' => 'Create Personnel',
                'departments' => $departments,
                'languages' => Language::all(),
                'roles' => $roles
            ]);
    }

    public function personnel_update(Request $request, $personnel_id = 0)
    {
        $user_info = UserInfo::with('user')->where('id', '=', $personnel_id)->first();
        $NewUser = User::find($user_info->user_id);


        if ($request->isMethod('post')) {
            $rules = [
                'email' => 'required|string|email|max:255||unique:users,email,' . $user_info->user_id,
                'first_name' => 'required|string|min:3',
                'last_name' => 'required|string|min:3',
                'photo_file' => 'mimes:png,jpg,jpeg',
            ];
            if ($request->input('residense_permit') == 'foreigner') {
                $rules = array_merge($rules, ['residense_permit_ends' => 'required']);
            }
            $this->validate($request, $rules);

            DB::transaction(function () use ($request, $user_info, $NewUser) {
                //after validation Save user


                $NewUser->name = $request->input('first_name');
                $NewUser->last_name = $request->input('last_name');
                $NewUser->email = $request->input('email');
                if (empty($NewUser->password)) {
                    $NewUser->password = Hash::make($request->input('password'));
                }
                $NewUser->gender = $request->input('gender');

                $NewUser->birth_date = $request->input('birthday');

                if (!empty($request->input('old-gsm_phone')) && empty($request->input('gsm_phone'))) {
                    $NewUser->gsm_phone = $request->input('old-gsm_phone');
                } else {
                    $NewUser->gsm_phone = DepartmentHelper::fixnumber($request->input('gsm_phone'));//str_replace_first("-","",trim($request->input('gsm_phone')));
                }

                $NewUser->save();
                $dosya = $request->file('photo_file');
                if (!empty($dosya)) {
                    $path = storage_path("user_files/user_" . $NewUser->id . "/");
                    $filename = "PF_" . date('YmdHis') . $dosya->getClientOriginalName();
                    $dosya->move($path, $filename);
                    $NewUser->profile_image = "user_files/user_" . $NewUser->id . "/" . $filename;
                    $NewUser->save();
                }

                ////////////////////userinfo/////////////////////////////////////////////////
                $languages = "";
                $classes = "";
                if (!empty($request->input('languages'))) {
                    foreach ($request->input('languages') as $language) {
                        $languages .= "," . $language;
                    }
                }
                if (!empty($request->input('licenseclasses'))) {
                    foreach ($request->input('licenseclasses') as $class) {
                        $classes .= "," . $class;
                    }
                }

                $user_info->languages = $languages;
                $user_info->classes = $classes;
                $user_info->photo = $NewUser->profile_image;
                $user_info->department_id = $request->input('department_id');
                $user_info->svnr = (!empty($request->input('svnr'))) ? $request->input('svnr') : null;
                $user_info->title = (!empty($request->input('title'))) ? $request->input('title') : "";
                $user_info->nationality = (!empty($request->input('nationality'))) ? $request->input('nationality') : "";
                //$user_info->license_number=(!empty($request->input('license_number')))?$request->input('license_number'):0;
                // $user_info->license_date=(!empty($request->input('license_date')))?$request->input('license_date'):null;
                $residense_permit=(!empty($request->input('residense_permit'))) ? $request->input('residense_permit') : "citizen";
                $user_info->residense_permit = $residense_permit;
                if($residense_permit=='citizen'){
                    $user_info->residense_permit_ends = null;
                }else{
                    $user_info->residense_permit_ends = (!empty($request->input('residense_permit_ends'))) ? $request->input('residense_permit_ends') : null;
                }


                $user_info->work_place = (!empty($request->input('work_place'))) ? $request->input('work_place') : "office";
                $user_info->address_id = (!empty($request->input('address_id'))) ? $request->input('address_id') : 0;
                $user_info->residential_id = (!empty($request->input('residential_id'))) ? $request->input('residential_id') : null;
                $user_info->comments = (!empty($request->input('comments'))) ? $request->input('comments') : null;
                //$user_info->gps_color=(!empty($request->input('gps_color')))?$request->input('gps_color'):null;

                $user_info->save();

                ////////////////////userinfo/////////////////////////////////////////////////
                if ($request->input('role_id') > 0) {
                    $role = Role::find($request->input('role_id'));
                    $old_role_id = RoleUser::whereUserId($NewUser->id)->first() ? RoleUser::whereUserId($NewUser->id)->first()->role_id : 0;
                    $old_role = Role::find($old_role_id);
                    if (isset($old_role)) {
                        $NewUser->detachRoles([$old_role]);
                    }
                    $NewUser->attachRole($role);
                }

            });


            return 'ok';


        }///postcountry


        $departments = Department::all();
        $roles = Role::where("system_user", "=", 0)->select(["name", "description", "id"])->get();
        $role_id = RoleUser::whereUserId($NewUser->id)->first() ? RoleUser::whereUserId($NewUser->id)->first()->role_id : 0;
        $departments = Department::all();
        $languages = Language::all()->toArray();
        $langDizi = array();
        $i = 0;
        foreach ($languages as $lan) {
            $langDizi[$i] = $lan['title'];
            $i++;
        }


        return view('themes.' . static::$tf . '.manager.personnel.update',

            [
                'title' => 'Create Personnel',
                'departments' => $departments,
                'languages' => $langDizi,
                'user_info' => $user_info,
                'roles' => $roles,
                'role_id'=>$role_id

            ]);
    }

    public function profile($personnel_id = 0)
    {
        $user_info = UserInfo::with('user')->where('id', '=', $personnel_id)->first();
        $departments = Department::all();
        $languages = Language::all();
        return view('themes.' . static::$tf . '.manager.personnel.profile',

            [
                'title' => 'Personnel Details',
                'departments' => $departments,
                'languages' => $languages,
                'user_info' => $user_info,


            ]);
    }



    public function file_create(Request $request, $user_id = 0)
    {

        if ($request->isMethod('post')) {


            $rules = [
                'file' => 'required|mimes:png,jpg,jpeg,doc,docx,xls,xlsx,pdf,gif,txt',
            ];
            $this->validate($request, $rules);
            \DB::transaction(function () use ($request) {
                $dosya = $request->file('file');

                if (!empty($dosya)) {

                    $path = storage_path("user_files/user_" . $request->input('user_id'));
                    $filename = "PERSONAL_" . $dosya->getClientOriginalName();
                    $dosya->move($path, $filename);
                    $filename = "user_files/user_" . $request->input('user_id') . "/" . $filename;
                    $user = User::find($request->input('user_id'));
                    $description = (!empty($request->input('description'))) ?
                        $request->input('description') : date('d.m.Y') . ' dated file for ' . $user->fullname();
                    $newFile = new UserFile();
                    $newFile->description = $description;
                    $newFile->user_id = $request->user_id;
                    $newFile->file = $filename;
                    $newFile->file_type = $request->file_type;
                    $newFile->date = Carbon::now();
                    $newFile->save();

                }

            });
            return "ok";
            /// return redirect()->route('manager.department.files', array('id' => $request->user_id));
        }////post
        $user = User::find($user_id);
        return view('themes.' . static::$tf . '.manager.personnel.files.create', array('user' => $user));
    }

    public function file_delete(Request $request)
    {
        $file_id = $request["file_id"];
        $id = \Illuminate\Support\Facades\Auth::id();
        $password = $request["password"];
        if ($password != "") {
            if (\Illuminate\Support\Facades\Auth::attempt(['id' => $id, 'password' => $password, 'status' => 1, 'banned_until' => null], true)) {


                UserFile::where('id', '=', $file_id)->delete();

                return response("ok", 200);
            } else {

                return response("Please enter a valid password", 422);
            }
        } else {
            return response("Please enter password", 422);
        }

    }

    public function file_update($file_id = 0, Request $request)
    {


        $newFile = UserFile::find($file_id);


        if ($request->isMethod('post')) {


            $rules = [
                'file' => 'mimes:png,jpg,jpeg,doc,docx,xls,xlsx,pdf,gif',
            ];
            $this->validate($request, $rules);
            \DB::transaction(function () use ($request, $newFile) {
                $dosya = $request->file('file');

                if (!empty($dosya)) {
                    $path = storage_path("user_files/user_" . $request->input('user_id'));
                    $filename = "PERSONAL_" . $dosya->getClientOriginalName();
                    $dosya->move($path, $filename);
                    $filename = "user_files/user_" . $request->input('user_id') . "/" . $filename;
                    $newFile->file = $filename;
                }

                $newFile->description = (!empty($request->input('description'))) ? $request->input('description') : $newFile->description;
                $newFile->file_type = $request->file_type;
                $newFile->save();
            });

            return "ok";

        }////post

        $title = "Update file for " . $newFile->user->fullname();
        $dz = explode("/", $newFile->file);
        $filename = $dz[count($dz) - 1];

        return view('themes.' . static::$tf . '.manager.personnel.files.update', array('file' => $newFile, 'title' => $title, 'filename' => $filename));
    }



    public function vacation_create($user_id = 0, Request $request){

        if ($request->isMethod('post')) {
            $rules = [
                'start_at' => 'required|date|after:now',
                'end_at' => 'required|date|after:start_at',
            ];

            $vacation= UserVacation::where('user_id','=',$request->input('user_id'))
                ->where('start_at','<=',$request->input('start_at'))
                ->where('end_at','>=',$request->input('start_at'))
                ->first();

            if(!empty($vacation->id)){////başka bir tatil
                $data['message'] = "There is another vacation for user between selected dates - start_at";
                return response($data, 422);
            }/////başka bir tatil


            $vacation= UserVacation::where('user_id','=',$request->input('user_id'))
                ->where('start_at','<=',$request->input('end_at'))
                ->where('end_at','>=',$request->input('end_at'))
                ->first();

            if(!empty($vacation->id)){////başka bir tatil
                $data['message'] = "There is another vacation for user between selected dates - end at";
                return response($data, 422);
            }/////başka bir tatil


            $this->validate($request, $rules);
            \DB::transaction(function () use ($request) {
                $vacation = new UserVacation;

                $vacation->user_id = $request->input('user_id');
                $vacation->start_at = $request->input('start_at');
                $vacation->end_at = $request->input('end_at');
                $vacation->description = (!empty($request->input('description'))) ? $request->input('description') : "";
                $vacation->save();


         /*       for($i=$request->input('start_at');$i<=$request->input('end_at');$i++){
                $starts=Carbon::parse($i)->format('Y-m-d H:i:s');// $i." 00:00:00";
                $ends= Carbon::parse($i)->format('Y-m-d H:i:s');// $i." 23:59:59";
                UserWork::where('id','=',$request->input('user_id'))->where('start_at','>',$starts)
                    ->where('end_at','<',$ends)->delete();


                    $Log = new DriverStateChangeLog();
                    $Log["user_id"] = $starts;
                    $Log["driver_id"] = $ends;
                    $Log["is_online"] = $request->user_id;
                    $Log->save();


                }*/

                $starts=Carbon::parse($request->input('start_at'))->format('Y-m-d H:i:s');// $i." 00:00:00";
                $ends= Carbon::parse($request->input('end_at'))->format('Y-m-d H:i:s');// $i." 23:59:59";
                UserWork::where('user_id','=',$request->input('user_id'))
                    ->where('start_at','>',$starts)
                    ->where('end_at','<',$ends)
                    ->delete();



            });
            return "ok";
        }
        $user = User::find($user_id);

        $title = "Create vacation for " . $user->fullname();
        return view('themes.' . static::$tf . '.manager.personnel.vacation.create', array('title' => $title, 'user' => $user));


    }/////create

    public function vacation_update($vacation_id = 0, Request $request)
    {


        $vacation = UserVacation::find($vacation_id);

        if ($request->isMethod('post')) {
            $rules = [
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
            ];
            $this->validate($request, $rules);
            \DB::transaction(function () use ($request, $vacation) {
                $vacation->user_id = $request->input('user_id');
                $vacation->start_at = $request->input('start_at');
                $vacation->end_at = $request->input('end_at');
                $vacation->description = (!empty($request->input('description'))) ? $request->input('description') : "";
                $vacation->save();
            });

            return "ok";
        }
        $user = User::find($vacation->user_id);

        $title = "Update vacation for " . $user->fullname();
        return view('themes.' . static::$tf . '.manager.personnel.vacation.update', array('title' => $title, 'user' => $user, 'vacation' => $vacation));

    }/////create

    public function vacation_delete(Request $request)
    {
        $vacation_id = $request["vacation_id"];
        $id = \Illuminate\Support\Facades\Auth::id();
        $password = $request["password"];
        if ($password != "") {
            if (\Illuminate\Support\Facades\Auth::attempt(['id' => $id, 'password' => $password, 'status' => 1, 'banned_until' => null], true)) {

                UserVacation::where('id', '=', $vacation_id)->delete();
                return response("ok", 200);
            } else {
                return response("Please enter a valid password", 422);
            }
        } else {
            return response("Please enter password", 422);
        }

    }

    public function personnel_delete(Request $request)
    {
        $personnel_id = $request["personnel_id"];
        $id = \Illuminate\Support\Facades\Auth::id();
        $password = $request["password"];
        if ($password != "") {
            if (\Illuminate\Support\Facades\Auth::attempt(['id' => $id, 'password' => $password, 'status' => 1, 'banned_until' => null], true)) {

                $user_info = UserInfo::where('id', '=', $personnel_id)->first();

                User::where('id', '=', $user_info->user_id)->delete();
                UserVacation::where('user_id', '=', $user_info->user_id)->delete();
                UserFile::where('user_id', '=', $user_info->user_id)->delete();
                UserInfo::where('user_id', '=', $user_info->user_id)->delete();

                return response("ok", 200);
            } else {
                return response("Please enter a valid password", 422);
            }
        } else {
            return response("Please enter password", 422);
        }

    }


    public function find_user_id($info_id = 0)
    {
        $user_info = UserInfo::find($info_id);
        return ['user_info_id' => $user_info->id, 'user_id' => $user_info->user_id];
    }

}
