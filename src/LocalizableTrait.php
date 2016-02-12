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
    public function saveLocalize($locale, $data)
    {
        $this->localizationPropertyChecks();

        $fields = collect($data)->only($this->localizeFields);

        return $this->localize($locale)->fill($fields->toArray())->save();
    }

    /**
     * Retrieve localization model by locale
     *
     * @param string $locale localization code
     *
     * @return mixed
     * @throws \Exception
     */
    public function localize($locale)
    {
        $this->localizationPropertyChecks();

        /*
         * Should only have one record per locale
         */
        $localize = $this->localizations->filter(function ($localize) use ($locale) {
            return $localize->locale === $locale;
        })->first();

        if (! $localize) {
            $localize = $this->newLocalization($locale);
        }

        return $localize;
    }

    /**
     * Create new localization record by locale
     *
     * @param string $locale localization code
     *
     * @return Object
     */
    protected function newLocalization($locale)
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
    private function localizationPropertyChecks()
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