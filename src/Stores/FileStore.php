<?php

namespace aso824\CacheTags;

class FileStore extends \Illuminate\Cache\FileStore
{
    use MakeCacheTaggable;
}