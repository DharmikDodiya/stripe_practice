<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
class CardController extends Controller
{
    use ListingApiTrait;
    public function createCard(Request $request,$id){
        $card = $this->stripe->customers->createSource(
            $id,
            ['source' => $request->token]
        );
        return success('Card Created Successfully',$card);
    }

    public function getCard($cus_id,$card_id){
        $card = $this->stripe->customers->retrieveSource(
          $cus_id,
          $card_id,
          []
        );
        return success('Card details',$card);
    }

    public function deleteCard($cus_id,$card_id){
        $card = $this->stripe->customers->deleteSource(
        $cus_id,
        $card_id,
        []
        );
        return success('Card Deleted Successfully');
    }

    public function listCard($id){
        $cards = $this->stripe->customers->allSources(
            $id,
            [
                'object' => 'card',
            ]
        );
        $data = $this->filterSearchPagination($cards,$searchable_fields ?? null);
        return success('Cards List',[
            'cards'        => $data['query'],
            'count'        => $data['count']
        ]);
    }
}
