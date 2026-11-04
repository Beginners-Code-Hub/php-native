<?php $actions = '<button class="btn fd-coral waves-effect" onclick="window.print()"><i class="material-icons left">print</i>Print report</button>'; ?>
<div class="card fd-panel-card fd-no-print">
    <div class="card-content">
        <form method="get" action="<?= url('/admin/reports') ?>" class="fd-filter">
            <div class="input-field"><input id="from" name="from" type="date" value="<?= e($from) ?>"><label for="from" class="active">From</label></div>
            <div class="input-field"><input id="to" name="to" type="date" value="<?= e($to) ?>"><label for="to" class="active">To</label></div>
            <button class="btn waves-effect fd-coral" type="submit">Show</button>
        </form>
    </div>
</div>

<div class="fd-print-title">FoodDash sales report · <?= e($from) ?> to <?= e($to) ?></div>

<div class="row fd-stats">
    <div class="col s6"><div class="fd-stat c2"><i class="material-icons">payments</i><span>Delivered sales</span><strong><?= money($totalSales) ?></strong></div></div>
    <div class="col s6"><div class="fd-stat c1"><i class="material-icons">receipt_long</i><span>Delivered orders</span><strong><?= (int) $totalOrders ?></strong></div></div>
</div>

<div class="row">
    <div class="col s12 l7">
        <div class="card fd-panel-card"><div class="card-content">
            <span class="card-title">Sales per day</span>
            <table class="striped">
                <thead><tr><th>Date</th><th>Orders</th><th class="right-align">Sales</th></tr></thead>
                <tbody>
                <?php foreach ($daily as $row): ?>
                    <tr><td><?= date('D, M j', strtotime($row['day'])) ?></td><td><?= (int) $row['orders'] ?></td><td class="right-align"><?= money($row['sales']) ?></td></tr>
                <?php endforeach; ?>
                <?php if (!$daily): ?><tr><td colspan="3" class="grey-text">No delivered orders in this range.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
    <div class="col s12 l5">
        <div class="card fd-panel-card"><div class="card-content">
            <span class="card-title">Top dishes</span>
            <table class="striped">
                <thead><tr><th>Dish</th><th>Sold</th><th class="right-align">Revenue</th></tr></thead>
                <tbody>
                <?php foreach ($top as $row): ?>
                    <tr><td><?= e($row['name']) ?></td><td><?= (int) $row['qty'] ?></td><td class="right-align"><?= money($row['revenue']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>
