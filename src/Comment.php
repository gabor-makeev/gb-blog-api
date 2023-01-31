<?php

namespace Gabormakeev\GbBlogApi;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private UUID $authorUuid,
        private UUID $postUuid,
        private string $text
    ) {}

    public function __toString(): string
    {
        return "Comment: Author with UUID $this->authorUuid left a comment for the post with UUID $this->postUuid:\n'$this->text'";
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
     * @return UUID
     */
    public function getPostUuid(): UUID
    {
        return $this->postUuid;
    }

    /**
     * @param UUID $postUuid
     */
    public function setPostUuid(UUID $postUuid): void
    {
        $this->postUuid = $postUuid;
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
