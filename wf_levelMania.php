<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Andi Platen
 * @author     Andi Platen
 * @package    wf_levelMania
 * @license    GNU/LGPL
 */


class wf_levelMania extends Frontend
{
	public function replaceInsertTags($strBuffer, $blnCache=true)
	{
		$arrTag = explode('::', $strBuffer);

		if($arrTag[0] == 'levelMania')
		{
			return $this->makeReplacement($arrTag[1]);
		}

		return false;
	}

	function makeReplacement($strParameter)
	{
		global $objPage;

		$pages = array();
		$pageId = $objPage->id;

		// $strParameter -> level, type(id, title, alias, pageTitle...), mode(if level > level count: 0->last existing level, >0->specific level, string->string value) //
		$strParameter = explode(',', $strParameter);
		if(count($strParameter) != 5)
		{
			die('Warning: levelMania => wrong amount of parameter, ' . count($strParameter) . ' given, 5 expected');
		}
		$strParameter[0] = trim($strParameter[0]);
		$strParameter[0] = $strParameter[0] - 1;
		$strParameter[1] = trim($strParameter[1]);
		$strParameter[2] = trim($strParameter[2]);
		$strParameter[3] = trim($strParameter[3]);
		$strParameter[4] = trim($strParameter[4]);

		// Get all pages up to the root page //
		do
		{
			$objPages = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
									   ->limit(1)
									   ->execute($pageId);

			$type = $objPages->type;
			$pageId = $objPages->pid;
			$pages[] = $objPages->row();
		}
		while($pageId > 0 && $type != 'root' && $objPages->numRows);

		$pages = array_reverse($pages);


		// Start: Default Behavior //
		if(count($pages) <= $strParameter[0])
		{
			if(is_numeric($strParameter[2]))
			{
				if($strParameter[2] == 0)
				{
					$strParameter[0] = count($pages) - 1;
				}
				else
				{
					$strParameter[0] = $strParameter[2] - 1;
				}
			}
			else
			{
				$returnValue = specialchars($strParameter[2]);
			}
		}

		if(!isset($returnValue))
		{
			$returnValue = specialchars($pages[$strParameter[0]][$strParameter[1]]);
		}
		// End: Default Behavior //


		// Start: Section Behaviour //
		if($strParameter[3] != '0')
		{
			$sectionArticles = $this->Database->prepare("SELECT id FROM tl_article WHERE pid=? AND inColumn=? AND published=1 ORDER BY sorting")
							   				  ->execute($pages[$strParameter[0]]['id'], $strParameter[3]);

			$articleArray = array();
			while($sectionArticles->next())
			{
				if($strParameter[4] == 1)
				{
					$articleArray[] = '{{insert_article::' . $sectionArticles->id . '}}';
				}
				else
				{
					$articleArray[] = $sectionArticles->id;
				}
			}

			if($strParameter[4] == 1)
			{
				$returnValue = implode('', $articleArray);
			}
			else
			{
				$returnValue = implode(',', $articleArray);
			}
			
			if(empty($articleArray))
			{
				$returnValue = 0;
			}
		}
		// End: Section Behaviour //


		return $returnValue;
	}
}

