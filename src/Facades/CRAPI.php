<?php

namespace Canzell\Facades;

use Illuminate\Support\Facades\Facade;
use Canzell\Http\Clients\CRAPIClient;

class CRAPI extends Facade
{

    static public function getFacadeAccessor()
    {
        return CRAPIClient::class;
    }

}