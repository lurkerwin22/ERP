<?php

namespace App\Services\Ai\Services;

use App\Services\Ai\Tools\StockTool;
use App\Services\Ai\Tools\SalesTool;
use App\Services\Ai\Tools\CustomerDebtTool;

class IntentRouter
{
    public function __construct(
        protected StockTool $stockTool,
        protected SalesTool $salesTool,
        protected CustomerDebtTool $debtTool
    ) {}

    public function matchAndExecute(string $message): ?array
    {
        $input = mb_strtolower(trim($message));

        // 1. Stock & Inventory Intent
        if (preg_match('/(rupture|stock|seuil|réapprovisionnement|manquer)/u', $input)) {
            $data = $this->stockTool->execute([]);
            return [
                'role' => 'assistant',
                'content' => $this->formatStockResponse($data)
            ];
        }

        // 2. Sales Summary Intent
        if (preg_match('/(vente|chiffre d\'affaire|ca|résumé des ventes)/u', $input)) {
            $data = $this->salesTool->execute(['period' => 'this_month']);
            $totalSales = number_format((float) ($data['total_sales'] ?? $data['total'] ?? 0), 2);
            $count = $data['count'] ?? $data['sales_count'] ?? 0;

            return [
                'role' => 'assistant',
                'content' => "📊 **Résumé des ventes ce mois-ci :**\n- Chiffre d'affaires : **{$totalSales} TND**\n- Total commandes : **{$count}**"
            ];
        }

        // 3. Customer Debts Intent
        if (preg_match('/(dette|créance|impai|débiteur|doivent)/u', $input)) {
            $data = $this->debtTool->execute(['min_debt' => 0]);
            return [
                'role' => 'assistant',
                'content' => $this->formatDebtResponse($data)
            ];
        }

        return null;
    }

    protected function formatStockResponse(array $data): string
    {
        // Unwrap nested items array if present
        $items = $data['items'] ?? $data['products'] ?? $data['data'] ?? $data;

        if (!is_array($items) || empty($items)) {
            return "✅ Aucun produit en rupture ou proche du seuil d'alerte.";
        }

        $lines = ["🚨 **Alerte Stock & Ruptures :**"];
        $count = 0;

        foreach ($items as $item) {
            if ($count >= 10) break;

            if (is_array($item)) {
                $name = $item['name'] ?? $item['label'] ?? 'Produit inconnu';
                $stock = $item['stock'] ?? $item['quantity'] ?? 0;
                $threshold = $item['alert_threshold'] ?? $item['min_stock'] ?? '-';
                $lines[] = "- **{$name}** : {$stock} unités (Seuil: {$threshold})";
                $count++;
            } elseif (is_object($item)) {
                $name = $item->name ?? 'Produit inconnu';
                $stock = $item->stock ?? 0;
                $threshold = $item->alert_threshold ?? '-';
                $lines[] = "- **{$name}** : {$stock} unités (Seuil: {$threshold})";
                $count++;
            }
        }

        return count($lines) > 1 ? implode("\n", $lines) : "✅ Aucun produit en rupture ou proche du seuil d'alerte.";
    }

    protected function formatDebtResponse(array $data): string
    {
        $debts = $data['debts'] ?? $data['customers'] ?? $data['data'] ?? $data;

        if (!is_array($debts) || empty($debts)) {
            return "✅ Aucun client n'a de créances en cours.";
        }

        $lines = ["💰 **Clients Débiteurs :**"];
        $count = 0;

        foreach ($debts as $d) {
            if ($count >= 10) break;

            if (is_array($d)) {
                $name = $d['customer_name'] ?? $d['name'] ?? 'Client inconnu';
                $balance = number_format((float) ($d['balance'] ?? $d['debt'] ?? 0), 2);
                $lines[] = "- **{$name}** : {$balance} TND";
                $count++;
            } elseif (is_object($d)) {
                $name = $d->customer_name ?? $d->name ?? 'Client inconnu';
                $balance = number_format((float) ($d->balance ?? 0), 2);
                $lines[] = "- **{$name}** : {$balance} TND";
                $count++;
            }
        }

        return count($lines) > 1 ? implode("\n", $lines) : "✅ Aucun client n'a de créances en cours.";
    }
}