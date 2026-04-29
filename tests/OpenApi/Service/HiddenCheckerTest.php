<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Fixtures\HiddenController;
use Ouzo\Fixtures\SampleController;
use Ouzo\Http\HttpMethod;
use Ouzo\Routing\RouteRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HiddenCheckerTest extends TestCase
{
    #[Test]
    public function shouldCheckClassIsHidden(): void
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', HiddenController::class, 'status', true);

        $hiddenChecker = new HiddenChecker();

        //when
        $hidden = $hiddenChecker->isHidden($routeRule);

        //then
        $this->assertTrue($hidden);
    }

    #[Test]
    public function shouldCheckMethodIsHidden(): void
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', SampleController::class, 'hiddenMethod', true);

        $hiddenChecker = new HiddenChecker();

        //when
        $hidden = $hiddenChecker->isHidden($routeRule);

        //then
        $this->assertTrue($hidden);
    }

    #[Test]
    public function shouldCheckClassAndMethodIsNotHidden(): void
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', SampleController::class, 'scalarInReturn', true);

        $hiddenChecker = new HiddenChecker();

        //when
        $hidden = $hiddenChecker->isHidden($routeRule);

        //then
        $this->assertFalse($hidden);
    }
}
