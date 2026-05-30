<?php

declare(strict_types=1);

namespace Core\View;

class Engine
{
    public function render(string $view, array $data = []): string
    {
        $viewPath = __DIR__ . '/../../app/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View [$view] not found.");
        }

        extract($data);

        ob_start();
        require $viewPath;
        return ob_get_clean();
    }
}
