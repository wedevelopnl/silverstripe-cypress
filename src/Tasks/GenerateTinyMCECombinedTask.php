<?php

declare(strict_types=1);

namespace WeDevelop\Cypress\Tasks;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;
use SilverStripe\Forms\HTMLEditor\TinyMCECombinedGenerator;
use SilverStripe\Forms\HTMLEditor\TinyMCEScriptGenerator;
use SilverStripe\i18n\i18n;

class GenerateTinyMCECombinedTask extends BuildTask
{
    /** @var string */
    protected $title = 'Generate TinyMCE configuration';

    /** @var string */
    protected $description = 'Silverstripe generates the TinyMCE bundle on the fly during the request, for CI purposes it is required to build the assets from the CLI';

    /** @config */
    private static string $segment = 'generate-tinyMCE-combined-task';

    public function run($request)
    {
        TinyMCECombinedGenerator::flush();

        $editorConfigs = HTMLEditorConfig::get_available_configs_map();
        $doGenerate = function() use($editorConfigs) {
            /** @var \SilverStripe\Forms\HTMLEditor\TinyMCEScriptGenerator $generator */
            $generator = Injector::inst()->create(TinyMCEScriptGenerator::class);
            foreach(array_keys($editorConfigs) as $identifier) {
                $generator->getScriptURL(HTMLEditorConfig::get($identifier));
            }
        };

        if (class_exists(i18n::class)) {
            // If the \SilverStripe\i18n\i18n class exists we do not know which locales
            // are enabled in the website at this point as this task is ran without active
            // database. So just generate the script for every known locale.
            foreach (array_keys(i18n::getData()->getLocales()) as $locale) {
                i18n::with_locale($locale, $doGenerate);
            };
        }
        else {
            $doGenerate();
        }

        print_r("Generated tinyMCE configuration files\n");
    }
}