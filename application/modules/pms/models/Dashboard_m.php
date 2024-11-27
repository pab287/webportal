<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard_m extends CI_Model {
        public function __construct() {
            parent::__construct();
        }

        function getUnitTaskDemographics() {
            $this->db->select("prj_unit.*");
            $this->db->where("prj_unit.is_active", 1);
            $this->db->where("prj_unit.`status`", 1);
            $this->db->where_in("prj_unit.sf_status", [0, 1]);
            $this->db->join("gccpms.sf_task task", "task.unit_id = prj_unit.id", "inner");
            $this->db->group_by("prj_unit.id");
            $project_units = $this->db->get("gccpms.sf_project_unit prj_unit")->result();

            foreach ($project_units as $project_unit) {
                $unit_id = $project_unit->id;
                $project_unit->awaiting = $this->getAwaiting($unit_id);
                $project_unit->inProgress = $this->getInProgress($unit_id);
                $project_unit->deferred = $this->getDeferred($unit_id);
                $project_unit->backlogs = $this->getBackLogs($unit_id);
            }

            return array(
                "status_based" => $project_units,
                "goals" => array(
                    "weekly" => $this->weeklyTaskGoals(),
                    "monthly" => $this->monthlyGoals(),
                )
            );
        }

        private function weeklyTaskGoals() {
            $today = date('Y-m-d'); // date('2020-07-30');
            $this->db->select("prj_unit.id, prj_unit.code, prj_unit.description, task.unit_id, task.contract_id,
                               IF(contract.extension_id = 0, contract.due_date, ext.extension_date) due_date,
                               DATE_SUB(IF(contract.extension_id = 0, contract.due_date, ext.extension_date), INTERVAL 7 DAY) show_date,
                               WEEKOFYEAR(IF(contract.extension_id = 0, contract.due_date, ext.extension_date)) due_date_week,
                               WEEKOFYEAR('$today') current_week,
                               COUNT(task.id) task_count");
            $this->db->join("gccpms.sf_task task", "task.id = task_ln.parent_id", "INNER");
            $this->db->join("gccpms.sf_project_unit prj_unit", "prj_unit.id = task.unit_id", "INNER");
            $this->db->join("gccpms.sf_item item", "task_ln.task_id = item.id", "INNER");
            $this->db->join("gccpms.sf_contract contract", "contract.id = task.contract_id", "INNER");
            $this->db->join("gccpms.sf_contract_extension ext", "ext.contract_id = contract.extension_id", "LEFT");
            $this->db->where_in("task_ln.task_status", [1, 2]); // count only 1=inprogress and 2=deferred
            $this->db->where("'$today' >= DATE_SUB(IF(contract.extension_id = 0, contract.due_date, ext.extension_date), INTERVAL 7 DAY)", NULL, FALSE);
            $this->db->where("'$today' <= IF(contract.extension_id = 0, contract.due_date, ext.extension_date)", NULL, FALSE);
            $this->db->group_by("prj_unit.id");
            $this->db->order_by("IF(contract.extension_id = 0, contract.due_date, ext.extension_date) ASC", NULL, FALSE);
            $query = $this->db->get("gccpms.sf_task_timeline task_ln");
            return $query->result();
        }

        private function monthlyGoals() {
            $today = date('Y-m-d'); // date('2020-07-30');
            $this->db->select("prj_unit.id, prj_unit.code, prj_unit.description, task.unit_id, task.contract_id,
                               IF(contract.extension_id = 0, contract.due_date, ext.extension_date) due_date,
                               DATE_SUB(IF(contract.extension_id = 0, contract.due_date, ext.extension_date), INTERVAL 7 DAY) show_date,
                               WEEKOFYEAR(IF(contract.extension_id = 0, contract.due_date, ext.extension_date)) due_date_week,
                               WEEKOFYEAR('$today') current_week,
                               COUNT(task.id) task_count");
            $this->db->join("gccpms.sf_task task", "task.id = task_ln.parent_id", "INNER");
            $this->db->join("gccpms.sf_project_unit prj_unit", "prj_unit.id = task.unit_id", "INNER");
            $this->db->join("gccpms.sf_item item", "task_ln.task_id = item.id", "INNER");
            $this->db->join("gccpms.sf_contract contract", "contract.id = task.contract_id", "INNER");
            $this->db->join("gccpms.sf_contract_extension ext", "ext.contract_id = contract.extension_id", "LEFT");
            $this->db->where_in("task_ln.task_status", [1, 2]); // count only 1=inprogress and 2=deferred
            $this->db->where("'$today' >= DATE_SUB(IF(contract.extension_id = 0, contract.due_date, ext.extension_date), INTERVAL 7 DAY)", NULL, FALSE);
            $this->db->where("MONTH(IF(contract.extension_id = 0, contract.due_date, ext.extension_date)) >= MONTH('$today')", NULL, FALSE);
            $this->db->group_by("prj_unit.id");
            $this->db->order_by("IF(contract.extension_id = 0, contract.due_date, ext.extension_date) ASC", NULL, FALSE);
            $query = $this->db->get("gccpms.sf_task_timeline task_ln");
            return $query->result();
        }

        /* CODE REDUNDANCY PURPOSE:
        * FOR FUTURE CHANGES PER STATUS */

        private function getAwaiting($unit_id) {
            $this->db->where("task_timeline.task_status", 0);
            $this->db->where("task.unit_id", $unit_id);
            $this->db->join("gccpms.sf_task task", "task.id = task_timeline.parent_id", "INNER");
            $this->db->join("gccpms.sf_item item", "item.id = task_timeline.task_id", "INNER");
            $this->db->join("gccpms.sf_contract contract", "contract.id = task_timeline.contract_id", "INNER");
            $this->db->join("gccpms.sf_contract_extension contract_ext", "contract_ext.contract_id = contract.id", "LEFT");
            return $this->db->count_all_results("gccpms.sf_task_timeline task_timeline");
        }

        private function getInProgress($unit_id) {
            $this->db->where("task_timeline.task_status", 1);
            $this->db->where("task.unit_id", $unit_id);
            $this->db->join("gccpms.sf_task task", "task.id = task_timeline.parent_id", "INNER");
            $this->db->join("gccpms.sf_item item", "item.id = task_timeline.task_id", "INNER");
            $this->db->join("gccpms.sf_contract contract", "contract.id = task_timeline.contract_id", "INNER");
            $this->db->join("gccpms.sf_contract_extension contract_ext", "contract_ext.contract_id = contract.id", "LEFT");
            return $this->db->count_all_results("gccpms.sf_task_timeline task_timeline");
        }

        private function getDeferred($unit_id) {
            $this->db->where("task_timeline.task_status", 2);
            $this->db->where("task.unit_id", $unit_id);
            $this->db->join("gccpms.sf_task task", "task.id = task_timeline.parent_id", "INNER");
            $this->db->join("gccpms.sf_item item", "item.id = task_timeline.task_id", "INNER");
            $this->db->join("gccpms.sf_contract contract", "contract.id = task_timeline.contract_id", "INNER");
            $this->db->join("gccpms.sf_contract_extension contract_ext", "contract_ext.contract_id = contract.id", "LEFT");
            return $this->db->count_all_results("gccpms.sf_task_timeline task_timeline");
        }

        private function getBackLogs($unit_id) {
            $this->db->where("CURDATE() >= (IF(contract_ext.id IS NULL, contract.due_date, contract_ext.extension_date))", NULL, FALSE);
            $this->db->where("task.unit_id", $unit_id);
            $this->db->join("gccpms.sf_task task", "task.id = task_timeline.parent_id", "INNER");
            $this->db->join("gccpms.sf_item item", "item.id = task_timeline.task_id", "INNER");
            $this->db->join("gccpms.sf_contract contract", "contract.id = task_timeline.contract_id", "INNER");
            $this->db->join("gccpms.sf_contract_extension contract_ext", "contract_ext.contract_id = contract.id", "LEFT");
            return $this->db->count_all_results("gccpms.sf_task_timeline task_timeline");
        }

        function getProjectUnitCode($unit_id) {
            $this->db->select("task_timeline.*, item.label, contract.due_date, contract_ext.id extension_id,
                               contract_ext.extension_date, contract.`status` contract_status, 
                               task_timeline.task_status `status`, item_m.label AS parent, task.task_id tid");

            $this->db->join("gccpms.sf_task task", "task.id = task_timeline.parent_id", "INNER");
            $this->db->join("gccpms.sf_contract contract", "contract.id = task_timeline.contract_id", "INNER");
            $this->db->join("gccpms.sf_contract_extension contract_ext", "contract_ext ON contract_ext.contract_id = contract.id", "LEFT");
            $this->db->join("gccpms.sf_item item", "item.id = task_timeline.task_id", "INNER");
            $this->db->join("gccpms.sf_item item_m", "item_m.id = task.task_id ", "INNER");
            $this->db->where("task.unit_id", $unit_id);
            $this->db->where_in("task_timeline.task_status", [0, 1, 2]);

            $q = $this->db->get("gccpms.sf_task_timeline task_timeline");
            $tasks = $q->result();

            $parents = array();

            foreach ($tasks as $task) {
                $parent = $task->parent;
                if (in_array($parent, $parents)) continue;
                array_push($parents, $parent);
            }

            $awaitingTasks = array_filter($tasks, function ($task) {
                return intval($task->status) === 0;
            });

            $inProgressTasks = array_filter($tasks, function ($task) {
                return intval($task->status) === 1;
            });

            $deferredTasks = array_filter($tasks, function ($task) {
                return intval($task->status) === 2;
            });

            $backLogTasks = array_filter($tasks, function ($task) {
                $due_date = ($task->extension_id === NULL) ? date("Y-m-d", strtotime($task->due_date)) : date("Y-m-d", strtotime($task->extension_date));
                return date("Y-m-d") > $due_date;
            });

            $arrays = array(
                "awaiting" => empty($awaitingTasks) ? array() : $this->arrangeTasks($awaitingTasks, $parents),
                "inProgress" => empty($inProgressTasks) ? array() : $this->arrangeTasks($inProgressTasks, $parents),
                "deferred" => empty($deferredTasks) ? array() : $this->arrangeTasks($deferredTasks, $parents),
                "backLogs" => empty($backLogTasks) ? array() : $this->arrangeTasks($backLogTasks, $parents),
            );

            $todos = json_decode(json_encode($arrays));

            $result = array(
                "data" => $todos,
                "sql" => $this->db->last_query()
            );
            return $result;
        }

        private function arrangeTasks($tasks, $parents) {
            $data = array();
            foreach ($parents as $parent) {
                $_tasks = array_filter($tasks, function ($task) use ($parent) {
                    return $task->parent === $parent;
                });

                array_push($data, array("parent" => $parent, "children" => !empty($_tasks) ? $_tasks : []));
            }

            return $data;
        }

        function plotTodoList($form) {
            $id = $form["unit_id"];
            $project_unit = $this->db->get_where("gccpms.sf_project_unit", array("id" => $id))->row();
            $todos = $this->getProjectUnitCode($id);

            return array(
                "todos" => $todos["data"],
                "sql" => $todos["sql"],
                "details" => $project_unit
            );
        }
    }

    /* End of file .php */
