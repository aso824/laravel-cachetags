<?php

namespace Aso824\CacheTags;

class FileStore extends \Illuminate\Cache\FileStore
{
    use MakeCacheTaggable;
}