<?php
/** @var array<string, mixed> $profile */
/** @var array<int, array<string, mixed>> $cases */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <div class="public-profile">
            <?= avatar_tag((string) $profile['name'], $profile['avatar_url'] ?? null, 'avatar-xl') ?>
            <div>
                <span class="eyebrow"><?= e(role_label((string) $profile['role'])) ?><?= $profile['workshop_name'] ? ' · ' . e($profile['workshop_name']) : '' ?></span>
                <h1><?= e($profile['name']) ?></h1>
                <p class="page-hero-lead"><?= e($profile['headline'] ?: 'Técnico de maquinaria agrícola') ?></p>
                <div class="byline-meta">
                    <span><?= icon('map-pin', 'icon icon-xs') ?> <?= e(location_label($profile['city'] ?? null, $profile['state_code'] ?? null)) ?></span>
                    <span><?= icon('wrench', 'icon icon-xs') ?> <?= (int) ($profile['cases_count'] ?? 0) ?> casos publicados</span>
                    <span><?= icon('eye', 'icon icon-xs') ?> <?= (int) ($profile['views_total'] ?? 0) ?> lecturas</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container profile-public-grid">
        <aside class="detail-aside">
            <div class="aside-card">
                <h3>Semblanza</h3>
                <p><?= e($profile['bio'] ?: 'Sin semblanza pública.') ?></p>
            </div>
            <?php if ((string) $profile['workshop_name'] !== ''): ?>
                <div class="aside-card">
                    <h3>Taller</h3>
                    <p><strong><?= e($profile['workshop_name']) ?></strong></p>
                    <?php if ((string) $profile['phone'] !== ''): ?><p><?= icon('message-circle', 'icon icon-xs') ?> <?= e($profile['phone']) ?></p><?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="aside-card">
                <h3>Cómo se verificó</h3>
                <p><?= e(verification_label((string) $profile['verification_method'])) ?>. La verificación acredita identidad, no avala técnicamente cada reparación publicada.</p>
            </div>
        </aside>

        <div>
            <div class="section-head">
                <div>
                    <span class="eyebrow">Casos publicados</span>
                    <h2 class="section-title-sm">Reparaciones documentadas</h2>
                </div>
                <a class="text-link" href="<?= e(url('/taller')) ?>">Ver todo el taller <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
            <?php if ($cases === []): ?>
                <div class="empty-state">
                    <span class="empty-icon"><?= icon('wrench') ?></span>
                    <h2>Sin casos publicados</h2>
                    <p>Cuando este técnico publique una reparación aparecerá aquí.</p>
                </div>
            <?php else: ?>
                <div class="workshop-grid">
                    <?php foreach ($cases as $case): ?>
                        <?php \App\Core\View::partial('partials/workshop-card', ['case' => $case]); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
