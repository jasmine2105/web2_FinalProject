<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Project;
use App\Models\Task;
use Core\Http\Request;
use Core\Http\Response;
use Core\View\Engine;

class ProjectController
{
    public function __construct(
        private readonly Project $projects,
        private readonly Task    $tasks,
        private readonly Engine  $view
    ) {}

    public function index(): Response
    {
        $projects = $this->projects->all();

        return new Response($this->view->render('projects/index', ['projects' => $projects]));
    }

    public function create(): Response
    {
        return new Response($this->view->render('projects/create', [
            'errors' => [],
            'old'    => [],
        ]));
    }

    public function store(Request $request): Response
    {
        $name        = trim($request->post['name']        ?? '');
        $description = trim($request->post['description'] ?? '');
        $startDate   = trim($request->post['start_date']  ?? '');
        $endDate     = trim($request->post['end_date']    ?? '');

        $errors = $this->validate($name, $description, $startDate, $endDate);

        if (empty($errors['name']) && $this->projects->nameExists($name)) {
            $errors['name'] = 'A project with this name already exists.';
        }

        if (!empty($errors)) {
            return new Response($this->view->render('projects/create', [
                'errors' => $errors,
                'old'    => [
                    'name'        => $request->post['name']        ?? '',
                    'description' => $description,
                    'start_date'  => $startDate,
                    'end_date'    => $endDate,
                ],
            ]), 422);
        }

        $this->projects->save([
            'name'        => $name,
            'description' => $description,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
        ]);

        return (new Response())->setHeader('Location', '/projects')->setStatusCode(302);
    }

    public function show(string $id): Response
    {
        $project = $this->projects->find((int) $id);

        if (!$project) {
            return new Response($this->view->render('errors/404', [
                'message' => "Project #{$id} was not found.",
            ]), 404);
        }

        $tasks = $this->tasks->findByProject((int) $id);

        return new Response($this->view->render('projects/show', [
            'project' => $project,
            'tasks'   => $tasks,
        ]));
    }

    public function edit(string $id): Response
    {
        $project = $this->projects->find((int) $id);

        if (!$project) {
            return new Response($this->view->render('errors/404', [
                'message' => "Project #{$id} was not found.",
            ]), 404);
        }

        return new Response($this->view->render('projects/edit', [
            'project' => $project,
            'errors'  => [],
        ]));
    }

    public function update(Request $request, string $id): Response
    {
        $project = $this->projects->find((int) $id);

        if (!$project) {
            return new Response($this->view->render('errors/404', [
                'message' => "Project #{$id} was not found.",
            ]), 404);
        }

        $name        = trim($request->post['name']        ?? '');
        $description = trim($request->post['description'] ?? '');
        $startDate   = trim($request->post['start_date']  ?? '');
        $endDate     = trim($request->post['end_date']    ?? '');

        $errors = $this->validate($name, $description, $startDate, $endDate);

        if (empty($errors['name']) && $this->projects->nameExists($name, (int) $id)) {
            $errors['name'] = 'A project with this name already exists.';
        }

        if (!empty($errors)) {
            return new Response($this->view->render('projects/edit', [
                'project' => array_merge($project, [
                    'name'        => $request->post['name'] ?? '',
                    'description' => $description,
                    'start_date'  => $startDate,
                    'end_date'    => $endDate,
                ]),
                'errors' => $errors,
            ]), 422);
        }

        $this->projects->update((int) $id, [
            'name'        => $name,
            'description' => $description,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
        ]);

        return (new Response())->setHeader('Location', '/projects')->setStatusCode(302);
    }

    public function delete(string $id): Response
    {
        $this->tasks->deleteByProject((int) $id);
        $this->projects->delete((int) $id);

        return (new Response())->setHeader('Location', '/projects')->setStatusCode(302);
    }

    private function validate(string $name, string $description, string $startDate, string $endDate): array
    {
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Project name is required.';
        } elseif (strlen($name) > 255) {
            $errors['name'] = 'Project name must not exceed 255 characters.';
        }

        if ($description === '') {
            $errors['description'] = 'Description is required.';
        }

        if ($startDate === '') {
            $errors['start_date'] = 'Start date is required.';
        } else {
            $dt = \DateTime::createFromFormat('Y-m-d', $startDate);
            if ($dt === false || $dt->format('Y-m-d') !== $startDate) {
                $errors['start_date'] = 'Invalid start date.';
            }
        }

        if ($endDate === '') {
            $errors['end_date'] = 'End date is required.';
        } else {
            $dt = \DateTime::createFromFormat('Y-m-d', $endDate);
            if ($dt === false || $dt->format('Y-m-d') !== $endDate) {
                $errors['end_date'] = 'Invalid end date.';
            }
        }

        if (empty($errors['start_date']) && empty($errors['end_date']) && $startDate !== '' && $endDate !== '') {
            if ($endDate < $startDate) {
                $errors['end_date'] = 'End date must not be before start date.';
            }
        }

        return $errors;
    }
}
