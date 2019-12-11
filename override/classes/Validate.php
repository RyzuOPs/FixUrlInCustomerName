<?php

class Validate extends ValidateCore
{
    
    /**
     * Check whether given name is valid
     *
     * @param string $name Name to validate
     *
     * @return int 1 if given input is a name, 0 else
     */

    public static function isName($name)
    {
        // module and overrive setting both must be enabled 
        if (Module::isEnabled('fixurlincustomername') && (Configuration::get('FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED'))) {
            
            $cleanName = stripslashes($name);
        
            // disallow content that looks like an url - slash character
            if (false !== strpos($cleanName, '/')) {
                
                return 0;

            }

            // disallow content that looks like an url - dots character
            $dotCharacters = array('.', '。');


            foreach ($dotCharacters as $dotCharacter) {
                $dotPosition = strpos($cleanName, $dotCharacter);
                if (false !== $dotPosition && isset($cleanName[$dotPosition + 1]) && ($cleanName[$dotPosition + 1] !== ' ')) {
                    
                    return 0;

                }
            }

            $validityPattern = Tools::cleanNonUnicodeSupport('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u');
            
            return preg_match($validityPattern, $cleanName);

            } 
        else {
            
            return parent::isName($name);

        }
    }
}