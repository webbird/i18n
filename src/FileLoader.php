<?php

declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webbird\i18n;

/**
 * Description of FileLoader
 *
 * @author bmartino
 */
class FileLoader 
{
    public function get(string $type)
    {
        $loader = '\webbird\i18n\Loader\\'.$type.'Loader';
        return new $loader();
    }
}
