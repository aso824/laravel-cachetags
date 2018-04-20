<?php

namespace Aso824\CacheTags;

class TaggedCache extends \Illuminate\Cache\TaggedCache
{
    /**
     * Name of key in cache where associations will be stored.
     *
     * @var string
     */
    public const DATA_KEY = '_tags_data';

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  float|int $minutes
     * @return void
     * @throws \Exception
     */
    public function put($key, $value, $minutes = null): void
    {
        $this->pushKey($key);

        parent::put($key, $value, $minutes);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     * @throws \Exception
     */
    public function forever($key, $value): void
    {
        $this->pushKey($key);

        parent::forever($key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key): bool
    {
        $this->popKey($key);

        return parent::forget($key);
    }

    /**
     * Remove all items from the cache.
     */
    public function flush(): void
    {
        $tags = explode('|', $this->tags->getNamespace());

        if (! empty($tags)) {
            $this->flushTags($tags);
        }

        parent::flush();
    }

    /**
     * Delete all items by given tags in behaviour like builtin cache drivers (Redis/Memcached)
     *
     * @param array $tags
     * @return void
     */
    protected function flushTags(array $tags): void
    {
        /** @var array $keys */
        $keys = $this->store->get(self::DATA_KEY);

        // Iterate over each tag of given TagSet with ex. cache()->tags([foo, bar, ...])->...
        foreach ($tags as $tag) {
            // Iterate over each item in data array
            foreach ($keys as $combinedKey => $value) {
                // Check if tag is in data array key name (combinedKey format is "foo|bar|...")
                if (strpos($combinedKey, $tag) !== false) {
                    // Delete all elements in cache that belongs to this tag
                    foreach ($keys[$combinedKey] as $key) {
                        $this->store->forget($key);
                    }

                    unset($keys[$combinedKey]);
                }
            }
        }

        $this->store->forever(self::DATA_KEY, $keys);
    }

    /**
     * Save association between key and tag into store.
     *
     * @param string $key
     * @return void
     * @throws \Exception
     */
    protected function pushKey(string $key): void
    {
        $keys = $this->store->get(self::DATA_KEY);
        $namespace = $this->tags->getNamespace();

        if (empty($namespace)) {
            return;
        }

        if (! \is_array($keys)) {
            $keys = [];
        }

        if (! isset($keys[$namespace])) {
            $keys[$namespace] = [$key];
        } else {
            $keys[$namespace][] = $key;
        }

        $this->store->forever(self::DATA_KEY, $keys);
    }

    /**
     * Remove association between key and tag from store.
     *
     * @param string $key
     * @return void
     */
    protected function popKey(string $key): void
    {
        $keys = $this->store->get(self::DATA_KEY);
        $namespace = $this->tags->getNamespace();

        if (empty($namespace) || ! \is_array($keys)) {
            return;
        }

        if (! isset($keys[$namespace])) {
            return;
        }

        unset($keys[$namespace][$key]);
    }
}