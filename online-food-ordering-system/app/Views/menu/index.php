<section class="fd-hero">
    <div class="container">
        <div class="row valign-wrapper">
            <div class="col s12 m7">
                <span class="fd-pill">Free delivery tracking</span>
                <h2>Hungry? Order now and<br>watch your rider <span class="fd-hl">live</span>.</h2>
                <p>Fresh burgers, pizza and rice meals from our kitchen to your door. Follow every step on the map.</p>
                <form action="<?= url('/') ?>" method="get" class="fd-search">
                    <i class="material-icons">search</i>
                    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search burgers, pizza, adobo...">
                    <?php if ($activeCategory): ?><input type="hidden" name="category" value="<?= $activeCategory ?>"><?php endif; ?>
                    <button class="btn fd-coral waves-effect" type="submit">Search</button>
                </form>
            </div>
            <div class="col m5 hide-on-small-only center-align fd-hero-art">🍔🍕🍛</div>
        </div>
    </div>
</section>

<div class="container fd-menu">
    <div class="fd-chips">
        <a href="<?= url('/') ?>" class="fd-chip <?= !$activeCategory ? 'active' : '' ?>"><i class="material-icons">restaurant_menu</i>All</a>
        <?php foreach ($categories as $category): ?>
            <a href="<?= url('/?category=' . $category['id']) ?>" class="fd-chip <?= $activeCategory === (int) $category['id'] ? 'active' : '' ?>">
                <i class="material-icons"><?= e($category['icon']) ?></i><?= e($category['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (!$products): ?>
        <div class="fd-empty"><i class="material-icons">search_off</i><p>No dishes match your search.</p><a href="<?= url('/') ?>" class="btn fd-coral">See the full menu</a></div>
    <?php endif; ?>

    <div class="row fd-grid">
        <?php foreach ($products as $product): ?>
            <div class="col s12 m6 l4">
                <article class="card fd-card">
                    <div class="fd-thumb">
                        <?php if ($product['image']): ?>
                            <img src="<?= url($product['image']) ?>" alt="<?= e($product['name']) ?>">
                        <?php else: ?>
                            <span class="fd-emoji"><?= e($product['emoji']) ?></span>
                        <?php endif; ?>
                        <span class="fd-tag"><?= e($product['category']) ?></span>
                    </div>
                    <div class="card-content">
                        <h3><?= e($product['name']) ?></h3>
                        <p><?= e($product['description']) ?></p>
                    </div>
                    <div class="fd-card-foot">
                        <strong class="fd-price"><?= money($product['price']) ?></strong>
                        <button class="btn-floating btn-small waves-effect fd-coral" data-add-to-cart="<?= (int) $product['id'] ?>" aria-label="Add <?= e($product['name']) ?> to cart">
                            <i class="material-icons">add</i>
                        </button>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</div>
