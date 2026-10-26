<?php
/** @var array<string, mixed> $fault */
?><article class="fault-card">
    <div class="fault-card-top">
        <span class="badge badge-<?= e(severity_tone($fault['severity'])) ?>"><?= e(severity_label($fault['severity'])) ?></span>
        <span class="fault-meta"><?= e(machine_category_singular($fault['machine_category'])) ?></span>
    </div>
    <h3><a href="<?= e(url('/fallas/' . $fault['slug'])) ?>"><?= e($fault['title']) ?></a></h3>
    <p><?= e(excerpt($fault['symptom'], 120)) ?></p>
    <div class="fault-meta">
        <span><?= e(fault_system_label($fault['system'])) ?></span>
        <span><?= e(difficulty_short($fault['difficulty'] !== null ? (int) $fault['difficulty'] : null)) ?></span>
        <?php if (!empty($fault['time_hours'])): ?>
            <span><?= e(hours_label($fault['time_hours'])) ?></span>
        <?php endif; ?>
        <span><?= e(frequency_label($fault['frequency'])) ?></span>
    </div>
</article>
