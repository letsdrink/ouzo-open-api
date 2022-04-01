<?php

namespace Ouzo\OpenApi\Util;

use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Strings;

class UriUtils
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    public static function sanitizeUri(RouteRule $routeRule): string
    {
        $uri = $routeRule->getUri();
        $uri = preg_replace('/:(.*?)\//', '{\1}/', $uri);
        return preg_replace('/:(.*?)$/', '{\1}', $uri);
    }

    /** @return string[] */
    public static function getPathParameterNames(string $uri): array
    {
        preg_match_all('/\\{(.*?)\\}/', $uri, $pathParameterNames);
        return FluentArray::from($pathParameterNames)
            ->flatten()
            ->filterNotBlank()
            ->filter(fn(string $param) => !Strings::contains($param, '{'))
            ->values()
            ->toArray();
    }
}
