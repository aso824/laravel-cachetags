<?php

namespace Aso824\CacheTags;

use Illuminate\Cache\TagSet;

trait MakeCacheTaggable
{
    /**
     * Return package's tagged cache class.
     *
     * @param string|array $names
     * @return TaggedCache
     */
    public function tags($names): TaggedCache
    {
        return new TaggedCache($this, new TagSet($this, \is_array($names) ? $names : \func_get_args()));
    }
}