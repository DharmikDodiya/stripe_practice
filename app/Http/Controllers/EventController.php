<?php

namespace App\Http\Controllers;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ListingApiTrait;
    public function retrieveEvent($id){
        $event = $this->stripe->events->retrieve(
            $id,
        []
        );
        if($event){
            return success('Event Details',$event);
        }
            return error('Event Not Found');
    }

    public function listEvent(){
        $event = $this->stripe->events->all();
        if($event){
            $data = $this->filterSearchPagination($event,$searchable_fields ?? null);
            return success('Event List',[
                'event'        => $data['query'],
                'count'        => $data['count']
            ]);
        }
            return error('Event Not Found');
    }
}
