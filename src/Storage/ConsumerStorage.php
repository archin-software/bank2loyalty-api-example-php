<?php

namespace Example\Storage;

class ConsumerStorage
{
    /**
     * File path.
     *
     * @var string
     */
    private string $file = __DIR__ . '/../../storage/consumers.json';

    /**
     * Key/value list of consumers.
     *
     * @var array
     */
    private array $consumers = [];

    /**
     * Create new consumers storage and read JSON.
     */
    public function __construct()
    {
        if (file_exists($this->file)) {
            $this->consumers = json_decode(file_get_contents($this->file), true);
        }
    }

    /**
     * Get consumer by id.
     *
     * @param string $id
     * @return array|null
     */
    public function getConsumer(string $id): ?array
    {
        return $this->consumers[$id] ?? null;
    }

    /**
     * Add or update consumer.
     *
     * @param string $id
     * @param mixed $data
     */
    public function addOrUpdateConsumer(string $id, $data): void
    {
        $this->consumers[$id] = $data;
    }

    /**
     * Delete consumer.
     *
     * @param string $id
     */
    public function deleteConsumer(string $id)
    {
        unset($this->consumers[$id]);
    }

    /**
     * Store JSON.
     */
    public function __destruct()
    {
        file_put_contents($this->file, json_encode($this->consumers));
    }
}
