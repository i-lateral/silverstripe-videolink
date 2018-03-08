# Video Link module for silverstripe

Allows the user to enter a youtube or vimeo share link and converts it into in iframe embed on the frontend.

## Requirements

silverstripe/framework ~4.0

## Installing

using composer: 

    composer require i-lateral/silverstripe-videolink

or you can manually clone this repo into <your_project_url>/vendor/i-lateral/

## Configuring

You can change the configuration settings by defining them in your mysite.yml:

```
ilateral\SilverStripe\VideoLink\ORM\FieldType\DBVideoLink
    embed_width: 600 // default width of iframe
    embed_height: 400 // default height of iframe
    youtube_classes: // classes to apply to wrapper on youtube videos
        - video-embed
        - youtube-video
    vimeo_classes: // classes to apply to wrapper on vimeo videos
        - video-embed
        - vimeo-video
```

## Usage

Simply add a 'VideoLink' DB field in the object you want to display a video

```
    private static $db = array(
        'Video' => 'VideoLink'
    );
```

and call the video in the template

    $Video.Embed()

or to set the size manually:

    $Video.Embed(1200,600)
