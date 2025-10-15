<?php
use Uitlaber\LaravelTelegramRay\Services\TelegramRayService;

if (!function_exists('ray')) {
    function ray(...$args)
    {
        if (!function_exists('base_path')) {
            return;
        }

        // "Белый список" директорий, где может находиться ваш код
        $whitelistedPaths = [
            base_path('app'),
            base_path('routes'),
            base_path('resources'),
            base_path('config'),
            base_path('database'),
            base_path('bootstrap'), // Для Laravel 11/12+
        ];

        $backtrace = debug_backtrace();
        $caller = null;

        foreach ($backtrace as $trace) {
            $file = null;

            // Сначала пытаемся "распаковать" SerializableClosure
            if (
                isset($trace['object']) &&
                class_exists(\Laravel\SerializableClosure\SerializableClosure::class) &&
                $trace['object'] instanceof \Laravel\SerializableClosure\SerializableClosure
            ) {
                try {
                    $reflection = new \ReflectionObject($trace['object']);
                    $closureProperty = $reflection->getProperty('closure');
                    $closureProperty->setAccessible(true);
                    $closure = $closureProperty->getValue($trace['object']);
                    $closureReflection = new \ReflectionFunction($closure);
                    $file = $closureReflection->getFileName();

                    // Для "распакованных" замыканий, нам также нужна правильная строка
                    $trace['line'] = $closureReflection->getStartLine();
                } catch (\ReflectionException $e) {
                    // Не удалось "распаковать", пропускаем
                    continue;
                }
            } elseif (isset($trace['file'])) {
                // Если это обычный файл
                $file = $trace['file'];
            }

            // Если мы смогли определить файл, проверяем его по "белому списку"
            if ($file) {
                // Нормализуем слеши для надежного сравнения в Windows
                $normalizedFile = str_replace('\\', '/', $file);
                foreach ($whitelistedPaths as $path) {
                    $normalizedPath = str_replace('\\', '/', $path);
                    if (str_starts_with($normalizedFile, $normalizedPath)) {
                        $caller = $trace;
                        $caller['file'] = $file; // Убедимся, что путь сохранен
                        break 2; // Выходим из обоих циклов, как только нашли первое совпадение
                    }
                }
            }
        }

        // Запасной вариант, если ничего не нашлось
        if (!$caller) {
            $caller = ['file' => 'unknown file', 'line' => 0];
        }

        $service = resolve(TelegramRayService::class);

        foreach ($args as $arg) {
            $service->send(
                $arg,
                $caller['file'],
                $caller['line']
            );
        }
    }
}
