<?php
/** The live map card used by the customer tracking page. Needs $tracking. */
$mapId = 'live-map';
$origin = $tracking['origin'];
$dest = $tracking['dest'];
$position = $tracking['position'];
?>
<div class="fd-map-wrap">
    <?php require ROOT . '/app/Views/partials/map.php'; ?>
    <div class="fd-map-legend">
        <span><i class="material-icons tiny">restaurant</i> Kitchen</span>
        <span><i class="material-icons tiny">two_wheeler</i> Rider</span>
        <span><i class="material-icons tiny">home</i> You</span>
    </div>
</div>
