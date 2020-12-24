<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Repository;

use Predis\Client;

trait RedisTrait
{
    /**
     * @param Client $predis
     * @param string $key
     * @param int $ttl
     */
    public function lock(Client $predis, string $key, int $ttl): void
    {
        while ($predis->get($key)) {
            usleep(50000);
        }
        $predis->setex($key, $ttl, true);
    }

    /**
     * @param Client $predis
     * @param string $key
     */
    public function unlock(Client $predis, string $key): void
    {
        $predis->del($key);
    }

    /**
     * @param Client $predis
     * @param string $key
     * @param int $ttl
     * @param \Closure $closure
     * @throws \Throwable
     * @return mixed
     */
    public function transaction(Client $predis, string $key, int $ttl, \Closure $closure)
    {
        $this->lock($predis, $key, $ttl);
        try {
            $res = $closure();
        } catch (\Exception $e) {
            $this->unlock($predis, $key);
            throw $e;
        } catch (\Throwable $e) {
            $this->unlock($predis, $key);
            throw $e;
        }
        // Unlock in correct flow.
        $this->unlock($predis, $key);

        return $res;
    }
}
