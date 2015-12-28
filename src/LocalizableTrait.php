<?php
/**
 * Created by PhpStorm.
 * User: ryanchan
 * Date: 28/12/2015
 * Time: 4:37 PM
 */

namespace Riseno\Localizable;


use Exception;

/**
 * Class LocalizableTrait
 * @package Riseno\Localizable
 */
trait LocalizableTrait
{
    /**
     * @param string $locale localization code
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function saveTranslate($locale, $data)
    {
        $this->translatePropertyChecks();

        $fields = collect($data)->only($this->localizeFields);

        return $this->translate($locale)->fill($fields->toArray())->save();
    }

    /**
     * Retrieve translation model by locale
     *
     * @param string $locale localization code
     *
     * @return mixed
     * @throws \Exception
     */
    public function translate($locale)
    {
        $this->translatePropertyChecks();

        /*
         * Should only have one record per locale
         */
        $localize = $this->localizations->filter(function ($localize) use ($locale) {
            return $localize->locale === $locale;
        })->first();

        if (! $localize) {
            $localize = $this->newTranslation($locale);
        }

        return $localize;
    }

    /**
     * Create new translation record by locale
     *
     * @param string $locale localization code
     *
     * @return Object
     */
    protected function newTranslation($locale)
    {
        $localize = new $this->localizeModel;

        $localize->locale = $locale;

        $this->localizations()->save($localize);

        $this->localizations->push($localize);

        return $localize;
    }

    /**
     * Safety checks that make sure all the properties & methods is implemented.
     *
     * @throws \Exception
     */
    private function translatePropertyChecks()
    {
        if (! property_exists($this, 'localizeModel')) {
            throw new Exception('Missing "localizeModel" property in the model');
        }

        if (! property_exists($this, 'localizeFields')) {
            throw new Exception('Missing "localizeFields" property in the model');
        }

        if (! method_exists($this, 'localizations')) {
            throw new Exception('Missing "localizations" method in the model');
        }
    }
}