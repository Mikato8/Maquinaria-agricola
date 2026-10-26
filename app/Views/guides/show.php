<?php
/** @var array<string, mixed> $guide */
/** @var array<int, array<string, mixed>> $related */
/** @var array<string, mixed> $jsonLd */
?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<section class="detail-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/guias')) ?>">Guías</a>
            <a href="<?= e(url('/guias?category=' . $guide['category'])) ?>"><?= e(guide_category_label((string) $guide['category'])) ?></a>
            <span aria-current="page"><?= e($guide['title']) ?></span>
        </nav>
        <span class="detail-kicker"><?= icon('book-open', 'icon icon-sm') ?> <?= e(guide_category_label((string) $guide['category'])) ?> · <?= e(level_label((string) $guide['level'])) ?></span>
        <h1><?= e($guide['title']) ?></h1>
        <p class="detail-lead"><?= e($guide['summary']) ?></p>
        <div class="byline-meta">
            <span><?= icon('user', 'icon icon-xs') ?> <?= e($guide['author']) ?></span>
            <span><?= icon('calendar', 'icon icon-xs') ?> <?= e(format_date((string) $guide['published_at'])) ?></span>
            <span><?= icon('clock', 'icon icon-xs') ?> <?= (int) $guide['reading_min'] ?> min de lectura</span>
        </div>
    </div>
</section>

<div class="container article-layout">
    <article class="article-body">
        <?= $guide['body'] ?>

        <div class="article-disclaimer">
            <?= icon('info', 'icon icon-sm') ?>
            <p>Contenido educativo. Confirma especificaciones, dosis, torques y procedimientos con el manual del fabricante y con un técnico certificado. Las condiciones de cada lote cambian el resultado.</p>
        </div>

        <div class="article-share">
            <span>Compartir:</span>
            <a href="https://wa.me/?text=<?= rawurlencode($guide['title'] . ' ' . url('/guias/' . $guide['slug'])) ?>" target="_blank" rel="noopener">WhatsApp</a>
            <a href="mailto:?subject=<?= rawurlencode($guide['title']) ?>&body=<?= rawurlencode(url('/guias/' . $guide['slug'])) ?>">Correo</a>
            <button type="button" class="link-button" data-copy="<?= e(url('/guias/' . $guide['slug'])) ?>">Copiar enlace</button>
        </div>
    </article>

    <aside class="detail-aside">
        <div class="aside-card">
            <h3>En esta guía</h3>
            <p><?= e(excerpt($guide['summary'], 140)) ?></p>
            <div class="aside-list">
                <a href="<?= e(url('/maquinas')) ?>">Ver máquinas <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/implementos')) ?>">Ver implementos <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/compatibilidad')) ?>">Calcular compatibilidad <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
            </div>
        </div>

        <?php if ($related !== []): ?>
            <div class="aside-card">
                <h3>Sigue leyendo</h3>
                <div class="aside-list">
                    <?php foreach ($related as $item): ?>
                        <a href="<?= e(url('/guias/' . $item['slug'])) ?>">
                            <span><?= e($item['title']) ?><br><small class="muted"><?= (int) $item['reading_min'] ?> min</small></span>
                            <?= icon('arrow-right', 'icon icon-xs') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="aside-card aside-card-dark">
            <h3>¿Encontraste un error?</h3>
            <p>Este portal se corrige con fuentes. Si un dato no coincide con tu manual, mándanos la página y la edición.</p>
            <a class="btn btn-sand btn-sm btn-block" href="<?= e(url('/contacto')) ?>">Reportar corrección</a>
        </div>
    </aside>
</div>
