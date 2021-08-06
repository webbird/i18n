<?php

declare(strict_types=1);

namespace webbird\i18n;

use \Punic\Data As PunicData;
use \Punic\Misc as PunicMisc;

/**
 * Description of I18nTrait
 *
 * @author bmartino
 */
class Translator
{
    use \webbird\common\ArrayUtilsTrait;
    
    protected array $files = [];
    protected string|null $locale = null;
    protected array $translations = [];
    protected string $defaultTextDomain = 'default';
    
    public function __construct() {
        require __DIR__ . '/../../vendor/autoload.php';
    }
    
    /**
     * add a translation file; will not be loaded until needed 
     * 
     * inspired by Laminas\I18n\Translator::addTranslationFile
     * 
     * @param string $type
     * @param string $filename
     * @param string $textdomain - optional text domain; defaults to $this->defaultTextDomain
     * @param string $locale     - optional locale; defaults to $this->getLocale()
     * @return object
     */
    public function addTranslationFile(string $type, string $filename, ?string $textdomain = null, ?string $locale = null) : self
    {
        $locale     = ($locale ?: $this->getLocale());
        $textdomain = ($textdomain ?: $this->defaultTextDomain);
        if (!isset($this->files[$textdomain])) {
            $this->files[$textdomain] = [];
        }
        $this->files[$textdomain][$locale][] = [
            'type'     => $type,
            'filename' => $filename,
        ];
        return $this;
    }   // end function addTranslationFile()

    /**
     * tries to get the locale from HTTP_ACCEPT_LANGUAGE with fallback to
     * setlocale()
     * 
     * use $this->setLocale(<LC>) to override
     * 
     * @return string
     */
    public function getLocale() : string
    {
        if ($this->locale === null) { // try to get from browser
            $locales = PunicMisc::getBrowserLocales();
            $this->locale = key($locales);
        }
        if ($this->locale === null) {
            $this->locale = setlocale(LC_ALL, 0);
        }
        return $this->locale;
    }    
    
    /**
     * Set the default locale; this will also pass the $locale to Punic
     *
     * @param  string $locale
     * @return $this
     */
    public function setLocale(string $locale) : self
    {
        $this->locale = $locale;
        PunicData::setDefaultLocale($locale);
        return $this;
    }
    
    /**
     * override the name of the default text domain (default: 'default')
     * 
     * @param string $textdomain
     * @return self
     */
    public function setDefaultTextdomain(string $textdomain) : self
    {
        $this->defaultTextDomain = $textdomain;
        return $this;
    }
    
    /**
     * 
     * @param string $message         - text to translate
     * @param string|null $textDomain - fallback to $defaultTextDomain
     * @param string|null $locale     - fallback to getLocale()
     * @return string
     */
    public function translate(
        string $message, 
        ?string $textDomain = null, 
        ?string $locale = null,
        mixed ...$replacements
    ) {
        if ($message === '' || $message === null) {
            return '';
        }
        $locale      = ($locale ?: $this->getLocale());
        $textDomain  = ($textDomain ?: $this->defaultTextDomain);
        if (! isset($this->translations[$textDomain][$locale])) {
            $this->loadTranslations($textDomain, $locale);
        }
        if (isset($this->translations[$textDomain][$locale][$message])) {
            $message = $this->translations[$textDomain][$locale][$message];
        }
        if($replacements && isset($replacements['replacements'])) {
            $message = str_replace(
                    array_keys($this->wrapKeys($replacements['replacements'],'%')), 
                    array_values($replacements['replacements']), 
                    $message
            );
        }
        return $message;
    }
    
    /**
     * 
     * @param string $textDomain
     * @param string $locale
     */
    protected function loadTranslations(string $textDomain, string $locale)
    {
        if (! isset($this->translations[$textDomain])) {
            $this->translations[$textDomain] = [];
        }
        $loader = new FileLoader();
        foreach ([$locale, '*'] as $currentLocale) {
            if (! isset($this->files[$textDomain][$currentLocale])) {
                continue;
            }
            foreach ($this->files[$textDomain][$currentLocale] as $file) {
                if (isset($this->translations[$textDomain][$locale])) {
                    $this->translations[$textDomain][$locale]->merge($loader->get($file['type'])->load($file['filename']));
                } else {
                    $this->translations[$textDomain][$locale] = $loader->get($file['type'])->load($file['filename']);
                }
            }
        }
    }
   
}
