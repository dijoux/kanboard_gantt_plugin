<?php if ($this->user->hasProjectAccess('TaskGanttController', 'show', $project['id'])): ?><li <?= $this->app->checkMenuSelection('TaskGanttController') ?>>
<?= $this->url->icon('sliders', t('Gantt'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'search' => $filters['search'], 'plugin' => 'Gantt'), false, 'view-gantt', t('Keyboard shortcut: "%s"', 'v g')) ?>
</li><li><?= $this->url->icon('sliders', t('Projects Gantt chart'), 'ProjectGanttController', 'show', array('plugin' => 'Gantt'), false, 'view-all-gantt', t('Keyboard shortcut: "%s"', 'g g')) ?></li><?php endif ?>
