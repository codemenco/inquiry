<?php

namespace Codemenco\Inquiry;

use Illuminate\Support\Facades\Facade;

class InquiryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Inquiry';
    }
}
