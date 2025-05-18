<?php

namespace App\View\Components;


use Illuminate\View\Component;

class Image extends Component
{

	public $title;
	public $alt;
	public $src;
	public $dataSrc;
	public $class;
	public $style;
	public $width;
	public $height;
	public $lazy;

	public function __construct($class = '', $style = '', $media = NULL, $lazy = false, $width = NULL, $height = NULL)
	{
		if($lazy === true || $lazy === 'true') $class .= ' lazy';
		$this->class = $class;
		$this->lazy = $lazy;
		$this->width = $width;
		$this->height = $height;
		$this->style = $style;

		if(!empty($media)) $this->setAttributesByMedia($media, $lazy);
	}

	public function setAttributesByMedia(array $media, $lazy)
	{
		$imageURL = sprintf('%s/storage/%s', url(''), $media['url'] ?? NULL);
		$imagePlaceholderURL = asset('/assets/10x10-transparent.png');
		
		if($lazy === true || $lazy === 'true') {
			$this->src = $imagePlaceholderURL;
			$this->dataSrc = $imageURL;
		}
		else {
			$this->src = $imageURL;
			$this->dataSrc = $imagePlaceholderURL;
		}
		
		$options = $media['options'] ?? NULL;
		if(empty($options)) return false;

		$options = json_decode($options, true);
		$this->title = $options['title'] ?? NULL;
		$this->alt = $options['alt'] ?? NULL;
	}

	public function render()
	{
		return view('components.image');
	}
}
