<?php
/**
 * Block that shows a video from an external source (YouTube, Vimeo)
 * @author bummzack
 */
class VideoBlock extends Block
{
	private static $db = array(
		'ExternalMedia' => 'Varchar(255)',
		'ManualRatio' => 'Float'
	);

	private static $has_one = array(
		'MediaThumb' => 'Image',
		'Preview' => 'Image'
	);
	
	/**
	 * Aspect ratios to use for manual ratio dropdown.
	 * Can be customized via config.yml
	 * @var array
	 */
	private static $aspect_ratios = array(
		'0', '16/9', '8/5', '4/3'
	);
	
	/**
	 * Color to customize the vimeo player. 
	 * Can be set via config.yml
	 * @var string
	 */ 
	private static $player_color = '44BBFF';
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		
		// field to enter the video URL
		$externalField = TextField::create('ExternalMedia', _t('VideoBlock.VIDEO_URL', 'Video URL'));
		
		// field for video aspect-ratio
		$ratios = Config::inst()->get('VideoBlock', 'aspect_ratios');
		$ratioField = null;
		if(is_array($ratios)){
			$values = array();
			foreach ($ratios as $ratio){
				if(preg_match('{(\d+)/(\d+)}', $ratio, $matches)){
					$float = number_format(intval($matches[2]) / intval($matches[1]), 6);
					$values[$float] = $matches[0];
				} else if($ratio == '0'){
					$values['0'] = 'Automatic';
				}
			}
			
			if(count($values) > 0){
				$ratioField = DropdownField::create('ManualRatio', _t('VideoBlock.ASPECT_RATIO', 'Aspect ratio'), $values);
			}
		}
		
		// preview thumbnail if media is set
		if($this->MediaThumbID){
			$thumb = $this->MediaThumb()->SetWidth(120);
			
			$fields->addFieldToTab('Root.Main', 
				LiteralField::create('MediaThumb', '<div class="field"><div class="middleColumn">' .$thumb->Tag . '</div></div>')
			);
		}
		
		$fields->addFieldToTab('Root.Main', $externalField);
		if($ratioField){
			$fields->addFieldToTab('Root.Main', $ratioField);
		}
		
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	/**
	 * Fetch and update the media thumbnail
	 */
	public function updateOEmbedThumbnail()
	{
		$oembed = $this->Media();
		if($oembed && $oembed->thumbnail_url){
			$fileName = preg_replace('/[^A-z0-9\._-]/', '', $oembed->thumbnail_url);
			if($existing = File::find($fileName)){
				$this->MediaThumbID = $existing->ID;
			} else {
				$contents = @file_get_contents($oembed->thumbnail_url);
				if($contents){
					$folder = Folder::find_or_make('downloaded');
					file_put_contents($folder->getFullPath() . $fileName, $contents);
					$file = Image::create();
					$file->setFilename($folder->getFilename() . $fileName);
					$file->ParentID = $folder->ID;
					$this->MediaThumbID = $file->write();
				}
			}
		} else {
			$this->MediaThumbID = 0;
		}
	}

	/**
	 * Get the embedded media
	 * @return false|Oembed_Result
	 */
	public function Media()
	{
		if($this->ExternalMedia){
			return Oembed::get_oembed_from_url($this->ExternalMedia, false, array(
				'color' 	=> Config::inst()->get('VideoBlock', 'player_color'),
				'title' 	=> false,
				'portrait'	=> false,
				'byline'	=> false,
				'loop'		=> false
			));
		}
		return false;
	}

	/**
	 * The ratio of the height compared to the width
	 * @param number $mult a multiplier for the ratio. Defaults to 100, so that the output is in percent
	 * @return number
	 */
	public function MediaHeightRatio($mult = 100)
	{
		if($this->ManualRatio){
			return round($mult * $this->ManualRatio, 2);
		}
		$ratio = 0;
		if($media = $this->Media()){
			$ratio = $media->height / $media->width;
		}
		return round($mult * $ratio);
	}

	protected function onBeforeWrite()
	{
		parent::onBeforeWrite();
		if($this->isChanged('ExternalMedia', 2) || ($this->ExternalMedia && !$this->MediaThumbID)){
			$this->updateOEmbedThumbnail();
		}
	}
}