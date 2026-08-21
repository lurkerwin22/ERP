<?php

namespace App\Services\Ai\Services;

use App\Services\Ai\Tools\StockTool;
use App\Services\Ai\Tools\SalesByPeriodTool;
use App\Services\Ai\Tools\DebtTool;

class IntentRouter
{
    public function __construct(
        protected StockTool $stockTool,
        protected SalesByPeriodTool $salesTool,
        protected DebtTool $debtTool
    ) {}

    public function matchAndExecute(string $message): ?array
    {
        $input = mb_strtolower(trim($message));

        // 1. Stock & Inventory Intent
        if (preg_match('/\b(rupture|stock|stocks|seuil|réapprovisionnement|réapprovisionner|manque|manquant)\b/u', $input)) {
            $data = $this->stockTool->execute([]);

            return [
                'role' => 'assistant',
                'content' => $this->formatStockResponse($data)
            ];
        }

        // 2. Sales Summary Intent
        if (preg_match('/\b(vente|ventes|chiffre d[\'’]?affaires?|résumé des ventes|résumé vente|revenu|revenus)\b/u', $input)) {
            $data = $this->salesTool->execute([
                'start_date' => now()->startOfMonth()->format('Y-m-d'),
                'end_date' => now()->endOfMonth()->format('Y-m-d'),
            ]);

            $totalRevenue = number_format(
                (float) ($data['total_revenue_tnd'] ?? 0),
                2,
                '.',
                ''
            );

            $count = (int) ($data['total_orders'] ?? 0);

            $average = number_format(
                (float) ($data['average_order_value_tnd'] ?? 0),
                2,
                '.',
                ''
            );

            return [
                'role' => 'assistant',
                'content' =>
                    "📊 **Résumé des ventes ce mois-ci :**\n" .
                    "- Chiffre d'affaires : **{$totalRevenue} TND**\n" .
                    "- Total commandes : **{$count}**\n" .
                    "- Panier moyen : **{$average} TND**"
            ];
        }

        // 3. Customer Debts Intent
        if (preg_match('/\b(dette|dettes|créance|créances|impayé|impayés|débiteur|débiteurs|doivent|doit)\b/u', $input)) {
            $data = $this->debtTool->execute([
                'min_amount' => 0
            ]);

            return [
                'role' => 'assistant',
                'content' => $this->formatDebtResponse($data)
            ];
        }

        return null;
    }

    protected function formatStockResponse(array $data): string
    {
        $items = $data['products'] ?? $data['items'] ?? $data['data'] ?? [];

        if (!is_array($items) || empty($items)) {
            return "✅ Aucun produit en rupture ou proche du seuil d'alerte.";
        }

        $lines = ["🚨 **Alerte Stock & Ruptures :**"];

        foreach (array_slice($items, 0, 10) as $item) {
            $name = $item['name'] ?? 'Produit inconnu';

            $stock = $item['current_stock']
                ?? $item['stock']
                ?? $item['quantity']
                ?? 0;

            $threshold = $item['alert_threshold']
                ?? $item['min_stock']
                ?? '-';

            $status = ((float) $stock <= 0)
                ? '🔴 RUPTURE'
                : '🟠 STOCK FAIBLE';

            $lines[] = "- **{$name}** : {$stock} unités " .
                "(Seuil : {$threshold}) — {$status}";
        }

        return implode("\n", $lines);
    }

    protected function formatDebtResponse(array $data): string
    {
        $debtors = $data['debtors'] ?? [];

        if (!is_array($debtors) || empty($debtors)) {
            return "✅ Aucun client n'a de créances en cours.";
        }

        $lines = [
            "💰 **Clients Débiteurs :**"
        ];

        foreach (array_slice($debtors, 0, 10) as $debtor) {
            $name = $debtor['name']
                ?? $debtor['customer_name']
                ?? 'Client inconnu';

            $balance = (float) (
                $debtor['outstanding_debt']
                ?? $debtor['balance']
                ?? $debtor['debt']
                ?? 0
            );

            $balanceFormatted = number_format(
                $balance,
                2,
                '.',
                ''
            );

            $lines[] = "- **{$name}** : **{$balanceFormatted} TND**";
        }

        return implode("\n", $lines);
    }
}