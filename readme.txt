InsertTag usage:

{{levelMania::level, type, mode, section, sectionMode}}

level: number (rootlevel = 1)
type: string (id, title, alias, pageTitle...)
mode: number or string (if level > level count:: 0 -> last existing level, >0 -> specific level, string -> string value), default: 0
section: name of section, if 0 this mode is inactive, returns article ids from section and selected level
sectionMode (only if section <> 0): 0 or 1, 0 -> comma separated list of article ids, 1 -> list of insert_article tags with article ids

If used for section article ids and sectionMode is 1, you must use $this->replaceInsertTags(), for example:
$this->replaceInsertTags('{{levelMania::3, id, 0, SlideShow, 1}}')


Example Default Behavior:

- Menu A1 (Level 1)
-- Menu A2 (Level 2)
--- Menu A3 (Level 3)

Active Menu = Menu A3

{{levelMania::1, title, 0, 0, 0}}         => Menu A1
{{levelMania::2, title, 0, 0, 0}}         => Menu A2
{{levelMania::3, title, 0, 0, 0}}         => Menu A3
{{levelMania::4, title, 0, 0, 0}}         => Menu A3
{{levelMania::4, title, 2, 0, 0}}         => Menu A2
{{levelMania::4, title, BlaBla, 0, 0}}    => BlaBla


Example Section Behavior:

{{levelMania::1, id, 0, SlideShow, 0}}         => 5
{{levelMania::1, id, 0, SlideShow, 1}}         => {{insert_article::5}}

{{levelMania::1, id, 0, LeftColumn, 0}}        => 3,4
{{levelMania::1, id, 0, LeftColumn, 1}}        => {{insert_article::3}}{{insert_article::4}}


This example shows a HeaderSlideshow,
if no Slideshow is found in the actual level, it will search at the specified upper level
and if there is nothing found the Slideshow from the StartPage will be shown

if(strlen($this->replaceInsertTags($this->sections['SlideShow'])) != 0)
{
	echo $this->sections['SlideShow'];
}
else if($this->replaceInsertTags('{{levelMania::3, alias, 0, SlideShow, 0}}') != 0)
{
	echo $this->replaceInsertTags('{{levelMania::3, alias, 0, SlideShow, 1}}');
}
else
{
	echo '{{insert_article::19}}';
}