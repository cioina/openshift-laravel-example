<?php namespace Distilleries\Expendable\Helpers;

      class StaticLabel {

          const STATUS_OFFLINE = 0;
          const STATUS_ONLINE  = 1;

          const BODY_TYPE_HTML = 'html';
          const BODY_TYPE_TEXT = 'text';

          const CODE_TYPE_HTML              = 'html';
          const CODE_TYPE_JAVASCRIPT        = 'javascript';
          const CODE_TYPE_CSS               = 'css';
          const CODE_TYPE_PHP               = 'php';
          const CODE_TYPE_JSON              = 'json';
          const CODE_TYPE_BASH              = 'bash';

          const YT_VIDEO_TYPE_UNKNOWN       = 0;
          const YT_VIDEO_TYPE_PERSONAL      = 1;
          const YT_VIDEO_TYPE_MUSIC         = 2;
          const YT_VIDEO_TYPE_TECH          = 3;
          const YT_VIDEO_TYPE_FUN           = 4;

          //https://stackoverflow.com/questions/2068344/how-do-i-get-a-youtube-video-thumbnail-from-the-youtube-api

          const NORMAL_QUALITY_THUMBNAIL              = 'default.jpg';
          //    Normal Quality Thumbnail (120x90 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/default.jpg

          const START_QUALITY_THUMBNAIL              = '1.jpg';
          //    Start Thumbnail (120x90 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/1.jpg

          const MIDDLE_QUALITY_THUMBNAIL              = '2.jpg';
          //    Middle Thumbnail (120x90 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/2.jpg

          const END_QUALITY_THUMBNAIL                 = '3.jpg';
          //    End Thumbnail (120x90 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/3.jpg

          const PLAYER_BACKGROUND_QUALITY_THUMBNAIL   = '0.jpg';
          //    Player Background Thumbnail (480x360 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/0.jpg

          const MEDIUM_QUALITY_THUMBNAIL              = 'mqdefault.jpg';
          //    Medium Quality Thumbnail (320x180 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/mqdefault.jpg

          const HIGH_QUALITY_THUMBNAIL                = 'hqdefault.jpg';
          //    High Quality Thumbnail (480x360 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/hqdefault.jpg

          //And additionally, the next two thumbnails may or may not exist. For HQ videos they exist.
          const STANDARD_DEFINITION_QUALITY_THUMBNAIL = 'sddefault.jpg';
          //    Standard Definition Thumbnail (640x480 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/sddefault.jpg

          const MAXIMUM_RESOLUTION_QUALITY_THUMBNAIL   = 'maxresdefault.jpg';
          //    Maximum Resolution Thumbnail (1920x1080 pixels)
          //    https://i1.ytimg.com/vi/G0wGs3useV8/maxresdefault.jpg

          const YOUTUBE_DOMAIN_I   = 'i';
          const YOUTUBE_DOMAIN_I1  = 'i1';
          const YOUTUBE_DOMAIN_I3  = 'i3';

          public static function status($id = null)
          {
              $items = [
                  self::STATUS_OFFLINE => trans('expendable::label.offline'),
                  self::STATUS_ONLINE  => trans('expendable::label.online'),
              ];

              return self::getLabel($items, $id);

          }

          public static function typeExport($id = null)
          {
              $items = [
                  'Distilleries\Expendable\Contracts\ExcelExporterContract' => trans('expendable::label.excel'),
                  'Distilleries\Expendable\Contracts\CsvExporterContract'   => trans('expendable::label.csv')
              ];

              return self::getLabel($items, $id);

          }

          public static function yesNo($id = null)
          {
              $items = [
                  self::STATUS_OFFLINE => trans('expendable::label.no'),
                  self::STATUS_ONLINE  => trans('expendable::label.yes'),
              ];

              return self::getLabel($items, $id);

          }

          public static function bodyType($id = null)
          {
              $items = [
                  self::BODY_TYPE_HTML => trans('expendable::label.html'),
                  self::BODY_TYPE_TEXT => trans('expendable::label.text'),
              ];

              return self::getLabel($items, $id);
          }

          public static function videoType($id = null)
          {
              $items = [
                  self::YT_VIDEO_TYPE_UNKNOWN       => trans('expendable::label.yt_unknown'),
                  self::YT_VIDEO_TYPE_PERSONAL      => trans('expendable::label.yt_personal'),
                  self::YT_VIDEO_TYPE_MUSIC         => trans('expendable::label.yt_music'),
                  self::YT_VIDEO_TYPE_TECH          => trans('expendable::label.yt_tech'),
                  self::YT_VIDEO_TYPE_FUN           => trans('expendable::label.yt_fun'),
               ];

              return self::getLabel($items, $id);
          }
          
          public static function codeLanguage($id = null)
          {
              $items = [
                  self::CODE_TYPE_HTML              => 'html',
                  self::CODE_TYPE_JAVASCRIPT        => 'js',
                  self::CODE_TYPE_CSS               => 'css',
                  self::CODE_TYPE_PHP               => 'php',
                  self::CODE_TYPE_JSON              => 'json',
                  self::CODE_TYPE_BASH              => 'bash',
              ];

              return self::getLabel($items, $id);
          }
          
          public static function codeType($id = null)
          {
              $items = [
                  self::CODE_TYPE_HTML              => trans('expendable::label.html'),
                  self::CODE_TYPE_JAVASCRIPT        => trans('expendable::label.java_script'),
                  self::CODE_TYPE_CSS               => trans('expendable::label.css'),
                  self::CODE_TYPE_PHP               => trans('expendable::label.php'),
                  self::CODE_TYPE_JSON              => trans('expendable::label.json'),
                  self::CODE_TYPE_BASH              => trans('expendable::label.bash'),
              ];

              return self::getLabel($items, $id);
          }
          
          public static function youtubeImageQuality($id = null)
          {
              $items = [
                  self::MAXIMUM_RESOLUTION_QUALITY_THUMBNAIL  => trans('expendable::label.maximum_resolution_thumbnail'),
                  self::STANDARD_DEFINITION_QUALITY_THUMBNAIL => trans('expendable::label.standard_definition_thumbnail'),
                  self::HIGH_QUALITY_THUMBNAIL                => trans('expendable::label.high_quality_thumbnail'),
                  self::MEDIUM_QUALITY_THUMBNAIL              => trans('expendable::label.medium_quality_thumbnail'),
                  self::PLAYER_BACKGROUND_QUALITY_THUMBNAIL   => trans('expendable::label.player_background_thumbnail'),
              ];

              return self::getLabel($items, $id);
          }

          public static function youtubeImageThumbnail($id = null)
          {
              $items = [
                  self::MEDIUM_QUALITY_THUMBNAIL              => trans('expendable::label.medium_quality_thumbnail'),
                  self::PLAYER_BACKGROUND_QUALITY_THUMBNAIL   => trans('expendable::label.player_background_thumbnail'),
                  self::NORMAL_QUALITY_THUMBNAIL              => trans('expendable::label.normal_quality_thumbnail'),
                  self::START_QUALITY_THUMBNAIL               => trans('expendable::label.start_thumbnail'),
                  self::MIDDLE_QUALITY_THUMBNAIL              => trans('expendable::label.middle_thumbnail'),
                  self::END_QUALITY_THUMBNAIL                 => trans('expendable::label.end_thumbnail'),
              ];

              return self::getLabel($items, $id);
          }

          public static function youtubeDomain($id = null)
          {
              $items = [
                  self::YOUTUBE_DOMAIN_I   => 'i',
                  self::YOUTUBE_DOMAIN_I1  => 'i1',
                  self::YOUTUBE_DOMAIN_I3  => 'i3',
              ];

              return self::getLabel($items, $id);
          }

          public static function mailActions($id = null)
          {
              $config      = app('config')->get('expendable.mail');
              $dataActions = $config['actions'];
              $dataResult  = [];

              foreach ($dataActions as $action)
              {
                  $dataResult[$action] = trans('expendable::mail.' . $action);
              }

              return self::getLabel($dataResult, $id);
          }

          public static function states($id = null)
          {
              $config      = app('config')->get('expendable.state');
              $dataActions = $config;
              $dataResult  = [];

              foreach ($dataActions as $key => $action)
              {
                  $dataResult[$key] = trans($action['label']);
              }

              return self::getLabel($dataResult, $id);
          }


          public static function getLabel($items, $id = null)
          {
              if (isset($id))
              {
                  return isset($items[$id]) ? $items[$id] : trans('expendable::label.na');
              }

              return $items;
          }
      }