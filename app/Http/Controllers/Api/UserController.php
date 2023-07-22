<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\CreateUserDebtResource;
use App\Http\Resources\UserResourse;
use App\Http\Resources\UserShowResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\debtbook;
use mysql_xdevapi\Collection;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::orderBy('name')->orderByRaw("SUBSTRING(name, 1, 1) ASC")->where('role', "debtor")->get();
           return $this->success($this->ok, "success", UserResourse::collection(count($users) != 0 ? $users : null));
        } catch (\Exception $e){
            return $this->error($e->getCode(),  $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:255', "unique:users"],
            'phone' => ['nullable', 'digits:12'],
            'amount' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return $this->error($this->unallowed, $validator->errors()->first());
        }
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'total_debt' =>  $request->input('amount'),
            ]);

            debtbook::create([
                'user_id' => $user->id,
                'amount' => $request->input('amount'),
                'status' => true,
            ]);

            return $this->success($this->ok, 'success', new CreateUserDebtResource($user));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::where('role', "debtor")->find($id);
            return $this->success($this->ok, "success", new UserShowResource($user));
        } catch (\Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request){
        try {
            $searchUser = User::where('name', 'like', '%'.$request->search.'%')->where('role', "debtor")->get();
            if(count($searchUser) > 0){
                return $this->success($this->ok, "success", UserResourse::collection(count($searchUser) != 0 ? $searchUser : null));
            } else{
                return response()->json([
                    'status' => '404',
                    'error' => 'No information found',
                ]);
            }
        } catch (\Exception $e){
            return $this->error(400, $e->getMessage());
        }
    }

    public function statistic(){
        try {
            $user_count = User::where('role', 'debtor')->get()->count();
            $amount_total = User::where('total_debt', '>', 0)->sum('total_debt');
            $todayUsers = User::whereDate('created_at', today())->get()->count();

            return $this->success($this->ok, "success", [
                'user_count' => $user_count,
                'total_debt' => $amount_total,
                'today_debt' => $todayUsers,
            ]);

        } catch (\Exception $e){
            return $this->error(400, $e->getMessage());
        }
    }
}
