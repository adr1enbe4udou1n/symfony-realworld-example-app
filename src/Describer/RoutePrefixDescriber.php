<?php

namespace App\Describer;

use Nelmio\ApiDocBundle\Describer\DescriberInterface;
use OpenApi\Annotations\OpenApi;

class RoutePrefixDescriber implements DescriberInterface
{
    public function describe(OpenApi $api)
    {
        foreach ($api->paths as $path) {
            $path->path = str_replace('/api', '', $path->path);
        }
    }
}
