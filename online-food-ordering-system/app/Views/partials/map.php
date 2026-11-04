<?php
/**
 * Demo city map (SVG, no third-party tiles). Coordinates are a 0-100 grid.
 * Needs: $origin ['x','y'], $dest ['x','y'], $position ['x','y'], $mapId
 */
$roadsX = [12, 18, 30, 52, 70, 82, 88];
$roadsY = [18, 22, 48, 52, 82];
?>
<svg class="fd-map" id="<?= e($mapId) ?>" viewBox="0 0 100 100" role="img" aria-label="Delivery map"
     data-ox="<?= (int) $origin['x'] ?>" data-oy="<?= (int) $origin['y'] ?>" data-dx="<?= (int) $dest['x'] ?>" data-dy="<?= (int) $dest['y'] ?>">
    <rect width="100" height="100" class="map-ground"/>
    <path d="M0 62 C 20 56, 28 70, 50 64 S 84 58, 100 66 L100 72 C 84 64, 60 72, 50 70 S 20 62, 0 68 Z" class="map-river"/>
    <rect x="34" y="26" width="14" height="16" rx="2" class="map-park"/>
    <rect x="60" y="56" width="18" height="12" rx="2" class="map-park"/>
    <?php foreach ([[14,28],[20,52],[56,28],[74,22],[58,88],[36,86],[92,60]] as [$bx, $by]): ?>
        <rect x="<?= $bx ?>" y="<?= $by ?>" width="8" height="7" rx="1.2" class="map-block"/>
    <?php endforeach; ?>
    <?php foreach ($roadsX as $x): ?><line x1="<?= $x ?>" y1="0" x2="<?= $x ?>" y2="100" class="map-road"/><?php endforeach; ?>
    <?php foreach ($roadsY as $y): ?><line x1="0" y1="<?= $y ?>" x2="100" y2="<?= $y ?>" class="map-road"/><?php endforeach; ?>

    <!-- The route: along the kitchen's street, then up/down the destination's street. -->
    <polyline class="map-route" points="<?= (int) $origin['x'] ?>,<?= (int) $origin['y'] ?> <?= (int) $dest['x'] ?>,<?= (int) $origin['y'] ?> <?= (int) $dest['x'] ?>,<?= (int) $dest['y'] ?>"/>

    <g transform="translate(<?= (int) $origin['x'] ?> <?= (int) $origin['y'] ?>)"><circle r="4.2" class="map-kitchen"/><text class="map-emoji" y="1.8">🍳</text></g>
    <g transform="translate(<?= (int) $dest['x'] ?> <?= (int) $dest['y'] ?>)"><circle r="4.2" class="map-home"/><text class="map-emoji" y="1.8">🏠</text></g>
    <g class="map-rider" data-rider style="transform: translate(<?= (float) $position['x'] ?>px, <?= (float) $position['y'] ?>px)">
        <circle r="5" class="map-rider-halo"/><circle r="4" class="map-rider-dot"/><text class="map-emoji" y="1.8">🛵</text>
    </g>
</svg>
