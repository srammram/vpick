<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance extends MY_Controller
{
    var $ftp_conn;
    var $local_file;
    var $version_path;
    function __construct() {
        parent::__construct();
        $this->load->library('zip');
        $this->version_path =  "patch/versions";//echo '<pre>';print_R($this->data);exit;
        $this->version_filepath = "files";
        $this->version_dbpath = 'database';
    }
    function upgrade(){
        $meta =array();
        $cur_version = $this->Settings->version;
        $available_versions = array();
        $latest_version = '';
        $this->ftp_conn = $this->ftpConnect();
        if($this->ftp_conn){
           $file_list = ftp_nlist($this->ftp_conn, $this->version_path);
        
            $n = array();
            foreach ($file_list as $v) {
                if ($this->ftp_is_dir($v)) {              
                    array_push($n,$v);
                }
            }
            $folderlist = $n;        
            $versions = $this->get_versions($this->ftp_conn);
            
            
            $total = count($versions);
            foreach($versions as $k => $v){
                if($v>$cur_version){
                    $available_versions[$k] = $v;
                }
                if($total==$k+1 && $cur_version!=$v){$latest_version = $v;}
                
            }
            
            
            
            //$this->data['cur_version_date'] = $this->site->get_product_version_history(); 
        }else{
            $this->data['network_error'] = 'Could not able to connect server. please check your network connection';
        }
        
        ftp_close($this->ftp_conn);
        $this->data['available_version'] = $available_versions;
        $this->data['latest_version'] = $latest_version;
        $this->data['cur_version'] = $cur_version;
        $this->data['last_applied_on'] =  $this->site->get_productVersion_date($cur_version);
        $bc = array(array('link' => base_url(), 'page' => lang('home')),  array('link' => '#', 'page' => lang('upgrade')));
        $meta = array('page_title' => lang('upgrade'), 'bc' => $bc);
        $this->page_construct('maintenance/update', $meta, $this->data);
    }
    function ftpConnect(){
        $ftp_details = $this->site->product_update_ftp();
	$ftp_server = $ftp_details->ftp_host;
	$ftp = ftp_connect($ftp_server);// or die("Could not connect to $ftp_server");
        if($ftp){
            $ftp_username=$ftp_details->ftp_username;//$this->ftp_details->ftp_username;
            $ftp_userpass=$ftp_details->ftp_password;//$this->ftp_details->ftp_password;
            $login = ftp_login($ftp, $ftp_username, $ftp_userpass);
            ftp_pasv($ftp, true);  
        }
	
	return $ftp;
    }
    function ftp_is_dir($dir) {
        
        if ($dir!='.' && $dir!='..') {
                return true;
        }
        return false;
    }
    function get_versions($ftp_conn){
        $path = 'files';
        $file_list = ftp_nlist($ftp_conn, $this->version_path);
        $n = array();
        foreach ($file_list as $v) {
            if ($v!='.' && $v!='..') {
               
                array_push($n,$v);
            }
        }
        $versions = $n;
        return $versions;
    }
    
    function download(){
        //ini_set('display_errors', 1);
        $ftpcon = $this->ftpConnect();
        $version = $this->input->post('version');
        $this->local_file = $version.'.zip';
        
        
        ////// Database Updates //////
        
        $versions = $this->get_versions($ftpcon);
        
        
        $h = fopen('php://temp', 'r+');
        foreach($versions as $k => $v){
            if($v<=$version){
                $server_DB_file = $this->version_path.'/'.$v.'/'.$this->version_dbpath.'/'.$v.'.sql';
                $fileSize = ftp_size($ftpcon, $server_DB_file);
                if ($fileSize != -1) {
                    ftp_fget($ftpcon, $h, $server_DB_file, FTP_BINARY, 0);
                } 
            }
        }
        $fstats = fstat($h);
        fseek($h, 0);
        $contents = @fread($h, $fstats['size']);
        $db_path = '';
        if($contents!=''){
            $db_path = $version.'.sql';
            //$this->db->query($contents);
            write_file($db_path, $contents);
        }
         
        
        
        /// file updates ///////
        $file_path = '';
        $server_file = $this->version_path.'/'.$v.'/'.$this->version_filepath.'/'.$version.'.zip';
        $fileSize = ftp_size($ftpcon, $server_file);
        if ($fileSize != -1) {
            if (ftp_get($ftpcon, $this->local_file, $server_file, FTP_BINARY)) {
                $file_path = $this->local_file;     
            }
        }
        
        ftp_close($ftpcon);
        echo json_encode(array('file_path'=>$file_path,'db_path'=>$db_path));exit;
       
    }
    public function extract()
    {
        $version = $this->input->post('version');
        $source_db_file = $this->input->post('db_path');
        $contents = @file_get_contents($source_db_file);
        $this->db->query($contents);
        
        $source_file = $this->input->post('file_path');
        $dest_dir = FCPATH;
        $zip = new ZipArchive;
        if ($zip->open($source_file) === TRUE) 
        {
            $zip->extractTo($dest_dir);
            $zip->close();
            unlink($source_file);
        }
        $this->site->update_version($version);
        $this->site->add_version_history($version);    
        unlink($source_db_file);
    }
    public function backup()
    {
        $oldversion = $this->Settings->version;
        $dbname = $oldversion.'.sql';
        $dbpath = $oldversion.'/'.$dbname;
        $filename = $oldversion.'.zip';
        $filepath = $oldversion.'/'.$filename;
        if (!file_exists($oldversion)) {
            mkdir($oldversion, 0777, true);
        }
        
        ///// files ///
        $this->load->dbutil();
        $date = date('d-m-Y');
        $prefs = array(
            'format' => 'txt',
            'filename' => $oldversion.'.sql'
        );
        $back = $this->dbutil->backup($prefs);
        $backup =& $back;	    
	$db_name = $oldversion.'.sql';
        write_file($dbpath, $backup);
        
        ///// files ///
        $this->zip->read_dir(FCPATH.'app',false);
        $this->zip->read_dir(FCPATH.'assets', false);
        $this->zip->read_dir(FCPATH.'files', false);
        $this->zip->read_dir(FCPATH.'install', false);
        $this->zip->read_dir(FCPATH.'node_modules', false);
        $this->zip->read_dir(FCPATH.'system', false);
        $this->zip->read_dir(FCPATH.'themes', false);
        $this->zip->read_dir(FCPATH.'vendor', false);
        $this->zip->read_file(FCPATH.'.htaccess', false);
        $this->zip->read_file(FCPATH.'index.php', false);
        //$this->zip->read_file(FCPATH.'server.js', false);
        $this->zip->read_file(FCPATH.'serverdb.js', false);
        $this->zip->read_file(FCPATH.'startserver.bat', false);
        $this->zip->archive($filepath);
        echo json_encode(array('success'=>'success'));
    }
    //https://codereview.stackexchange.com/questions/24578/is-dir-function-for-ftp-ftps-connections
    //https://stackoverflow.com/questions/1554346/how-to-check-using-php-ftp-functionality-if-folder-exists-on-server-or-not
}
