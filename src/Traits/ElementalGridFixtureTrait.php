<?php

declare(strict_types=1);

namespace WeDevelop\Cypress\Traits;

use DNADesign\Elemental\Extensions\ElementalPageExtension as DNAExtensionsElementalPageExtension;
use DNADesign\Elemental\Models\ElementalArea;
use WeDevelop\ElementalGrid\Extensions\ElementalPageExtension as WeDevelopExtensionsElementalPageExtension;

trait ElementalGridFixtureTrait {
    /**
     * @param class-string $class
     */
    protected function createPageWithGrid(string $className, ...$args) {
        $page = call_user_func(sprintf('%s::create', $className), $args);
        $page->write();

        if (
            !$page->hasExtension(DNAExtensionsElementalPageExtension::class)
            &&
            !$page->hasExtension(WeDevelopExtensionsElementalPageExtension::class)
        ) {
            return $page;
        }

        $elementalArea = ElementalArea::create();
        $elementalArea->OwnerID = $page->ID;
        $page->ElementalAreaID = $elementalArea->write();
        $page->write();
    }
}