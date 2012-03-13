<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Tim Gatzky 2012 
 * @author     Tim Gatzky <info@tim-gatzky.de>
 * @package    zHeadlineImages
 * @license    LGPL 
 * @filesource
 */


class ContentTextHeadlineImage extends Frontend
{
	/**
	 * Add headline image to template
	 * @param object (Template Object)
	 */
	public function __construct($objTemplate)
	{
		// store regular headline
		
		$this->import('Database');
		$objImage = $this->Database->prepare("SELECT headline_addImage, headline_singleSRC, headline_alt, headline_size, headline_imagemargin, headline_fullsize, headline_caption, headline_floating FROM tl_content WHERE id=?")
 						->limit(1)
 						->execute($objTemplate->id);
 		
 		$arrImage = $objImage->fetchAssoc();
 		// add image to template if necessary
 		if($arrImage['headline_addImage'] && $arrImage['headline_singleSRC'] != '' && is_file(TL_ROOT . '/' . $arrImage['headline_singleSRC']) )
 		{
 			$this->addImageToTemplate($objTemplate, $arrImage, 'headline_');
 		}
	}

	
	/**
	 * Contaos internal functions like addImageToTemplate, getImage only work with standard fieldnames like 'singleSRC' etc.
	 * This function is equal to the default function but replaces fieldname prefixes with '' to get the default fieldnames
	 *
	 * Add an image to a template
	 * @param object
	 * @param array
	 * @param integer
	 * @param string
	 */
	protected function addImageToTemplate($objTemplate, $arrImage, $strFieldPrefix = '', $intMaxWidth=false, $strLightboxId=false)
	{
		// replace the field prefix
		//if( strlen($strFieldPrefix) && $strFieldPrefix != '' )
			$tmp = array();
			foreach($arrImage as $k => $v)
			{
			   $newKey = str_replace($strFieldPrefix, '', $k);
			   $tmp[$newKey] = $v;
			}
			$arrImage = $tmp;
			unset($tmp);
		//}
		
		// Default
		$size = deserialize($arrImage['size']);
		$imgSize = getimagesize(TL_ROOT .'/'. $arrImage['singleSRC']);
		
				
		if (!$intMaxWidth)
		{
			$intMaxWidth = (TL_MODE == 'BE') ? 320 : $GLOBALS['TL_CONFIG']['maxImageWidth'];
		}

		if (!$strLightboxId)
		{
			$strLightboxId = 'lightbox';
		}

		// Store original dimensions
		$objTemplate->width = $imgSize[0];
		$objTemplate->height = $imgSize[1];
	
		// Adjust image size
		if ($intMaxWidth > 0 && ($size[0] > $intMaxWidth || (!$size[0] && !$size[1] && $imgSize[0] > $intMaxWidth)))
		{
			$arrMargin = deserialize($arrImage['imagemargin']);
			
			// Subtract margins
			if (is_array($arrMargin) && $arrMargin['unit'] == 'px')
			{
				$intMaxWidth = $intMaxWidth - $arrMargin['left'] - $arrMargin['right'];
			}

			// See #2268 (thanks to Thyon)
			$ratio = ($size[0] && $size[1]) ? $size[1] / $size[0] : $imgSize[1] / $imgSize[0];

			$size[0] = $intMaxWidth;
			$size[1] = floor($intMaxWidth * $ratio);
		}
		$src = $this->getImage($this->urlEncode($arrImage['singleSRC']), $size[0], $size[1], $size[2]);
		
		
		// Image dimensions
		if (($imgSize = @getimagesize(TL_ROOT .'/'. $headline_src)) !== false)
		{
			$objTemplate->headline_arrSize = $imgSize;
			$objTemplate->headline_imgSize = ' ' . $headline_imgSize[3];
			
		}
		// Float image
		if (in_array($arrImage['floating'], array('left', 'right')))
		{
			$objTemplate->headline_floatClass = ' float_' . $arrImage['floating'];
			$objTemplate->headline_float = ' float:' . $arrImage['floating'] . ';';
		}
		
		// Image link
		if (strlen($arrImage['imageUrl']) && TL_MODE == 'FE')
		{
			$objTemplate->headline_href = $arrImage['imageUrl'];
			$objTemplate->headline_attributes = $arrImage['fullsize'] ? LINK_NEW_WINDOW : '';
		}

		// Fullsize view
		elseif ($arrImage['fullsize'] && TL_MODE == 'FE')
		{
			$objTemplate->headline_href = $this->urlEncode($arrImage['singleSRC']);
			$objTemplate->headline_attributes = ' rel="' . $strLightboxId . '"';
		}
		
		// rebuild the template vars
		$objTemplate->headline_src = $src;
		$objTemplate->headline_alt = specialchars($arrImage['alt']);
		$objTemplate->headline_fullsize = $arrImage['fullsize'] ? true : false;
		$objTemplate->headline_addBefore = ($arrImage['floating'] != 'below');
		$objTemplate->headline_margin = $this->generateMargin(deserialize($arrImage['imagemargin']), 'padding');
		$objTemplate->headline_caption = $arrImage['caption'];
		$objTemplate->headline_addImage = true;
	}


}

?>