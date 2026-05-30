<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Task;
use App\Models\Project;
use Core\Http\Request;
use Core\Http\Response;
use Core\View\Engine;

class TaskController
{
    public function __construct(
        private readonly Task    $tasks,
        private readonly Project $projects,
        private readonly Engine  $view
    ) {}

    public function index(): Response
    {
        $tasks    = $this->tasks->all();
        $projects = $this->projects->all();

        return new Response($this->view->render('tasks/index', [
            'tasks'        => $tasks,
            'projectsById' => array_column($projects, null, 'id'),
        ]));
    }

    public function create(): Response
    {
        return new Response($this->view->render('tasks/create', [
            'errors'   => [],
            'old'      => [],
            'projects' => $this->projects->all(),
        ]));
    }

    public function store(Request $request): Response
    {
        $title        = trim($request->post['title']    ?? '');
        $dueDate      = trim($request->post['due_date'] ?? '');
        $rawProjectId = $request->post['project_id']    ?? '';
        $projectId    = $rawProjectId !== '' ? (int) $rawProjectId : null;

        $errors = $this->validateCreate($title, $dueDate, $projectId);

        if (!empty($errors)) {
            return new Response($this->view->render('tasks/create', [
                'errors'   => $errors,
                'old'      => [
                    'title'      => $request->post['title']    ?? '',
                    'due_date'   => $dueDate,
                    'project_id' => $rawProjectId,
                ],
                'projects' => $this->projects->all(),
            ]), 422);
        }

        $this->tasks->save([
            'title'      => $title,
            'status'     => 'pending',
            'due_date'   => $dueDate,
            'project_id' => $projectId,
        ]);

        return (new Response())->setHeader('Location', '/tasks')->setStatusCode(302);
    }

    public function edit(string $id): Response
    {
        $task = $this->tasks->find((int) $id);

        if (!$task) {
            return new Response($this->view->render('errors/404', [
                'message' => "Task #{$id} was not found.",
            ]), 404);
        }

        return new Response($this->view->render('tasks/edit', [
            'task'     => $task,
            'errors'   => [],
            'projects' => $this->projects->all(),
        ]));
    }

    public function update(Request $request, string $id): Response
    {
        $task = $this->tasks->find((int) $id);

        if (!$task) {
            return new Response($this->view->render('errors/404', [
                'message' => "Task #{$id} was not found.",
            ]), 404);
        }

        $title        = trim($request->post['title']    ?? '');
        $statusValue  = $request->post['status']        ?? '';
        $dueDate      = trim($request->post['due_date'] ?? '');
        $rawProjectId = $request->post['project_id']    ?? '';
        $projectId    = $rawProjectId !== '' ? (int) $rawProjectId : null;

        $errors = $this->validateUpdate($title, $statusValue, $dueDate, $projectId);

        if (!empty($errors)) {
            return new Response($this->view->render('tasks/edit', [
                'task'   => array_merge($task, [
                    'title'      => $request->post['title'] ?? '',
                    'status'     => $statusValue,
                    'due_date'   => $dueDate,
                    'project_id' => $rawProjectId,
                ]),
                'errors'   => $errors,
                'projects' => $this->projects->all(),
            ]), 422);
        }

        $this->tasks->update((int) $id, [
            'title'      => $title,
            'status'     => $statusValue,
            'due_date'   => $dueDate !== '' ? $dueDate : null,
            'project_id' => $projectId,
        ]);

        return (new Response())->setHeader('Location', '/tasks')->setStatusCode(302);
    }

    public function delete(string $id): Response
    {
        $this->tasks->delete((int) $id);
        return (new Response())->setHeader('Location', '/tasks')->setStatusCode(302);
    }

    private function validateCreate(string $title, string $dueDate, ?int $projectId): array
    {
        $errors = [];

        if ($title === '') {
            $errors['title'] = 'Title is required.';
        } elseif (strlen($title) > 255) {
            $errors['title'] = 'Title must not exceed 255 characters.';
        } elseif ($this->tasks->titleExistsInProgress($title)) {
            $errors['title'] = 'A task with this title is already in progress.';
        }

        if ($projectId === null) {
            $errors['project_id'] = 'Project must be selected.';
        }

        if ($dueDate === '') {
            $errors['due_date'] = 'Due date is required.';
        } else {
            $dt = \DateTime::createFromFormat('Y-m-d', $dueDate);
            if ($dt === false || $dt->format('Y-m-d') !== $dueDate) {
                $errors['due_date'] = 'Invalid date format (YYYY-MM-DD expected).';
            } elseif ($projectId !== null && !isset($errors['project_id'])) {
                $errors = array_merge($errors, $this->validateDueDateInProjectRange($dueDate, $projectId));
            }
        }

        return $errors;
    }

    private function validateUpdate(string $title, string $statusValue, string $dueDate, ?int $projectId): array
    {
        $errors = [];

        if ($title === '') {
            $errors['title'] = 'Title is required.';
        } elseif (strlen($title) > 255) {
            $errors['title'] = 'Title must not exceed 255 characters.';
        }

        if (!in_array($statusValue, ['pending', 'in_progress', 'completed'], strict: true)) {
            $errors['status'] = 'Invalid status value.';
        }

        if ($projectId === null) {
            $errors['project_id'] = 'Project must be selected.';
        }

        if ($dueDate === '') {
            $errors['due_date'] = 'Due date is required.';
        } else {
            $dt = \DateTime::createFromFormat('Y-m-d', $dueDate);
            if ($dt === false || $dt->format('Y-m-d') !== $dueDate) {
                $errors['due_date'] = 'Invalid date format (YYYY-MM-DD expected).';
            } elseif ($projectId !== null && !isset($errors['project_id'])) {
                $errors = array_merge($errors, $this->validateDueDateInProjectRange($dueDate, $projectId));
            }
        }

        return $errors;
    }

    private function validateDueDateInProjectRange(string $dueDate, int $projectId): array
    {
        $project = $this->projects->find($projectId);

        if (!$project) {
            return ['project_id' => 'Selected project does not exist.'];
        }

        $due   = new \DateTime($dueDate);
        $start = $project['start_date'] ? new \DateTime($project['start_date']) : null;
        $end   = $project['end_date']   ? new \DateTime($project['end_date'])   : null;

        if ($start && $due < $start) {
            return ['due_date' => "Due date must not be before the project start date ({$project['start_date']})."];
        }

        if ($end && $due > $end) {
            return ['due_date' => "Due date must not be after the project end date ({$project['end_date']})."];
        }

        return [];
    }
}
