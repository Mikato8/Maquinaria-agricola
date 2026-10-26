<?php
/** @var array<string, mixed> $case */
?><article class="workshop-card">
    <div class="workshop-cover">
        <?php if (!empty($case['cover_url'])): ?>
            <img src="<?= e($case['cover_url']) ?>" alt="Foto del caso: <?= e($case['title']) ?>" loading="lazy">
        <?php else: ?>
            <?= e(machine_category_emoji((string) $case['machine_category'])) ?>
        <?php endif; ?>
        <span class="workshop-cover-tag"><?= e(machine_category_singular($case['machine_category'])) ?></span>
    </div>
    <div class="workshop-body">
        <div class="workshop-footer-meta">
            <span class="badge badge-<?= e(outcome_tone($case['outcome'])) ?>"><?= e(outcome_label($case['outcome'])) ?></span>
            <?php if (!empty($case['machine_label'])): ?>
                <span><?= e($case['machine_label']) ?></span>
            <?php endif; ?>
        </div>
        <h3><a href="<?= e(url('/taller/' . $case['slug'])) ?>"><?= e($case['title']) ?></a></h3>
        <p><?= e(excerpt($case['summary'] ?: $case['symptom'], 130)) ?></p>
        <div class="workshop-byline">
            <?= avatar_tag((string) $case['author_name'], $case['author_avatar'] ?? null, 'avatar-xs') ?>
            <span><strong><?= e($case['author_name']) ?></strong><?php if (!empty($case['author_workshop'])): ?> · <?= e($case['author_workshop']) ?><?php endif; ?></span>
        </div>
        <div class="workshop-footer-meta">
            <span><?= icon('message-circle', 'icon icon-xs') ?> <?= (int) ($case['comments_count'] ?? 0) ?></span>
            <span><?= icon('heart-pulse', 'icon icon-xs') ?> <?= (int) ($case['reactions_count'] ?? 0) ?></span>
            <?php if (!empty($case['cost_mxn'])): ?>
                <span><?= e(compact_money($case['cost_mxn'])) ?> de refacción</span>
            <?php endif; ?>
            <span><?= e(time_ago($case['published_at'] ?? $case['created_at'])) ?></span>
        </div>
    </div>
</article>
