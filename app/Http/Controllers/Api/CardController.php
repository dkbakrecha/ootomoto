<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\CustomerCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller {

    /**
     * API to get card details of customer
     */
    public function index() {
        $currentUserID = Auth::guard('api')->id();
        $userData = User::where([
                    ['id', '=', $currentUserID],
                ])
                ->select('users.id', 'users.unique_id', 'users.name', 'users.profile_image', 'users.phone', 'users.email', 'users.gender', 'users.preferred_language', 'users.confirmation_alert', 'users.address', 'users.status')
                ->first();

        $data = $userData->toArray();

        //$data['profile_image'] = public_path() . '/images/profile/' . $data['profile_image'];
        if (!empty($data['profile_image'])) {
            $data['profile_image'] = URL::to('/') . '/images/profile/' . $data['profile_image'];
        }
        return response()->json([
                    "success" => true,
                    'data' => $data,
                        ], 200);
    }

    /**
     * API to store new card details of customer
     */
    public function store(Request $request) {
        $currentUserID = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'card_number' => 'required',
                    'card_holder' => 'required',
                    'card_expyear' => 'required',
                    'card_expyear' => 'required',
                    'card_cvv' => 'required',
                    'is_default' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $card = new CustomerCard();
        $card->card_number = $request->get('card_number');
        $card->card_holder = $request->get('card_holder');
        $card->card_expmonth = $request->get('card_expmonth');
        $card->card_expyear = $request->get('card_expyear');
        $card->card_cvv = $request->get('card_cvv');
        $card->is_default = $request->get('is_default');
        $card->customer_id = $currentUserID;

        /* $insertData = [];
          $insertData = $request->all();
          $insertData['customer_id'] = $currentUserID;
          $card->fill($insertData); */

        $card->save();

        return response()->json([
                    "success" => true,
                    'message' => "Card added successfully.",
                        ], 200);
    }

}
