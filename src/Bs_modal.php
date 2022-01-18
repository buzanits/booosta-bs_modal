<?php
namespace booosta\bs_modal;

use \booosta\Framework as b;
b::init_module('bs_modal');

class BS_Modal extends \booosta\ui\UI
{
  use moduletrait_bs_modal;

  protected $content, $title;
  protected $closebutton = true, $closebutton_text;
  protected $buttons;
  protected $static = false;
  protected $width;

  public function after_instanciation()
  {
    parent::after_instanciation();

    if(is_object($this->topobj) && is_a($this->topobj, "\\booosta\\webapp\\Webapp")):
      $this->topobj->moduleinfo['bs_modal'] = true;
      if($this->topobj->moduleinfo['jquery']['use'] == '') $this->topobj->moduleinfo['jquery']['use'] = true;
      if($this->topobj->moduleinfo['bootstrap']['use'] == '') $this->topobj->moduleinfo['bootstrap']['use'] = true;
    endif;

    $this->closebutton_text = $this->t('Close');
    $this->buttons = [];
  }

  public function get_htmlonly()
  {
    if($this->static):
      $static = "data-backdrop='static' data-keyboard='false'";
      $closex = '';
    else:
      $closex = "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
      $static = '';
    endif;

    if($this->title) $head = "<div class='modal-header'><h4 class='modal-title' id='bs_modal_label_$this->id'>$this->title</h4>$closex</div>";

    if($this->closebutton || sizeof($this->buttons)):
      $footer = "<div class='modal-footer'>";
      if(sizeof($this->buttons))
        foreach($this->buttons as $button):
          if(strstr($button['link'], 'javascript:')) $link = $button['link'];
          else $link = "location.href=\"{$button['link']}\"";

          $footer .= "<button type='button' class='btn btn-default' onClick='$link'>{$button['text']}</button>";
        endforeach;
      if($this->closebutton) $footer .= "<button type='button' class='btn btn-default' data-dismiss='modal'>$this->closebutton_text</button>";
      $footer .= "</div>";
    endif;

    if($this->width) $style = "style='max-width: {$this->width}px; width: 100%'";
     
    if(is_readable($this->content)):
      $parser = $this->makeInstance('Templateparser', $this->config('language'));
      $content = $parser->parseTemplate($this->content);
    else:
      $content = $this->content;
    endif;

    return "<div class='modal fade' id='bs_modal_$this->id' tabindex='-1' role='dialog' aria-labelledby='bs_modal_label_$this->id' aria-hidden='true' $static>
            <div class='modal-dialog' $style> <div class='modal-content' $style> $head
            <div class='modal-body'> $content </div> $footer </div> </div> </div>";
  }

  public function get_js()
  {
    return '';
  }

  public function get_html_link($text = null, $with_html = true)
  {
    if($text == null) $text = 'Link';
    if($with_html) $html = $this->get_html();
    return "<div class='bs_modal_$this->id' data-toggle='modal' data-target='#bs_modal_$this->id'>$text</div> $html";
  }

  public function get_button_link($text = null, $with_html = true)
  {
    if($text == null) $text = 'Link';
    if($with_html) $html = $this->get_html();
    return "<button type='button' class='btn btn-primary default' data-toggle='modal' data-target='#bs_modal_$this->id'>$text</button> $html";
  }

  public function get_html_image_link($image, $with_html = true)
  {
    return $this->get_html_link("<img src='$image'>", $with_html);
  }

  public function set_content($content) { $this->content = $content; }
  public function set_title($title) { $this->title = $title; }
  public function set_static($flag) { $this->static = $flag; }
  public function set_closebutton($closebutton) { $this->closebutton = $closebutton; }
  public function set_closebutton_text($closebutton_text) { $this->closebutton = true; $this->closebutton_text = $closebutton_text; }
  public function add_button($text, $link) { $this->buttons[] = ['text' => $text, 'link' => $link]; }
  public function set_width($width) { $this->width = $width; }
}
