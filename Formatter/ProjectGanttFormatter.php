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
                    "(SELECT count(*) FROM tasks WHERE tasks.project_id=projects.id) AS nb_tasks"
                )
                ->eq('projects.id', $project_['id'])
                ->join('users', 'id', 'owner_id')
                ->join('tasks', 'project_id', 'id')
                ->findOne();
            
            $start = empty($project['start_date']) ? time() : strtotime($project['start_date']);
            $end = empty($project['end_date']) ? $start : strtotime($project['end_date']);
            if($start - $end > 0) {
                $start = $end;
            }
            $color = next($colors) ?: reset($colors);

            $bars[] = array(
                'id' => $project['id'],
                'name' => $project['name'],
                'start' => date('Y-m-d', $start),
                'end' => date('Y-m-d', $end),
                'progress' => ((!empty($project['nb_tasks'])) ? round($project['nb_closed_tasks'] / $project['nb_tasks'] * 100, 0) : '0'),
                'popup' => '<div class="title">' . $project['name'] . '<br>' . $project['owner_name'] . '</div><div class="subtitle"><strong>' . t("Start") . ':</strong> ' . date(t("Y-m-d"), $start) . '<br><strong>' . t("End") . ':</strong> ' . date(t("Y-m-d"), $end) . '<br>' . t("%s%% completed", ((!empty($project['nb_tasks'])) ? round($project['nb_closed_tasks'] / $project['nb_tasks'] * 100, 0) : '0')) . '<br><br><span class="button">' . $this->helper->url->link(t('Show'), 'BoardViewController', 'show', array('project_id' => $project['id'])) . '</span>&nbsp;<span class="button">' . $this->helper->url->link(t('Gantt'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'plugin' => 'Gantt')) . '</span></div>',
            );
        }

        return $bars;
    }
}
