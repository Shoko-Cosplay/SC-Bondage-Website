<?php

namespace App\Infrastructure\Legal;

class CreditItem
{
    private string $type = "";

    private string $author = "";

    private string $link = "";

    private string $path = "";


    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getPath(): string
    {
        return $this->path;
    }

}
