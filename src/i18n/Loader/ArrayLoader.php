<?php

declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webbird\i18n\Loader;

class ArrayLoader implements LoaderInterface 
{
    /**
     * 
     * @param string $filename
     * @return \webbird\I18n\Loader\TextDomain
     * @throws Exception\InvalidArgumentException
     */
    public function load(string $filename) : \webbird\i18n\TextDomain
    {
        if (! is_file($filename) || ! is_readable($filename)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename
            ));
        }
        $translations = include $filename;
        if (! is_array($translations)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array, but received %s',
                get_debug_type($translations)
            ));
        }
        $td = new \webbird\i18n\TextDomain($translations);
        if ($td->offsetExists('')) {
            if (isset($td['']['locales'])) {
                $td->setLocales(
                    $td['']['locales']
                );
            }
            unset($td['']);
        }

        return $td;
    }
}
