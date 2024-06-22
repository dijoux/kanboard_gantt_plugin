<?php

namespace Kanboard\Plugin\Gantt\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Formatter\BaseFormatter;

/**
 * Gantt chart formatter for projects
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class ProjectGanttFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Format projects to be displayed in the Gantt chart
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $projects = $this->query->findAll();
        $colors = $this->colorModel->getDefaultColors();
        $bars = array();

        foreach ($projects as $project_) {
            $project = $this->db->table('projects')
                ->columns(
                    'projects.*',
                    'users.username AS owner_username',
                    'users.name AS owner_name',
                    "(SELECT count(*) FROM tasks WHERE tasks.project_id=projects.id AND tasks.is_active='1') AS nb_active_tasks",
                    "(SELECT count(*) FROM tasks WHERE tasks.project_id=projects.id AND tasks.is_active='0') AS nb_closed_tasks",
                    "(SELECT count(*) FROM tasks WHERE tasks.project_id=projects.id) AS nb_tasks",
                    "(SELECT min(date_started) FROM tasks WHERE tasks.project_id=projects.id AND date_started>0) AS start_date_tasks",
                    "(SELECT max(date_due) FROM tasks WHERE tasks.project_id=projects.id) AS end_date_tasks"
                )
                ->eq('projects.id', $project_['id'])
                ->join('users', 'id', 'owner_id')
                ->join('tasks', 'project_id', 'id')
                ->findOne();
            
            $now = time();
            $start = empty($project['start_date']) ? ($project['start_date_tasks'] ?: $now) : strtotime($project['start_date']);
            $end = empty($project['end_date']) ? ($project['end_date_tasks'] ?: $now) : strtotime($project['end_date']);
            if($start - $end > 0 && $start == $now) {
                $start = $end;
            } elseif($start - $end > 0 && $end == $now) {
                $end = $start;
            }
            $color = next($colors) ?: reset($colors);

            $bars[] = array(
                'id' => $project['id'],
                'name' => $project['name'],
                'start' => date('Y-m-d', $start),
                'end' => date('Y-m-d', $end),
                'progress' => ((!empty($project['nb_tasks'])) ? round($project['nb_closed_tasks'] / $project['nb_tasks'] * 100, 0) : '0'),
                'popup' => '<div class="title">' . $project['name'] . '<br>' . $project['owner_name'] . '</div><div class="subtitle"><strong>' . t("Start") . ':</strong> ' . date(t("Y-m-d"), $start) . '<br><strong>' . t("End") . ':</strong> ' . date(t("Y-m-d"), $end) . '<br>' . t("%s%% completed", ((!empty($project['nb_tasks'])) ? round($project['nb_closed_tasks'] / $project['nb_tasks'] * 100, 0) : '0')) . '<br><br><span class="button">' . $this->helper->url->link('<i class="fa fa-fw fa-th"></i> ' . t('Board'), 'BoardViewController', 'show', array('project_id' => $project['id'])) . '</span>&nbsp;<span class="button">' . $this->helper->url->link('<i class="fa fa-fw fa-sliders"></i> ' . t('Gantt'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'plugin' => 'Gantt')) . '</span></div>',
            );
        }

        return $bars;
    }
}
