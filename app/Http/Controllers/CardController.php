<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardController extends Controller
{

    public function createCard(Request $request,$id){
        $card = $this->stripe->customers->createSource(
            $id,
            ['source' => $request->token]
        );
        return success('Card Created Successfully',$card);
    }

}
