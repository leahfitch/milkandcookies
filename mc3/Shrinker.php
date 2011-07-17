<?

require_once('mc3/Options.php');

class mc_Shrinker
{
    public static function css($files)
    {
        $cache_info = self::concatenate('css', $files);
        
        if ($cache_info['exists'])
        {
            return $cache_info['remote_path'];
        }
        
        require_once('mc3/shrinkers/cssmin-v2.0.2.2.php');
        
        file_put_contents(
            $cache_info['local_path'],
            CssMin::minify( 
                $cache_info['data'],
                array(
                    'convert-font-weight-values' => true,
                    'convert-named-color-values' => true,
                    'convert-hsl-color-values' => true,
                    'convert-rgb-color-values' => true,
                    'compress-color-values' => true,
                    'compress-unit-values' => true
                )
            )
        );
        
        return $cache_info['remote_path'];
    }
    
    
    public static function js($files)
    {
        $cache_info = self::concatenate('js', $files);
        
        if ($cache_info['exists'])
        {
            return $cache_info['remote_path'];
        }
        
        require_once('mc3/shrinkers/JSMin.php');
        
        file_put_contents(
            $cache_info['local_path'],
            JSMin::minify($cache_info['data'])
        );
        
        return $cache_info['remote_path'];
    }
    
    
    protected static function concatenate($type, $files)
    {
		$mtimes = array();
		
		foreach ($files as $file)
		{
		    $path = O::path($type.'_local', $file);
		    
		    if (!file_exists($path))
		    {
		        throw new Exception("$path does not exist.");
		    }
		    
			$mtimes[] = filemtime($path);
		}
		
		$tokens = array_merge($files, $mtimes);
		$filename = md5(implode('&',$tokens)).'.'.$type;
		$local_path = O::path('cache_local', $filename);
		$remote_path = O::path('cache', $filename);
        
		if (file_exists($local_path))
		{
		    return array(
		        'remote_path' => $remote_path,
		        'exists' => true
            );
		}
		
		$data = array();
		
		foreach ($files as $file)
		{
			$path = O::path($type.'_local', $file);
			$data[] = file_get_contents($path);
		}
		
		return array(
            'local_path' => $local_path,
            'remote_path' => $remote_path,
            'exists' => false,
            'data' => implode("\n", $data)
        );
    }
}

?>
