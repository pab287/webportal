<?php
    trait Logs_maker{
        private $template_content;
        private $page;
        private $emp_id;
        private $responce;

        private function template(){
            $date = date("Y-m-d H:i:s");
            
            $content = $this->template_content;
            $page = $this->page;
            $emp_id = $this->emp_id;
            $storage = dir."/androidapp/storage/logs/".$page;

            if(!is_dir($storage)){
                mkdir($storage, 777);
            }
            
            $template = json_encode($content);
            if(file_put_contents($storage.'/'.$emp_id.'.log', $template." ".$date."\n", FILE_APPEND)){
                $this->responce = true;
            }else{
                $this->responce = false;
            }
        }
    }
?>