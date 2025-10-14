<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SalesReport
{
    /**
     * Унифицированный date-expression для всех отчётов:
     * если есть paid_at — берём его, иначе created_at.
     */
    protected function dateExpr(): \Illuminate\Database\Query\Expression
    {
        return DB::raw('COALESCE(og.paid_at, og.created_at)');
    }

    /**
     * Базовый запрос по группам заказов с фильтрами.
     */
    protected function baseGroupQuery(string $from, string $to, ?int $locationId = null, ?string $source = null)
    {
        $dt = $this->dateExpr();

        $q = DB::table('order_groups as og')
            ->whereRaw('LOWER(og.status) = ?', ['success'])
            ->whereBetween($dt, [$from, $to]);

        if ($locationId !== null) {
            $q->where('og.location_id', $locationId);
        }
        if ($source !== null) {
            $q->where('og.source', $source);
        }

        return $q;
    }

    public function kpis(string $from, string $to, ?int $locationId = null, ?string $source = null): array
    {
        $dt = $this->dateExpr();

        // Позиции успешных чеков в диапазоне
        $ordersQ = DB::table('orders as o')
            ->join('order_groups as og', 'og.id', '=', 'o.order_group_id')
            ->whereRaw('LOWER(og.status) = ?', ['success'])
            ->whereBetween($dt, [$from, $to]);

        if ($locationId !== null) {
            $ordersQ->where('og.location_id', $locationId);
        }
        if ($source !== null) {
            $ordersQ->where('og.source', $source);
        }

        // Нетто по количеству и выручке (возвраты со знаком минус)
        $totals = $ordersQ->selectRaw("
            COALESCE(SUM(
                GREATEST(0,(o.price - COALESCE(o.discount,0))) *
                CASE WHEN og.type = 'return' THEN -o.count ELSE o.count END
            ), 0) as revenue_net,

            COALESCE(SUM(
                CASE WHEN og.type = 'return' THEN -o.count ELSE o.count END
            ), 0) as items_net,

            COALESCE(SUM(
                CASE WHEN og.type = 'return'
                    THEN 0
                    ELSE COALESCE(o.discount,0) * o.count
                END
            ), 0) as discount_total
        ")->first();

        $revenueNet = (int) ($totals->revenue_net ?? 0);
        $itemsNet = (int) ($totals->items_net ?? 0);
        $discountTotal = (int) ($totals->discount_total ?? 0);

        // Кол-во чеков продаж/обменов (для среднего чека)
        $ordersCount = (int) $this->baseGroupQuery($from, $to, $locationId, $source)
            ->whereIn('og.type', ['sale', 'exchange'])
            ->count();

        // Сумма возвратов отдельно (модулем)
        $returnsAmount = (int) $this->baseGroupQuery($from, $to, $locationId, $source)
            ->where('og.type', 'return')
            ->sum('og.total');

        $avgReceipt = $ordersCount > 0 ? (int) floor($revenueNet / $ordersCount) : 0;

        return [
            'revenue' => $revenueNet,
            'orders_count' => $ordersCount,
            'items_sold' => $itemsNet,
            'avg_receipt' => $avgReceipt,
            'returns_amount' => abs($returnsAmount),
            'discount_total' => $discountTotal,
        ];
    }

    public function allSales(string $from, string $to, ?int $locationId = null, ?string $source = null): array
    {
        $dateExpr = Schema::hasColumn('order_groups', 'paid_at')
            ? "COALESCE(og.paid_at, og.created_at)"
            : "og.created_at";

        $q = DB::table('order_groups as og')
            ->leftJoin('users as cashier', 'cashier.id', '=', 'og.cashier_id')
            ->leftJoin('users as client', 'client.id', '=', 'og.user_id')
            ->join('orders as o', 'o.order_group_id', '=', 'og.id')
            ->whereRaw("LOWER(og.status) = 'success'")
            ->whereBetween(DB::raw($dateExpr), [$from, $to]);

        if ($locationId !== null) {
            $q->where('og.location_id', $locationId);
        }
        if ($source !== null) {
            $q->where('og.source', $source);
        }

        return $q->selectRaw("
            og.id,
            og.order_number,
            {$dateExpr} as paid_at,
            og.source,
            og.type,
            COALESCE(
                og.total,
                SUM(GREATEST(0, (o.price - COALESCE(o.discount,0))) *
                    CASE WHEN og.type = 'return' THEN -o.count ELSE o.count END)
            ) AS total_net,
            COUNT(o.id) as items_count,
            COALESCE(NULLIF(cashier.full_name,''), cashier.email, '—') as cashier_name,
            COALESCE(NULLIF(client.full_name,''),  client.email,  '—') as client_name
        ")
            ->groupBy(
                'og.id',
                'og.order_number',
                DB::raw($dateExpr),
                'og.source',
                'og.type',
                'og.total',
                'cashier.full_name',
                'cashier.email',
                'client.full_name',
                'client.email'
            )
            ->orderByDesc(DB::raw($dateExpr))
            ->limit(200)
            ->get()
            ->map(fn($r) => [
                'id' => (int) $r->id,
                'order_number' => (string) ($r->order_number ?? $r->id),
                'paid_at' => (string) $r->paid_at,
                'source' => (string) $r->source,
                'type' => (string) $r->type,
                'total' => (int) $r->total_net,
                'items_count' => (int) $r->items_count,
                'cashier_name' => (string) $r->cashier_name,
                'client_name' => (string) $r->client_name,
            ])
            ->toArray();
    }

    public function topProducts(string $from, string $to, int $limit = 20, ?int $locationId = null, ?string $source = null): array
    {
        $dt = $this->dateExpr();

        $q = DB::table('orders as o')
            ->join('order_groups as og', 'og.id', '=', 'o.order_group_id')
            ->join('products as p', 'p.id', '=', 'o.product_id')
            ->whereRaw('LOWER(og.status) = ?', ['success'])
            ->whereBetween($dt, [$from, $to]);

        if ($locationId !== null) {
            $q->where('og.location_id', $locationId);
        }
        if ($source !== null) {
            $q->where('og.source', $source);
        }

        $nameExpr = Schema::hasColumn('products', 'name_ru')
            ? "COALESCE(NULLIF(p.name_ru, ''), 'Товар #' || p.id::text)"
            : "('Товар #' || p.id::text)";

        $rows = $q->selectRaw("
                o.product_id,
                {$nameExpr} as name,
                p.image as image,
                SUM(CASE WHEN og.type = 'return' THEN -o.count ELSE o.count END) as qty_net,
                SUM(GREATEST(0, (o.price - COALESCE(o.discount, 0)))
                    * CASE WHEN og.type = 'return' THEN -o.count ELSE o.count END) as revenue_net
            ")
            ->groupBy('o.product_id', 'p.id', 'p.image', DB::raw($nameExpr))
            ->havingRaw("SUM(CASE WHEN og.type = 'return' THEN -o.count ELSE o.count END) > 0")
            ->orderByDesc('qty_net')
            ->limit($limit)
            ->get();

        return $rows->map(fn($r) => [
            'product_id' => (int) $r->product_id,
            'name' => (string) $r->name,
            'qty' => (int) $r->qty_net,
            'amount' => (int) $r->revenue_net,
            'image' => $r->image,
        ])->toArray();
    }

    public function byCashier(string $from, string $to, ?int $locationId = null, ?string $source = null): array
    {
        $q = $this->baseGroupQuery($from, $to, $locationId, $source)
            ->whereIn('og.type', ['sale', 'exchange'])
            ->leftJoin('users as u', 'u.id', '=', 'og.cashier_id')
            ->selectRaw("
                og.cashier_id,
                COALESCE(NULLIF(u.full_name, ''), 'ID:' || og.cashier_id::text) as cashier_name,
                COUNT(*) as orders_count,
                SUM(og.total) as revenue
            ")
            ->groupBy('og.cashier_id', 'u.full_name')
            ->orderByDesc('revenue');

        return $q->get()->map(fn($r) => [
            'cashier_id' => $r->cashier_id ? (int) $r->cashier_id : null,
            'cashier' => $r->cashier_name ?: '—',
            'receipts' => (int) $r->orders_count,
            'amount' => (int) $r->revenue,
        ])->toArray();
    }

    public function bySizeColor(string $from, string $to, ?int $locationId = null, ?string $source = null): array
    {
        $dt = $this->dateExpr();

        $q = DB::table('orders as o')
            ->join('order_groups as og', 'og.id', '=', 'o.order_group_id')
            ->leftJoin('sizes as s', 's.id', '=', 'o.size_id')
            ->leftJoin('colors as c', 'c.id', '=', 'o.color_id')
            ->whereRaw("LOWER(og.status) = 'success'")
            ->whereIn('og.type', ['sale', 'exchange'])
            ->whereBetween($dt, [$from, $to]);

        if ($locationId !== null) {
            $q->where('og.location_id', $locationId);
        }
        if ($source !== null) {
            $q->where('og.source', $source);
        }

        return $q->selectRaw("
                COALESCE(s.name, '—') as size,
                COALESCE(c.name, '—') as color,
                SUM(o.count) as qty,
                SUM(GREATEST(0,(o.price - COALESCE(o.discount,0))) * o.count) as revenue
            ")
            ->groupBy('s.name', 'c.name')
            ->orderByDesc('qty')
            ->get()
            ->map(fn($r) => [
                'size' => (string) $r->size,
                'color' => (string) $r->color,
                'qty' => (int) $r->qty,
                'amount' => (int) $r->revenue,
            ])->toArray();
    }

    /**
     * Остатки
     */
    public function stockSnapshot(?int $locationId = null): array
    {
        if (!Schema::hasTable('inventory_levels')) {
            return $this->stockFromVariants($locationId);
        }

        $nameCol = Schema::hasColumn('products', 'name_ru') ? 'p.name_ru' : 'p.name';

        $q = DB::table('inventory_levels as il')
            ->join('products as p', 'p.id', '=', 'il.product_id')
            ->leftJoin('sizes as s', 's.id', '=', 'il.size_id')
            ->selectRaw("
                il.product_id,
                {$nameCol} as name,
                COALESCE(s.name, '—') as size,
                il.qty_on_hand,
                il.qty_reserved,
                GREATEST(0, il.qty_on_hand - COALESCE(il.qty_reserved,0)) as available
            ")
            ->where('il.qty_on_hand', '>', 0);

        if ($locationId !== null) {
            $q->where('il.stock_location_id', $locationId);
        }

        return $q->orderBy(DB::raw($nameCol))
            ->orderBy('size')
            ->limit(200)
            ->get()
            ->map(fn($r) => [
                'product_id' => (int) $r->product_id,
                'name' => (string) ($r->name ?? 'Товар #' . $r->product_id),
                'size' => (string) $r->size,
                'color' => '—',
                'stock' => (int) $r->available,
            ])
            ->toArray();
    }

    /**
     * Fallback: остатки из variants если нет inventory_levels.
     */
    private function stockFromVariants(?int $locationId): array
    {
        if (!Schema::hasTable('variants')) {
            return [];
        }

        $nameCol = Schema::hasColumn('products', 'name_ru') ? 'p.name_ru' : 'p.name';

        return DB::table('variants as v')
            ->join('products as p', 'p.id', '=', 'v.product_id')
            ->selectRaw("
                p.id as product_id,
                {$nameCol} as name,
                v.attrs->>'Size'  as size,
                v.attrs->>'Color' as color,
                v.stock
            ")
            ->where('v.stock', '>', 0)
            ->orderBy(DB::raw($nameCol))
            ->limit(200)
            ->get()
            ->map(fn($r) => [
                'product_id' => (int) $r->product_id,
                'name' => (string) ($r->name ?? 'Товар #' . $r->product_id),
                'size' => (string) ($r->size ?? '—'),
                'color' => (string) ($r->color ?? '—'),
                'stock' => (int) $r->stock,
            ])
            ->toArray();
    }
}