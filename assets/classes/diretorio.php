<?php
    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
        echo "<script>window.location.href = '../index.php'</script>";
    }
    
    require_once 'db.php';
    class Diretorio {
        private $db;
        private $identidade = null;
        private $id = null;
        private $arquivo = null;
        public $caminho = "CAMINHO";


        public function __construct() {
            $this->db = new Conexao_BD();
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
        }
        public function AddDiretorio($caminho_backup = null){
            if($caminho_backup){
                $caminho = $this->caminho."backup_diretorio/".$this->CaminhoDiretorioBackup()."/".$this->id;
            }else{
                $caminho = $this->caminho.$this->identidade."/".$this->id;
            }
            if(mkdir($caminho,0777)){
                return true;
            }
        }
        public function ListaArquivos($backup = null){

            if($backup){
                $caminho = $this->caminho."backup_diretorio/".$this->CaminhoDiretorioBackup()."/".$this->id;
            }else{
                $caminho = $this->caminho.$this->identidade."/".$this->id;
            }
            if(@$arquivo = dir($caminho)){
                return $arquivo;
            } else {
                return false;
            }
        }
        public function UploadArquivo(){
            $caminho = $this->caminho.$this->identidade."/".$this->id."/".$this->arquivo['name'];
            if(move_uploaded_file($this->arquivo['tmp_name'],$caminho)){
                return true;
            }
        }
        public function BackupDiretorio(){
            try{
                $nome_pasta = $this->caminho."backup_diretorio/".$this->CaminhoDiretorioBackup()."/".$this->id;
                if(!is_dir($nome_pasta)){
                    $this->AddDiretorio(true);
                }
                $nome_pasta = $nome_pasta."/id_".$this->id."_".date("d-m-Y").".zip";
                $zip = new ZipArchive();
                if($zip->open($nome_pasta, ZipArchive::CREATE)){
                    if($arquivo = $this->ListaArquivos()){
                        while(($nome_arquivo = $arquivo->read()) !== false){
                            if($nome_arquivo!=="." && $nome_arquivo!==".."){
                                $zip->addFile($this->caminho.$this->identidade."/".$this->id."/".$nome_arquivo,$nome_arquivo);
                            }
                        }
                        if($zip->close()){
                            return true;
                        }
                    }
                }
            }catch(Exception $error){

                $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
                return false;
            }
        }
        public function DesativarDiretorio(){
            try{
                if($this->BackupDiretorio()){
                    if($arquivo = $this->ListaArquivos()){
                        while(($nome_arquivo = $arquivo->read()) !== false){
                            if($nome_arquivo!=="." && $nome_arquivo!==".."){
                                unlink($this->caminho.$this->identidade."/".$this->id."/".$nome_arquivo);
                            }
                        }
                        
                        if(rmdir($this->caminho.$this->identidade."/".$this->id)){
                            return true;
                        }
                    }
                }
            }catch(Exception $error){

                $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
                return false;
            }
        }
        public function AtivarDiretorio(){
            try{
                $arquivo_zip = $this->caminho."/"."backup_diretorio/".$this->CaminhoDiretorioBackup()."/".$this->id;
                $destino = $this->caminho.$this->identidade;
                if($this->AddDiretorio()){
                    $destino = $destino."/".$this->id;
                    $arquivo = $this->ListaArquivos(true);
                    $array_nome = [];
                    while(($nome_arquivo = $arquivo->read()) !== false){
                        if($nome_arquivo!=="." && $nome_arquivo!==".."){
                            array_push($array_nome,$nome_arquivo);
                        }
                    }
                    $zip = new ZipArchive();
                    $arquivo_zip = $arquivo_zip."/".end($array_nome);
                    $zip->open($arquivo_zip);
                    if($zip->extractTo($destino)){
                        return "true";
                    }
                    if($zip->close()){
                        return true;
                    }
                }
            }catch(Exception $error){
                $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
                return false;
            }
        }
        private function CaminhoDiretorioBackup(){
            switch ($this->identidade){
                case "diretorio_empresa":
                    return "backup_empresa";
                    break;
                case "diretorio_colaborador":
                    return "backup_colaborador";
                    break;
                case "diretorio_contrato":
                    return"backup_contrato";
                    break;
            }
        }
    }   
?>