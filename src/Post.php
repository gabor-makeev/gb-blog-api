<?php

namespace Gabormakeev\GbBlogApi;

class Post
{
    public function __construct(
        private UUID $uuid,
        private UUID $authorUuid,
        private string $title,
        private string $text
    ) {}

    public function __toString(): string
    {
        return "Post: Author with UUID $this->authorUuid created a post with title '$this->title':\n'$this->text'";
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return UUID
     */
    public function getAuthorUuid(): UUID
    {
        return $this->authorUuid;
    }

    /**
     * @param UUID $authorUuid
     */
    public function setAuthorUuid(UUID $authorUuid): void
    {
        $this->authorUuid = $authorUuid;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
