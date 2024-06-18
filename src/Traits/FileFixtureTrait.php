<?php

declare(strict_types=1);

namespace WeDevelop\Cypress\Traits;

trait FileFixtureTrait {
    /**
     * @return array<string>
     */
    protected function resolveAsset(string $path): array {
        return [
            sprintf('dev/docker/silverstripe/assets/%s', ltrim($path, '/')),
            sprintf('public/assets/%s', ltrim($path, '/')),
        ];
    }

    protected function copyAsset(string $path): string {
        $sourcePath = sprintf('dev/docker/silverstripe/assets/%s', ltrim($path, '/'));
        $targetPath = sprintf('public/assets/%s', ltrim($path, '/'));

        if (file_exists($targetPath)) {
            return $targetPath;
        }

        $pathinfo = pathinfo($targetPath);
        if (!is_dir($pathinfo['dirname'])) {
            mkdir($pathinfo['dirname'], recursive: true);
        }

        copy($sourcePath, $targetPath);
        return $targetPath;
    }
}