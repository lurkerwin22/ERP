<?php

namespace App\Services\Ai\Tools;

abstract class BaseTool
{
    abstract public function name(): string;
    abstract public function description(): string;
    abstract public function parameters(): array;
    abstract public function execute(array $args = []): array;

    public function toSchema(): array
    {
        $params = $this->parameters();

        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => $this->description(),
                'parameters' => [
                    'type' => 'object',
                    'properties' => empty($params) ? (object) [] : $params,
                ],
            ],
        ];
    }
}