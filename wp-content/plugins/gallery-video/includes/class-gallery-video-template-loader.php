<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Gallery_Video_Template_Loader
{

    /**
     * Load the Plugin shortcode's frontend
     *
     * @param $images
     * @param $gallery_video_get_option
     * @param $gallery
     */
    public function load_front_end($videos, $gallery_video_get_option, $gallery_video)
    {
        $gallery_videoID = $gallery_video[0]->id;
        global $wpdb, $post;
        $view = $gallery_video[0]->huge_it_sl_effects;
        $arrowfolder = GALLERY_VIDEO_IMAGES_URL . '/arrows';
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
        $view_slug = gallery_video_get_view_slag_by_id($gallery_videoID);
        $disp_type = $gallery_video[0]->display_type;
        $num = $gallery_video[0]->content_per_page;
        $total = intval(((count($videos) - 1) / $num) + 1);
        $total_videos = count($videos);
        $pattern = '/-/';
        $pID = $post->ID;
        $path_site = GALLERY_VIDEO_IMAGES_URL;
        if (isset($_GET['page-video' . $gallery_videoID . $pID])) {
            $page = absint($_GET['page-video' . $gallery_videoID . $pID]);
        } else {
            $page = '';
        }
        $page = intval($page);
        if (empty($page) or $page < 0) {
            $page = 1;
        }
        if ($page > $total) {
            $page = $total;
        }
        $start = $page * $num - $num;
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_it_videogallery_videos where videogallery_id = '%d' order by ordering ASC LIMIT " . $start . "," . $num, $gallery_videoID);
        $page_videos = $wpdb->get_results($query);
        if ($disp_type == 2) {
            $page_videos = $videos;
            $count_page = 9999;
        }
        $has_youtube = false;
        $has_vimeo = false;
        foreach ($videos as $video) {
            if (strpos($video->image_url, 'youtu') !== false) {
                $has_youtube = true;
            }
            if (strpos($video->image_url, 'vimeo') !== false) {
                $has_vimeo = true;
            }
        }
        $sliderheight = $gallery_video[0]->sl_height - 2 * $gallery_video_get_option['gallery_video_slider_slideshow_border_size'];
        $sliderwidth = $gallery_video[0]->sl_width - 2 * $gallery_video_get_option['gallery_video_slider_slideshow_border_size'];
        $slidereffect = $gallery_video[0]->videogallery_list_effects_s;
        $slidepausetime = ($gallery_video[0]->description + $gallery_video[0]->param);
        $sliderpauseonhover = $gallery_video[0]->pause_on_hover;
        $sliderposition = $gallery_video[0]->sl_position;
        $slidechangespeed = $gallery_video[0]->param;
        $trim_slider_title_position = trim($gallery_video_get_option['gallery_video_slider_title_position']);
        $slideshow_title_position = explode('-', $trim_slider_title_position);
        $trim_slider_description_position = trim($gallery_video_get_option['gallery_video_slider_description_position']);
        $slideshow_description_position = explode('-', $trim_slider_description_position);
        switch ($view) {
            case 0:
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-popup' . DIRECTORY_SEPARATOR . 'content-popup-view.php';
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-popup' . DIRECTORY_SEPARATOR . 'content-popup-view.css.php';
                break;
            case 1:
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-slider' . DIRECTORY_SEPARATOR . 'content-slider-view.php';
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-slider' . DIRECTORY_SEPARATOR . 'content-slider-view.css.php';
                break;
            case 3:
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . 'slider-view.php';
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . 'slider-view.css.php';
                break;
            case 4:
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . 'thumbnails-view.php';
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . 'thumbnails-view.css.php';
                break;
            case 5:
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'lightbox-gallery' . DIRECTORY_SEPARATOR . 'lightbox-gallery-view.php';
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'lightbox-gallery' . DIRECTORY_SEPARATOR . 'lightbox-gallery-view.css.php';
                break;
            case 6:
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'justified' . DIRECTORY_SEPARATOR . 'justified-view.php';
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'justified' . DIRECTORY_SEPARATOR . 'justified-view.css.php';
                break;
            case 7:
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'blog-style-gallery' . DIRECTORY_SEPARATOR . 'blog-style-gallery-view.php';
                require GALLERY_VIDEO_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'blog-style-gallery' . DIRECTORY_SEPARATOR . 'blog-style-gallery-view.css.php';
                break;
        }


    }
}