<?php
require __DIR__ . '/facebook-sdk-v5/autoload.php';

class FBGallery
{
    /**
     * FBGallery constructor.
     * @param $config array
     */
    public function __construct($config) {
        $this->fb = new \Facebook\Facebook([
            'app_id' => $config['app_id'],
            'app_secret' => $config['app_secret'],
            'default_graph_version' => 'v2.8'
        ]);

		//print_r($config['app_secret']);
		$this->helper = $this->fb->getRedirectLoginHelper();
         //$_SESSION['FBRLH_state']=$_GET['state'];
    //  $_SESSION['FBRLH_state']= isset($_GET['state']) ? $_GET['state'] : '';


        //$this->fb->setDefaultAccessToken((string)$this->access_token);
		//print_r("fgf");
		
		if (isset($_SESSION['facebook_access_token'])) {
			$this->access_token = $_SESSION['facebook_access_token'];
             //print_r($_SESSION['facebook_access_token']);
		} else {
			$this->access_token = $this->helper->getAccessToken();
               //print_r('fg'.$this->access_token);
            //$_SESSION['facebook_access_token'] = (string) $this->access_token; 
		}

       if(isset($this->access_token)){
            if (!isset($_SESSION['facebook_access_token'])) {
                $_SESSION['facebook_access_token'] = (string) $this->access_token; 
            } else {
                 $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            }
       } 

       // if (isset($_SESSION['facebook_access_token'])) {
           // $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
       // } else {
           //$_SESSION['facebook_access_token'] = (string) $this->access_token; 
       // }
       // $this->access_token = $this->helper->getAccessToken();
        //$this->fb->setDefaultAccessToken( $this->access_token );
		
		
        $this->page_name = $config['page_name'];
        $this->breadcrumbs = $config['breadcrumbs'];
        $this->cache = $config['cache'];
		
		$this->permissions = ['user_photos'];
    }
	

    public function display(){
        try{

		 
			//$helper = $this->fb->getRedirectLoginHelper();
		  if(isset($this->access_token)){

            if(empty($_GET['id'])){
                return $this->displayAlbums();
            }

            return $this->displayPhotos($_GET['id'],$_GET['title']);
		 } else {
			  $loginUrl = $this->helper->getLoginUrl('http://localhost/rtfb/', $this->permissions);
              echo '<div style="margin-top:150px"><center><a style="width:300px" href="' . $loginUrl . '" class="btn btn-block btn-lg btn-social btn-facebook"><span class="fa fa-facebook"></span> Sign in with Facebook</a></center></div>';
			 // echo '<a href="' . $loginUrl . '">Login with Facebook</a>';
		  }
		  
		  
        } catch(Exception $e){
            return 'Unable to display gallery due to the following error: '.$e->getMessage();
        }
    }


public function logout_account(){
    //print_r("fg");
    session_destroy();
    header('location:index.php');
    //session_start();
   // print_r($_SESSION['facebook_access_token']);
    //$this->fb->setDefaultAccessToken('');

    //header('location: index.php');
    //$this->fb->setDefaultAccessToken('');
}
    /**
     * Sends each request Facebook (currently only for 'albums' and 'photos')
     *
     * @param string $album_id
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    private function getData($album_id='',$type=''){
        if($type == 'photos'){
            $url = 'https://graph.facebook.com/v2.8/'.$album_id.'/photos?access_token='.$this->access_token.'&limit=100&fields=id,picture,source';

        } else if($type=='albums') {
           
            $url = 'https://graph.facebook.com/v2.8/me/albums?access_token='.$this->access_token.'&limit=100&fields=id,name,cover_photo,count,url';
          
            //
        } 
//print_r($url);
        
        //$ch = curl_init($url);
		//print_r($ch);
       // curl_setopt($ch, CURLOPT_HEADER,0);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        
        $ch = curl_init();
		 //curl_setopt($ch, CURLOPT_HTTPHEADER, 1);
		 curl_setopt($ch, CURLOPT_URL, $url);
	         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        
        $return_data = curl_exec($ch);

        $json_array = json_decode($return_data,true);
        if(isset($json_array['error'])){
            throw new Exception($json_array['error']['message']);
        }

       
        return $json_array;
    }

    public function getalbumtemp(){
        $gallery = '';
        $albums = $this->getData($this->page_name,$type='albums');
 
			
        return $albums;
    }
    private function displayAlbums(){
        //$cache = $this->getCache($this->page_name); // loads cached file
        //if($cache) return $cache;

        $gallery = '';
        $albums = $this->getData($this->page_name,$type='albums');


		//print_r($albums);
        foreach($albums['data'] as $album){
            if($album['count'] > 0) {
				//$im = getimagesize('https://graph.facebook.com/'.$album['cover_photo']['id'].'/picture?type=normal&access_token='.$this->access_token.''); 
				//$centreX = $im[0];
				
				//print_r($centreX);
                $img_url = 'https://graph.facebook.com/'.$album['cover_photo']['id'].'/picture?type=normal&access_token='.$this->access_token.'';
              
               // print_r($img_url);
                
				$img_str = "url('".$img_url."');";
			
				
				
                $gallery .= '<div class="col-lg-2 col-sm-3 col-xs-6">
                                <a href="?id='.$album['id'].'&title='.urlencode($album['name']).'" class="thumbnail" rel="tooltip" data-placement="bottom" title="'.$album['name'].' ('.$album['count'].')">
                               
                                   
									<div class="center-cropped" style="width: 100%; height: 150px; background-size: cover; background-position: center center; background-repeat: no-repeat; background-image:'.$img_str.'"></div>
                                </a>
                                <a href="zip2.php"><b>Download Album<b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href=""><b>Move<b></a>
                            </div>';
							
							$_SESSION['f2'] = $album;
					 		$_SESSION['f3'] = $album['name'];
            }
        }

        $gallery = '<div class="row">'.$gallery.'</div>';

        if($this->breadcrumbs){
            $crumbs = array('Gallery' => $_SERVER['PHP_SELF']);
            $gallery = $this->addBreadCrumbs($crumbs).$gallery;
        }

       // $this->saveCache($this->page_name,$gallery); // saves cached HTML file

        return $gallery;
    }

    private function displayPhotos($album_id,$title='Photos'){
        $cache = $this->getCache($album_id); // loads cached file
        if($cache) return $cache;

        $photos = $this->getData($album_id,$type='photos');
        if(count($photos) == 0) return 'No photos in this gallery';

        $gallery = '';
        foreach($photos['data'] as $photo)
        {

            $img_url = 'https://graph.facebook.com/'.$photo['id'].'/picture?type=normal&access_token='.$this->access_token.'';
                $img_str = "url('".$img_url."');";
            
               

            $gallery .= '<div class="col-lg-2 col-sm-3 col-xs-6">
                            <a href="'.$photo['source'].'" rel="prettyPhoto['.$album_id.']" title="" class="thumbnail">
                               <div class="center-cropped" style="width: 100%; height: 150px; background-size: cover; background-position: center center; background-repeat: no-repeat; background-image:'.$img_str.'"></div>
                            </a>
                        </div>';
						
						
        }

        $gallery = '<div class="row">'.$gallery.'</div>';

        if($this->breadcrumbs){
            $crumbs = array('Gallery' => $_SERVER['PHP_SELF'],  $title => '');
            $gallery = $this->addBreadCrumbs($crumbs).$gallery;
        }

        $this->saveCache($album_id,$gallery); // saves cached HTML file

        return $gallery;
    }

    /**
     * Loops through array of breadcrubs to be displayed
     * $crumbs must be setup like array('parent title' => 'parent url','child title' => 'child array')
     *
     * @param $crumbs_array
     * @return string
     */
    private function addBreadCrumbs($crumbs_array){
        $crumbs = '';
        if(is_array($crumbs_array)){
            foreach($crumbs_array as $title => $url){
                $crumbs .= '<li><b><a style="color:#365899" href="'.$url.'">'.stripslashes($title).'</a></b></li>';
            }

            return '<ol class="breadcrumb" style="background-color:#fff; border:1px solid #ddd;">'.$crumbs.'</ol>';
        }
    }


    ##---------------------------
    ## CACHE
    ##---------------------------
    private function saveCache($id,$html){
        if($this->cache && is_writable($this->cache['location']))
        {
            $fp = @fopen($this->cache['location'].'/'.$id.'.html', 'w');
            if (false == $fp) {
                $error = error_get_last();
                throw new Exception('Unable to save cache due to '.$error['message']);
            } else {
                fwrite($fp, $html);
                fclose($fp);
            }

        }
    }

    private function getCache($id){
        if($this->cache) {
            $cache_file = $this->cache['location'].'/'.$id.'.html';
            if(file_exists($cache_file) AND filemtime($cache_file) > (date("U") - $this->cache['time'])) {
                return file_get_contents($cache_file);

            }
        }

        return false;
    }
}
