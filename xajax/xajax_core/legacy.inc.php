<?php

class legacyXajaxResponse extends xajaxResponse
{
    public function outputEntitiesOn()
    {
        $this->setOutputEntities(true);
    }
    public function outputEntitiesOff()
    {
        $this->setOutputEntities(false);
    }
    public function addConfirmCommands()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'confirmCommands'), $temp);
    }
    public function addAssign()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'assign'), $temp);
    }
    public function addAppend()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'append'), $temp);
    }
    public function addPrepend()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'prepend'), $temp);
    }
    public function addReplace()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'replace'), $temp);
    }
    public function addClear()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'clear'), $temp);
    }
    public function addAlert()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'alert'), $temp);
    }
    public function addRedirect()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'redirect'), $temp);
    }
    public function addScript()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'script'), $temp);
    }
    public function addScriptCall()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'call'), $temp);
    }
    public function addRemove()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'remove'), $temp);
    }
    public function addCreate()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'create'), $temp);
    }
    public function addInsert()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'insert'), $temp);
    }
    public function addInsertAfter()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'insertAfter'), $temp);
    }
    public function addCreateInput()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'createInput'), $temp);
    }
    public function addInsertInput()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'insertInput'), $temp);
    }
    public function addInsertInputAfter()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'insertInputAfter'), $temp);
    }
    public function addRemoveHandler()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'removeHandler'), $temp);
    }
    public function addIncludeScript()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'includeScript'), $temp);
    }
    public function addIncludeCSS()
    {
        $temp = func_get_args();

        return call_user_func_array(array($this, 'includeCSS'), $temp);
    }
    public function &getXML()
    {
        return $this;
    }
}

class legacyXajax extends xajax
{
    public function legacyXajax($sRequestURI = '', $sWrapperPrefix = 'xajax_', $sEncoding = XAJAX_DEFAULT_CHAR_ENCODING, $bDebug = false)
    {
        parent::xajax();
        $this->configure('requestURI', $sRequestURI);
        $this->configure('wrapperPrefix', $sWrapperPrefix);
        $this->configure('characterEncoding', $sEncoding);
        $this->configure('debug', $bDebug);
    }
    public function registerExternalFunction($mFunction, $sInclude)
    {
        $xuf = new xajaxUserFunction($mFunction, $sInclude);
        $this->register(XAJAX_FUNCTION, $xuf);
    }
    public function registerCatchAllFunction($mFunction)
    {
        if (is_array($mFunction)) {
            array_shift($mFunction);
        }
        $this->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_INVALID, $mFunction);
    }
    public function registerPreFunction($mFunction)
    {
        if (is_array($mFunction)) {
            array_shift($mFunction);
        }
        $this->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_BEFORE, $mFunction);
    }
    public function canProcessRequests()
    {
        return $this->canProcessRequest();
    }
    public function processRequests()
    {
        return $this->processRequest();
    }
    public function setCallableObject($oObject)
    {
        return $this->register(XAJAX_CALLABLE_OBJECT, $oObject);
    }
    public function debugOn()
    {
        return $this->configure('debug', true);
    }
    public function debugOff()
    {
        return $this->configure('debug', false);
    }
    public function statusMessagesOn()
    {
        return $this->configure('statusMessages', true);
    }
    public function statusMessagesOff()
    {
        return $this->configure('statusMessages', false);
    }
    public function waitCursorOn()
    {
        return $this->configure('waitCursor', true);
    }
    public function waitCursorOff()
    {
        return $this->configure('waitCursor', false);
    }
    public function exitAllowedOn()
    {
        return $this->configure('exitAllowed', true);
    }
    public function exitAllowedOff()
    {
        return $this->configure('exitAllowed', false);
    }
    public function errorHandlerOn()
    {
        return $this->configure('errorHandler', true);
    }
    public function errorHandlerOff()
    {
        return $this->configure('errorHandler', false);
    }
    public function cleanBufferOn()
    {
        return $this->configure('cleanBuffer', true);
    }
    public function cleanBufferOff()
    {
        return $this->configure('cleanBuffer', false);
    }
    public function decodeUTF8InputOn()
    {
        return $this->configure('decodeUTF8Input', true);
    }
    public function decodeUTF8InputOff()
    {
        return $this->configure('decodeUTF8Input', false);
    }
    public function outputEntitiesOn()
    {
        return $this->configure('outputEntities', true);
    }
    public function outputEntitiesOff()
    {
        return $this->configure('outputEntities', false);
    }
    public function allowBlankResponseOn()
    {
        return $this->configure('allowBlankResponse', true);
    }
    public function allowBlankResponseOff()
    {
        return $this->configure('allowBlankResponse', false);
    }
}
