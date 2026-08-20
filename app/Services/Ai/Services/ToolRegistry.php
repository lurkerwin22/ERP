<?php

namespace App\Services\Ai\Services;

use App\Services\Ai\Tools\BaseTool;
use App\Services\Ai\Tools\StockTool;
use App\Services\Ai\Tools\DebtTool;
use App\Services\Ai\Tools\SalesTool;
use App\Services\Ai\Tools\SalesByPeriodTool;
use App\Services\Ai\Tools\ProductPerformanceTool;
use App\Services\Ai\Tools\CustomerHistoryTool;
use App\Services\Ai\Tools\UnpaidSalesTool;

class ToolRegistry
{
    protected array $tools = [];

    public function __construct()
    {
        $this->register(new StockTool());
        $this->register(new DebtTool());
        $this->register(new SalesTool());
        $this->register(new SalesByPeriodTool());
        $this->register(new ProductPerformanceTool());
        $this->register(new CustomerHistoryTool());
        $this->register(new UnpaidSalesTool());
    }

    public function register(BaseTool $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }

    public function getSchemas(): array
    {
        return array_map(fn (BaseTool $tool) => $tool->toSchema(), array_values($this->tools));
    }

    // Added alias method to resolve 500 error
    public function getTools(): array
    {
        return $this->getSchemas();
    }

    public function execute(string $name, array $args = []): array
    {
        if (!isset($this->tools[$name])) {
            return [
                'error' => "Tool '{$name}' is not registered in the AI Business Tool layer.",
            ];
        }

        return $this->tools[$name]->execute($args);
    }
}