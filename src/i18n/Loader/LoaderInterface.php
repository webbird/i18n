<?php

declare(strict_types=1);

namespace webbird\i18n\Loader;

/**
 *
 * @author bmartino
 */
interface LoaderInterface {
    public function load(string $filename);
}
