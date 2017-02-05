<?php

namespace ADAL;

abstract class AdalError
{
    
    /**
     * Unknown error.
     */
    const UNKNOWN = 'unknown_error';

    /**
     * 'authority' is not in the list of valid addresses.
     */
    const AUTHORITY_NOT_IN_VALID_LIST = 'authority_not_in_valid_list';

    /**
     * Authority validation failed.
     */
    const AUTHORITY_VALIDATION_FAILED = 'authority_validation_failed';

}