<?php

namespace Ouzo\OpenApi\Model;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/3.0.1/versions/3.0.1.md#pathItemObject
 */
class PathItem
{
    private ?Operation $get = null;

    private ?Operation $put = null;

    private ?Operation $post = null;

    private ?Operation $delete = null;

    public function getGet(): ?Operation
    {
        return $this->get;
    }

    public function setGet(?Operation $get): static
    {
        $this->get = $get;
        return $this;
    }

    public function getPut(): ?Operation
    {
        return $this->put;
    }

    public function setPut(?Operation $put): static
    {
        $this->put = $put;
        return $this;
    }

    public function getPost(): ?Operation
    {
        return $this->post;
    }

    public function setPost(?Operation $post): static
    {
        $this->post = $post;
        return $this;
    }

    public function getDelete(): ?Operation
    {
        return $this->delete;
    }

    public function setDelete(?Operation $delete): static
    {
        $this->delete = $delete;
        return $this;
    }
}
