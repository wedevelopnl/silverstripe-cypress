<?php

declare(strict_types=1);

namespace WeDevelop\Cypress\Traits;

use App\Pages\GridPage;
use DNADesign\Elemental\Extensions\ElementalPageExtension as DNAExtensionsElementalPageExtension;
use DNADesign\Elemental\Models\BaseElement;
use DNADesign\Elemental\Models\ElementalArea;
use SilverStripe\CMS\Model\SiteTree;
use WeDevelop\ElementalGrid\Extensions\ElementalPageExtension as WeDevelopExtensionsElementalPageExtension;

trait ElementalGridFixtureTrait {
    /**
     * @param class-string $className
     */
    protected function createPageWithGrid(string $className, ...$args): SiteTree {
        $page = call_user_func_array(sprintf('%s::create', $className), $args);
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

        return $page;
    }

    /**
     * @param class-string $elementClassName
     */
    protected function addElementToPage(SiteTree $page, string $elementClassName, ...$args): BaseElement {
        $elementArgs = ($args[0] ?? []) + ['ParentID' => $page->ElementalAreaID];

        /** @var BaseElement $element */
        $element = call_user_func(sprintf('%s::create', $elementClassName), $elementArgs);
        $element->write();
        return $element;
    }
}
