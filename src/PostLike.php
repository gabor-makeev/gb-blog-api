<?php

namespace Gabormakeev\GbBlogApi;

class PostLike
{
    public function __construct(
        private UUID $uuid,
        private UUID $postUuid,
        private UUID $userUuid
    ) {}

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
     * @return UUID
     */
    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    /**
     * @param UUID $userUuid
     */
    public function setUserUuid(UUID $userUuid): void
    {
        $this->userUuid = $userUuid;
    }
}
