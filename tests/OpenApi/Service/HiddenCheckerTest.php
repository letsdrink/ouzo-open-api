<?php

namespace Ouzo\OpenApi\Service;

use Ouzo\Fixtures\HiddenController;
use Ouzo\Fixtures\SampleController;
use Ouzo\Http\HttpMethod;
use Ouzo\Routing\RouteRule;
use PHPUnit\Framework\TestCase;

class HiddenCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCheckClassIsHidden()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', HiddenController::class, 'status', true);

        $hiddenChecker = new HiddenChecker();

        //when
        $hidden = $hiddenChecker->isHidden($routeRule);

        //then
        $this->assertTrue($hidden);
    }

    /**
     * @test
     */
    public function shouldCheckMethodIsHidden()
    {
        //given
        $routeRule = new RouteRule(HttpMethod::GET, '/url', SampleController::class, 'hiddenMethod', true);

        $hiddenChecker = new HiddenChecker();

        //when
        $hidden = $hiddenChecker->isHidden($routeRule);

        //then
        $this->assertTrue($hidden);
    }

    /**
     * @test
     */
    public function shouldCheckClassAndMethodIsNotHidden()
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
