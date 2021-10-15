<?php

namespace Ouzo\Fixtures;

use Ouzo\Utilities\Strings;

class UsersController
{
    public function show()
    {
    }

    public function details(int $id)
    {
    }

    public function update(int $id, UserRequest $userRequest): void
    {
    }

    public function filter(UserQueryRequest $userQueryRequest): void
    {
    }

    public function status(): string
    {
        return Strings::EMPTY_STRING;
    }

    public function info(): User
    {
        return new User();
    }

    /** @return string[] */
    public function tagNames(): array
    {
        return [];
    }

    /** @return Tag[] */
    public function tags(): array
    {
        return [];
    }

    public function categories(): array
    {
        return [];
    }
}
