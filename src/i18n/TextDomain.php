<?php

declare(strict_types=1);

namespace webbird\i18n;

/**
 * Description of TextDomain
 *
 * @author bmartino
 */
class TextDomain extends \ArrayObject 
{
    protected array $pluralRules = [];
    protected array $locales = [];
    
    public function setPluralRules(array $rules) : self
    {
        $this->pluralRules = $rules;
        return $this;
    }
    
    public function getPluralRules() : array
    {
        return $this->pluralRules;
    }
    
    public function hasPluralRules() : bool
    {
        return (count($this->pluralRules)>0);
    }
    
    /**
     * 
     * @param TextDomain $textDomain
     * @return $this
     * @throws Exception\RuntimeException
     */
    public function merge(TextDomain $textDomain) : self
    {
        if ($this->hasPluralRules() && $textDomain->hasPluralRules()) {
            if (count($this->pluralRules) !== count($textDomain->getPluralRules())) {
                throw new Exception\RuntimeException(
                    'Plural rule of merging text domain is not compatible with the current one'
                );
            }
        } elseif ($textDomain->hasPluralRule()) {
            $this->setPluralRule($textDomain->getPluralRules());
        }

        $this->exchangeArray(
            array_replace(
                $this->getArrayCopy(),
                $textDomain->getArrayCopy()
            )
        );

        return $this;
    }
    
    function getLocales() : array
    {
        return $this->locales;
    }
    
    function setLocales(array $locales) : self
    {
        $this->locales = $locales;
        return $this;
    }
    
}
