<?php

namespace ADAL;

abstract class AdalErrorMessage
{

    const UNSUPPORTED_AUTHORITY_VALIDATION = 'Authority validation is not supported for this type of authority';
    const AUTHORITY_INVALID_URI_FORMAT = "'authority' should be in Uri format";

    const AUTHORITY_URI_INSECURE = "'authority' should use the 'https' scheme";

    const AUTHORITY_URI_INVALID_PATH =
            "'authority' Uri should have at least one segment in the path (i.e. https://<host>/<path>/...)";
            
}