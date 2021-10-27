<?php

namespace Ouzo\OpenApi;

use Ouzo\Fixtures\HiddenController;
use Ouzo\Fixtures\UsersController;
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
        $routeRule = new RouteRule(HttpMethod::GET, '/url', UsersController::class, 'hiddenMethod', true);

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
        $routeRule = new RouteRule(HttpMethod::GET, '/url', UsersController::class, 'status', true);

        $hiddenChecker = new HiddenChecker();

        //when
        $hidden = $hiddenChecker->isHidden($routeRule);

        //then
        $this->assertFalse($hidden);
    }
}
