<?php 
require_once __DIR__ . '/../lib/WebMethod.php';
class AAjax extends \WebMethod\WebMethod {
    public $logData = [
        "method"=>null,
        "params"=>null,
        "response"=>null,
        "error"=>null,
    ];
    protected function convertToArray($methodName, $methodParams, $methodResult, $methodOutParams, $methodDuration, $methodException): array {       
        $this->logData["method"] = $methodName;
        $this->logData["params"] = $methodParams;
        if (is_null($methodException) ) {            
            $this->logData["response"] = [
                "result"=>$methodResult,
                "outs"=>$methodOutParams
            ];            
            return [
                "success"=>true,
                "method"=>$methodName,
                "outputs"=>$methodOutParams,
                "result"=>$methodResult
            ];
        } else {        
            $this->logData["error"] = [
                "message"=>$methodException->getMessage(),
                "code"=>$methodException->getCode(),
                "file"=>$methodException->getFile(),
                "line"=>$methodException->getLine(),
            ];
            return [
                "success"=>false,
                "method"=>$methodName,
                "text"=>$methodException->getMessage()
            ];
        }
    }

    protected function generateParam(string $name, string $command) {
        return null;
    }

    protected function methodAuthorization($method, $params): bool {        
        return true;
    }
}