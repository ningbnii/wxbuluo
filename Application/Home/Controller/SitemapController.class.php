<?php
namespace Home\Controller;
class SitemapController extends HomeController{
	public function createSitemap(){
		$links = $this->getPageLink('http://www.wxbuluo.com');//获取http://www.vardream.cn页面的所有链接

		$content = '<?xml version="1.0" encoding="UTF-8"?>
		<urlset
		    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
		    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
		';//以上是生成sitemap.xml的页头部分

		$str1 = 'http://www.wxbuluo.com/';
		$str2 = 'http://www.wxbuluo.com/article';
		$str3 = 'http://www.wxbuluo.com/category';
		$str4 = 'http://www.wxbuluo.com/index.html';
		foreach ($links as $data) {
		    if (stripos($data, $str3) !== false) {
		        //使用绝对等于
		        $priority = 0.6;
		    } elseif (stripos($data, $str2) !== false) {
		        $priority = 0.8;
		    } elseif ($data == $str1 || $data == $str4) {
		        $priority = 1.0;
		    } else {
		        $priority = 0.5;//根据页面的重要性设定页面的priority值，利于搜索引擎判断页面的重要性
		    }
		    $content .= $this->create_item($data, $priority);
		}
		$content .= '</urlset>';
		$fp = fopen('sitemap.xml', 'w+');
		fwrite($fp, $content);//写入
		fclose($fp);
	}

	/* 
	*PHP获取页面中的所有链接 
	*/
	protected function getPageLink($url)
	{
	    set_time_limit(0);
	    $html = file_get_contents($url);
	    preg_match_all('/<a(s*[^>]+s*)href=(["|\']?)([^"\'>\\s]+)(["|\']?)/ies', $html, $out);
	    $arrLink = $out[3];
	    $arrUrl = parse_url($url);
	    $dir = '';
	    if (isset($arrUrl['path']) && !emptyempty($arrUrl['path'])) {
	        $dir = str_replace('\\', '/', ($dir = dirname($arrUrl['path'])));
	        if ($dir == '/') {
	            $dir = '';
	        }
	    }
	    if (is_array($arrLink) && count($arrLink) > 0) {
	        $arrLink = array_unique($arrLink);
	        foreach ($arrLink as $key => $val) {
	            $val = strtolower($val);
	            if (preg_match('/^#*$/isU', $val)) {
	                unset($arrLink[$key]);
	            } elseif (preg_match('/^\\//isU', $val)) {
	                $arrLink[$key] = ('http://' . $arrUrl['host']) . $val;
	            } elseif (preg_match('/^javascript/isU', $val)) {
	                unset($arrLink[$key]);
	            } elseif (preg_match('/^mailto:/isU', $val)) {
	                unset($arrLink[$key]);
	            } elseif (!preg_match('/^\\//isU', $val) && strpos($val, 'http://') === FALSE) {
	                $arrLink[$key] = ((('http://' . $arrUrl['host']) . $dir) . '/') . $val;
	            }
	        }
	    }
	    sort($arrLink);
	    return $arrLink;
	}

	/**
	 * 生成item
	 */
	function create_item($data, $priority)
	{
	    $item = '<url>
	';
	    $item .= ('<loc>' . $data) . '</loc>
	';
	    $item .= ('<priority>' . $priority) . '</priority>
	';
	    $item .= ('<changefreq>' . 'weekly') . '</changefreq>
	';
	    $item .= '</url>
	';
	    return $item;
	}
}