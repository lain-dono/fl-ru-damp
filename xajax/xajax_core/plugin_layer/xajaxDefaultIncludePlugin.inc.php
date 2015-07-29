<?php

/*
    File: xajaxDefaultIncludePlugin.inc.php

    Contains the default script include plugin class.

    Title: xajax default script include plugin class

    Please see <copyright.inc.php> for a detailed description, copyright
    and license information.
*/

/*
    @package xajax
    @version $Id: xajaxDefaultIncludePlugin.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
    @copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
    @license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
    Class: xajaxIncludeClientScript

    Generates the SCRIPT tags necessary to 'include' the xajax javascript
    library on the browser.

    This is called when the page is first loaded.
*/
class xajaxIncludeClientScriptPlugin extends xajaxRequestPlugin
{
    public $sJsURI;
    public $aJsFiles;
    public $sDefer;
    public $sRequestURI;
    public $sStatusMessages;
    public $sWaitCursor;
    public $sVersion;
    public $sDefaultMode;
    public $sDefaultMethod;
    public $bDebug;
    public $bVerboseDebug;
    public $nScriptLoadTimeout;
    public $bUseUncompressedScripts;
    public $bDeferScriptGeneration;
    public $sLanguage;
    public $nResponseQueueSize;

    public function xajaxIncludeClientScriptPlugin()
    {
        $this->sJsURI = '';
        $this->aJsFiles = array();
        $this->sDefer = '';
        $this->sRequestURI = '';
        $this->sStatusMessages = 'false';
        $this->sWaitCursor = 'true';
        $this->sVersion = 'unknown';
        $this->sDefaultMode = 'asynchronous';
        $this->sDefaultMethod = 'POST';    // W3C: Method is case sensitive
        $this->bDebug = false;
        $this->bVerboseDebug = false;
        $this->nScriptLoadTimeout = 2000;
        $this->bUseUncompressedScripts = false;
        $this->bDeferScriptGeneration = false;
        $this->sLanguage = null;
        $this->nResponseQueueSize = null;
    }

    /*
        Function: configure
    */
    public function configure($sName, $mValue)
    {
        if ('javascript URI' == $sName) {
            $this->sJsURI = $mValue;
        } elseif ('javascript files' == $sName) {
            $this->aJsFiles = $mValue;
        } elseif ('scriptDefferal' == $sName) {
            if (true === $mValue) {
                $this->sDefer = 'defer ';
            } else {
                $this->sDefer = '';
            }
        } elseif ('requestURI' == $sName) {
            $this->sRequestURI = $mValue;
        } elseif ('statusMessages' == $sName) {
            if (true === $mValue) {
                $this->sStatusMessages = 'true';
            } else {
                $this->sStatusMessages = 'false';
            }
        } elseif ('waitCursor' == $sName) {
            if (true === $mValue) {
                $this->sWaitCursor = 'true';
            } else {
                $this->sWaitCursor = 'false';
            }
        } elseif ('version' == $sName) {
            $this->sVersion = $mValue;
        } elseif ('defaultMode' == $sName) {
            if ('asynchronous' == $mValue || 'synchronous' == $mValue) {
                $this->sDefaultMode = $mValue;
            }
        } elseif ('defaultMethod' == $sName) {
            if ('POST' == $mValue || 'GET' == $mValue) {    // W3C: Method is case sensitive
                $this->sDefaultMethod = $mValue;
            }
        } elseif ('debug' == $sName) {
            if (true === $mValue || false === $mValue) {
                $this->bDebug = $mValue;
            }
        } elseif ('verboseDebug' == $sName) {
            if (true === $mValue || false === $mValue) {
                $this->bVerboseDebug = $mValue;
            }
        } elseif ('scriptLoadTimeout' == $sName) {
            $this->nScriptLoadTimeout = $mValue;
        } elseif ('useUncompressedScripts' == $sName) {
            if (true === $mValue || false === $mValue) {
                $this->bUseUncompressedScripts = $mValue;
            }
        } elseif ('deferScriptGeneration' == $sName) {
            if (true === $mValue || false === $mValue) {
                $this->bDeferScriptGeneration = $mValue;
            } elseif ('deferred' == $mValue) {
                $this->bDeferScriptGeneration = $mValue;
            }
        } elseif ('language' == $sName) {
            $this->sLanguage = $mValue;
        } elseif ('responseQueueSize' == $sName) {
            $this->nResponseQueueSize = $mValue;
        }
    }

    /*
        Function: generateClientScript
    */
    public function generateClientScript()
    {
        if (false === $this->bDeferScriptGeneration) {
            $this->printJavascriptConfig();
            $this->printJavascriptInclude();
        } elseif (true === $this->bDeferScriptGeneration) {
            $this->printJavascriptInclude();
        } elseif ('deferred' == $this->bDeferScriptGeneration) {
            $this->printJavascriptConfig();
        }
    }

    /*
        Function: getJavascriptConfig

        Generates the xajax settings that will be used by the xajax javascript
        library when making requests back to the server.

        Returns:

        string - The javascript code necessary to configure the settings on
            the browser.
    */
    public function getJavascriptConfig()
    {
        ob_start();
        $this->printJavascriptConfig();

        return ob_get_clean();
    }

    /*
        Function: printJavascriptConfig
        
        See <xajaxIncludeClientScriptPlugin::getJavascriptConfig>
    */
    public function printJavascriptConfig()
    {
        $sCrLf = "\n";

        echo $sCrLf;
        echo '<';
        echo 'script type="text/javascript" ';
        echo $this->sDefer;
        echo 'charset="UTF-8">';
        echo $sCrLf;
        echo '/* <';
        echo '![CDATA[ */';
        echo $sCrLf;
        echo 'try { if (undefined == xajax.config) xajax.config = {}; } catch (e) { xajax = {}; xajax.config = {}; };';
        echo $sCrLf;
        echo 'xajax.config.requestURI = "';
        echo $this->sRequestURI;
        echo '";';
        echo $sCrLf;
        echo 'xajax.config.statusMessages = ';
        echo $this->sStatusMessages;
        echo ';';
        echo $sCrLf;
        echo 'xajax.config.waitCursor = ';
        echo $this->sWaitCursor;
        echo ';';
        echo $sCrLf;
        echo 'xajax.config.version = "';
        echo $this->sVersion;
        echo '";';
        echo $sCrLf;
        echo 'xajax.config.legacy = false;';
        echo $sCrLf;
        echo 'xajax.config.defaultMode = "';
        echo $this->sDefaultMode;
        echo '";';
        echo $sCrLf;
        echo 'xajax.config.defaultMethod = "';
        echo $this->sDefaultMethod;
        echo '";';

        if (false === (null === $this->nResponseQueueSize)) {
            echo $sCrLf;
            echo 'xajax.config.responseQueueSize = ';
            echo $this->nResponseQueueSize;
            echo ';';
        }

        echo $sCrLf;
        echo '/* ]]> */';
        echo $sCrLf;
        echo '<';
        echo '/script>';
        echo $sCrLf;
    }

    /*
        Function: getJavascriptInclude

        Generates SCRIPT tags necessary to load the javascript libraries on
        the browser.

        sJsURI - (string):  The relative or fully qualified PATH that will be
            used to compose the URI to the specified javascript files.
        aJsFiles - (array):  List of javascript files to include.

        Returns:

        string - The SCRIPT tags that will cause the browser to load the
            specified files.
    */
    public function getJavascriptInclude()
    {
        ob_start();
        $this->printJavascriptInclude();

        return ob_get_clean();
    }

    /*
        Function: printJavascriptInclude
        
        See <xajaxIncludeClientScriptPlugin::getJavascriptInclude>
    */
    public function printJavascriptInclude()
    {
        $aJsFiles = $this->aJsFiles;
        $sJsURI = $this->sJsURI;

        if (0 == count($aJsFiles)) {
            $aJsFiles[] = array($this->_getScriptFilename('xajax_js/xajax_core.js'), 'xajax');

            if (true === $this->bDebug) {
                $aJsFiles[] = array($this->_getScriptFilename('xajax_js/xajax_debug.js'), 'xajax.debug');
            }

            if (true === $this->bVerboseDebug) {
                $aJsFiles[] = array($this->_getScriptFilename('xajax_js/xajax_verbose.js'), 'xajax.debug.verbose');
            }

            if (null !== $this->sLanguage) {
                $aJsFiles[] = array($this->_getScriptFilename('xajax_js/xajax_lang_'.$this->sLanguage.'.js'), 'xajax');
            }
        }

        if ($sJsURI != '' && substr($sJsURI, -1) != '/') {
            $sJsURI .= '/';
        }

        $sCrLf = "\n";
        $this->_initCTRFTokenKey();
        foreach ($aJsFiles as $aJsFile) {
            echo '<';
            echo 'script type="text/javascript" src="';
            echo $sJsURI;
            echo $aJsFile[0].'?v='.$GLOBALS['RELEASE_VERSION'];
            echo '" ';
            echo $this->sDefer;
            echo 'charset="UTF-8"><';
            echo '/script>';
            echo $sCrLf;
        }

        if (0 < $this->nScriptLoadTimeout) {
            foreach ($aJsFiles as $aJsFile) {
                echo '<';
                echo 'script type="text/javascript" ';
                echo $this->sDefer;
                echo 'charset="UTF-8">';
                echo $sCrLf;
                echo '/* <';
                echo '![CDATA[ */';
                echo $sCrLf;
                echo 'window.setTimeout(';
                echo $sCrLf;
                echo ' function() {';
                echo $sCrLf;
                echo '  var scriptExists = false;';
                echo $sCrLf;
                echo '  try { if (';
                echo $aJsFile[1];
                echo '.isLoaded) scriptExists = true; }';
                echo $sCrLf;
                echo '  catch (e) {}';
                echo $sCrLf;
                echo '  if (!scriptExists) {';
                echo $sCrLf;
                echo '   alert("Error: the ';
                echo $aJsFile[1];
                echo ' Javascript component could not be included. Perhaps the URL is incorrect?\nURL: ';
                echo $sJsURI;
                echo $aJsFile[0];
                echo '");';
                echo $sCrLf;
                echo '  }';
                echo $sCrLf;
                echo ' }, ';
                echo $this->nScriptLoadTimeout;
                echo ');';
                echo $sCrLf;
                echo '/* ]]> */';
                echo $sCrLf;
                echo '<';
                echo '/script>';
                echo $sCrLf;
            }
        }
    }

    /*
        Function: _getScriptFilename
        
        Returns the name of the script file, based on the current settings.
        
        sFilename - (string):  The base filename.
        
        Returns:
        
        string - The filename as it should be specified in the script tags
        on the browser.
    */
    public function _getScriptFilename($sFilename)
    {
        if ($this->bUseUncompressedScripts) {
            return str_replace('.js', '_uncompressed.js', $sFilename);
        }

        return $sFilename;
    }

    public function _initCTRFTokenKey()
    {
        session_start();

        $sCrLf = "\n";
        echo '<';
        echo 'script type="text/javascript" ';
        echo $this->sDefer;
        echo 'charset="UTF-8">';
        echo $sCrLf;
        echo 'var U_TOKEN_KEY = "'.$_SESSION['rand'].'";';
        echo $sCrLf;
        echo '<';
        echo '/script>';
        echo $sCrLf;
    }
}

/*
    Register the xajaxIncludeClientScriptPlugin object with the xajaxPluginManager.
*/
$objPluginManager = xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new xajaxIncludeClientScriptPlugin(), 99);
