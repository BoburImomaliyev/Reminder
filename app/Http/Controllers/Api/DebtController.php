<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CreateUserDebtResource;
use App\Http\Resources\UserShowResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\debtbook;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'user_id' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return $this->error($this->unallowed, $validator->errors()->first());
        }
        try {
            $user = User::find($request->user_id);
            $total = (int) $user->total_debt + (int) $request->amount;
            $user->update([
                'total_debt' => $total
            ]);
            $input = $request->all();
            debtbook::create($input);
            return $this->success($this->ok, 'success', new UserShowResource($user));
        } catch (\Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'user_id' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return $this->error($this->unallowed, $validator->errors()->first());
        }
        try {
            $user = User::find($request->user_id);
            $total = (int) $user->total_debt - (int) $request->amount;
            if($total > 0){
                $user->update([
                    'total_debt' => $total
                ]);
                $debt = new debtbook();
                $debt->user_id = $request->user_id;
                $debt->amount = $request->amount;
                $debt->status = false;
                $debt->save();
                return $this->success($this->ok, 'success', new UserShowResource($user));
            } elseif($total === 0) {
                $user->debtbook()->delete();
                $user->delete();
                return $this->success($this->ok, 'Malumotlar o`chirildi');
            } else{
                return $this->error(403, 'An amount greater than the specified amount was entered', $user->total_debt);
            }
        } catch (\Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
