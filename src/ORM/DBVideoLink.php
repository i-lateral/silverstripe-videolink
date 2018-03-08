<?php

namespace ilateral\SilverStripe\VideoLink\ORM\FieldType;

use SilverStripe\ORM\DB;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\FieldType\DBString;
use SilverStripe\ORM\Connect\MySQLDatabase;

class DBVideoLink extends DBString
{
    private static $casting = array(
        'Embed' => 'HTMLText'
    );

        /**
     * The physical width to set on all elements (such as img
     * tags and video embeds). This will result in a width="xx"
     * attribute being added to the element.
     *
     * @config
     * @var integer
     */
    private static $embed_width = 600;
    
    /**
     * The physical height to set on all elements (such as img
     * tags and video embeds). This will result in a height="xx"
     * attribute being added to the element.
     *
     * @config
     * @var integer
     */
    private static $embed_height = 400;

    /**
     * These classes are added to a div that wraps 
     * each youtube iframe
     * 
     * @config
     * @var array
     */
    private static $youtube_classes = array(
        "video-embed",
        'youtube-video'
    );

    /**
     * These classes are added to a div that wraps 
     * each youtube iframe
     * 
     * @config
     * @var array
     */
    private static $vimeo_classes = array(
        "video-embed",
        'vimeo-video'
    );

        /**
     * (non-PHPdoc)
     * @see DBField::requireField()
     */
    public function requireField()
    {
        $charset = Config::inst()->get(MySQLDatabase::class, 'charset');
        $collation = Config::inst()->get(MySQLDatabase::class, 'collation');

        $parts = array(
            'datatype'=>'varchar',
            'precision'=> 255,
            'character set'=> $charset,
            'collate'=> $collation,
            'arrayValue'=>$this->arrayValue
        );

        $values = array(
            'type' => 'varchar',
            'parts' => $parts
        );

        DB::require_field($this->tableName, $this->name, $values);
    }

    /**
     * Get youtube video ID from content
     *
     * @param $url The share link to extract the ID from
     * @return string
     */
    protected function getYouTubeID($url) 
    {
        preg_match(
            "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",
            $url,
            $matches
        );
        
        if(isset($matches[1])) {
            return $matches[1];
        } else {
            return "";
        }
    }
    /**
     * Get vimeo video ID from content
     *
     * @param $url The share link to extract the ID from
     * @return string
     */
    protected function getVimeoID($url) 
    {        
        preg_match(
            "/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([‌​0-9]{6,11})[?]?.*/",
            $url,
            $matches
        );
        
        if(isset($matches[5])) {
            return $matches[5];
        } else {
            return "";
        }
    }

    public function Embed($width = null,$height = null)
    {
        $url = $this->Plain();
        if (empty($width)) {
            $width = $this->config()->embed_width;
        }
        if (empty($height)) {
            $height = $this->config()->embed_height;
        }

        if (strpos($url, "youtube") !== false || strpos($url, "youtu.be") !== false) {
            return $this->embedYoutube(
                $url,
                $width,
                $height
            );
        } elseif (strpos($url, "vimeo") !== false) {
            return $this->embedVimeo(
                $url,
                $width,
                $height
            );
        } else {
            return false;
        }
    }

    protected function embedYoutube($url, $width, $height)
    {
        $classes = implode(' ',$this->config()->youtube_classes);      
        $src = 'https://www.youtube.com/embed/' . $this->getYouTubeID($url);

        $vars = [
            'Classes' => $classes,
            'SRC' => $src,
            'Width' => $width,
            'Height' => $height
        ];

        return $this->renderWith(
            'ilateral\SilverStripe\VideoLink\ORM\FieldType\VideoEmbed',
            $vars
        );
    }
    
    protected function embedVimeo($url, $width, $height)
    {
        $classes = implode(' ',$this->config()->vimeo_classes);
        $src = 'https://player.vimeo.com/video/' . $this->getVimeoID($url);

        $vars = [
            'Classes' => $classes,
            'SRC' => $src,
            'Width' => $width,
            'Height' => $height
        ];

        return $this->renderWith(
            'ilateral\SilverStripe\VideoLink\ORM\FieldType\VideoEmbed',
            $vars
        );
    }
}