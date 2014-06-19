<?php

class ApiDoc
{

    protected $jsonData;
    public $base_url;
    public $version;
    public $date;
    public $project_name;
    public $project_desc;
    protected $errors;
    protected $objects;
    protected $object_url;
    protected $resource_url;
    protected $objectName;

    protected $resourceCount=1;

    public $resourceHeaderHTML = '';
    public $objectHeaderHTML = '';
    public $objectContainerHTML = '';
    public $resourcePlayerHTML = '';

    protected $resourcePlayHTML = '';


    public function make($filePath)
    {
        $apiDoc = new ApiDoc();
        $this->jsonData = file_get_contents($filePath);
        $this->jsonData = json_decode($this->jsonData);
        return $this;
    }
    protected function Errors()
    {

    }

    protected function Objects($object)
    {
        //$this->resourceCount=1;
        $this->objectContainerHTML .= "<table class='table' id='resourcesTable'>";


        foreach($object->resources as $resource)
        {
            $this->resource_url = $this->object_url.(($resource->uri=='/')? '':$resource->uri);
            $method = strtoupper($resource->method);
            $methodTD = "";
            $methodColour="";
            $icon="";
            if($method=='GET'){$methodColour ='grass2';}
            else if($method=='POST'){$methodColour ='sunflower1';}
            else if($method=='PUT'){$methodColour ='bluejeans1';}
            else if($method=='PATCH'){$methodColour ='aqua1';}
            else if($method=='DELETE'){$methodColour ='bittersweet1';}
            
            $methodTD= "<td class='$methodColour wf'>$method</td>";
            if($resource->returns =='element'){$icon='glyphicon-tag';}
            else if($resource->returns =='collection'){$icon='glyphicon-tags';}


            $this->objectContainerHTML .= "<tr class='resourceTr' data-resourceSeq='".$this->resourceCount."'>$methodTD<td>$this->resource_url</td><td>$resource->description</td></tr>";
            
            $this->resourceHeaderHTML .= "<div id='".$this->objectName."ResourceHeader".$this->resourceCount."' class='resource'>
              <span class='resource-back'><i class='glyphicon glyphicon-chevron-left'>&nbsp;</i></span>
              <span class='resource-method $methodColour wf'>$method</span>
              <span class='resource-link'>$this->resource_url</span>
              <span class='resource-returns'><i class='glyphicon $icon'>&nbsp;</i></span>          
              <span class='resource-description'>$resource->description</span>
            </div>";

            $this->resourcePlayerHTML .= "<div id='".$this->objectName."ResourcePlayer".$this->resourceCount."' class='player'>
              <span class='player-back'><i class='glyphicon glyphicon-chevron-left'>&nbsp;</i></span>
              <span class='player-method $methodColour wf' data-resourceUrl='$this->base_url".$this->resource_url."'>$method</span>
              <span class='player-link'><input type='text' value='$this->base_url".$this->resource_url."' id='iUrl$this->objectName".$this->resourceCount."'  data-resourceUrl='$this->base_url".$this->resource_url."'></span>
            </div>";
            $this->Resources($resource);
            $this->resourceCount++;

        }

        $this->objectContainerHTML .= "</table>";
    }

    protected function Resources($resource)
    {

        $this->objectContainerHTML .= "<tr class='resourceDescTr'><td colspan='3'>";
        $this->resourcePlayHTML = "<tr class='resourcePlayTr'><td colspan='1' class='resourcePlayTrInput'>";
        
        $this->Requests($resource->Request);
        $this->Responses($resource->Response);
        
        $this->resourcePlayHTML .= "</td><td colspan='2' class='resourcePlayTrTextarea'><span class='morphResponse' data-id='$this->objectName".$this->resourceCount."' id='Morph$this->objectName".$this->resourceCount."'>&nbsp; &nbsp;<i class='glyphicon glyphicon-retweet'>&nbsp;</i></span><span id='Status".$this->objectName.$this->resourceCount."'>Status : &nbsp; &nbsp; &nbsp;&nbsp;  </span><span id='Time".$this->objectName.$this->resourceCount."'>Time : &nbsp; &nbsp;&nbsp;</span><textarea id='Ta$this->objectName".$this->resourceCount."' readonly></textarea></td></tr>";
        $this->objectContainerHTML .= "</td></tr>".$this->resourcePlayHTML;
    }
    protected function Requests($request)
    {
        $this->objectContainerHTML .= "<h2>Request</h2><div class='resourceRequest'><div class='resourceRequestHeaders'><h3>Headers</h3>";
                $this->Headers($request->headers);
        $this->objectContainerHTML .= "</div>";

        $this->resourcePlayHTML .= "<h3>Post Data</h3><form id='fPost$this->objectName".$this->resourceCount."'>";
        $this->objectContainerHTML .= "<div class='resourceRequestPostData'><h3>Post Parameters</h3><table class='table table-hover'>";                
            foreach($request->post_data as $requestData)
                {
                    $this->requestData($requestData);
                }
        $this->objectContainerHTML .= "</table></div>";

        $this->resourcePlayHTML .= "</form><hr><h3>Url Parameters</h3><form id='fUrl$this->objectName".$this->resourceCount."'>";
        $this->objectContainerHTML .= "<div class='resourceRequestUrlParams'><h3>Url Parameters</h3><table class='table table-hover'>";
                foreach($request->url_params as $requestData)
                {
                    $this->requestData($requestData);
                }
        $this->objectContainerHTML .= "</table></div>";
        $this->objectContainerHTML .= "</div>";
        $this->resourcePlayHTML .= "</form>";  

    }
    protected function Headers($headers)
    {
        $this->objectContainerHTML .= '<table class="table table-hover">';
        foreach ($headers as $key => $value) {
            $this->objectContainerHTML .= "<tr><td>$key</td><td>$value</td></tr>";
        }
        $this->objectContainerHTML .= '</table>';
    }

    protected function requestData($requestData)
    {
        $this->objectContainerHTML .= "<tr><td>$requestData->name</td><td>";
        if($requestData->required==0){$this->objectContainerHTML .= "No";}
        else if($requestData->required==1){$this->objectContainerHTML .= "Yes";}
        $this->objectContainerHTML .= "</td><td>$requestData->validations</td><td>$requestData->description</td></tr>";
        
        if($requestData->required==1){$this->resourcePlayHTML .= "&nbsp;*&nbsp;";}
        else {$this->resourcePlayHTML .= "&nbsp; &nbsp;";}
        $this->resourcePlayHTML .= "<input type='text' name='$requestData->name' title='$requestData->name [$requestData->validations]' placeholder='$requestData->name [$requestData->validations]' class='resourcePlayTrInputElement'><br>";
    }
    protected function Responses($response)
    {
        $this->objectContainerHTML .= "<h2>Response</h2><div class='resourceResponse'><div class='resourceResponseHeaders'><h3>Headers</h3>";
                $this->Headers($response->headers);
        $this->objectContainerHTML .= "</div>";

        $this->objectContainerHTML .= "<div class='resourceResponsePostData'><h3>Data</h3>";
            $this->responseData($response->response_data);        
        $this->objectContainerHTML .= "</div>";
        $this->objectContainerHTML .= "</div>";  
            
    }
    protected function responseData($responseData)
    {
        $this->objectContainerHTML .= '<table class="table table-hover">';
        foreach ($responseData as $key => $value) {
            $this->objectContainerHTML .= "<tr><td>$key</td><td>$value</td></tr>";
        }
        $this->objectContainerHTML .= '</table>';
    }

    public function generate()
    {
        $this->base_url =$this->jsonData->api_base_url;
        $this->version =$this->jsonData->version;
        $this->date =$this->jsonData->date;

        $this->project_name =$this->jsonData->Project;
        $this->project_desc =$this->jsonData->Description;
        $this->errors =$this->jsonData->errors;
        $this->objects =$this->jsonData->objects;


        $this->objectHeaderHTML ="<div id='midContainerObjectHeader' class='container page-header'>";
        $this->resourceHeaderHTML .= "<div id='midContainerResourceHeader' class='container page-header'>";
        $this->resourcePlayerHTML .= "<div id='midContainerResourcePlayerHeader' class='container page-header'>";

       
        $this->objectContainerHTML .= "<div id='objectContainer' class='page-content'>";
        foreach($this->objects as $object)
        {
            $this->object_url = '/v'.$this->version.'/'.$object->route;
            $this->objectName = strtolower($object->name);
            $this->objectHeaderHTML .= "<div id='".strtolower($object->name)."ObjectHeader' class='object'>
          <span class='object-name darkgray2 wf'>$object->name</span>
          <span class='object-link'>$this->object_url</span>
          <span class='object-methods'>[".strtoupper(implode(', ',$object->options))."]</span>
          <span class='object-description'>$object->description</span>
            </div>";
            $this->objectContainerHTML .= "<div id='$object->name"."Object' class='object'>";
            $this->Objects($object);
            $this->objectContainerHTML .= "</div>";
        }
        $this->objectContainerHTML .= "</div>";
        $this->resourcePlayerHTML .= "</div>";
        $this->resourceHeaderHTML .= "</div>";

        $this->objectHeaderHTML .= '</div>';
    }

}
$apiDoc = new ApiDoc();
$apiDoc->make('api.json')->generate();
?>
