<?php

class Message_Parser_Youtube extends Message_Parser_Base
{

	const Search_Regex = 
	'~
    # Match non-linked youtube URL in the wild. (Rev:20111012)
    https?://         # Required scheme. Either http or https.
    (?:[0-9A-Z-]+\.)? # Optional subdomain.
    (?:               # Group host alternatives.
      youtu\.be/      # Either youtu.be,
    | youtube\.com    # or youtube.com followed by
      \S*             # Allow anything up to VIDEO_ID,
      [^\w\-\s]       # but char before ID is non-ID char.
    )                 # End host alternatives.
    ([\w\-]{11})      # $1: VIDEO_ID is exactly 11 chars.
    (?=[^\w\-]|$)     # Assert next char is non-ID or EOS.
    (?!               # Assert URL is not pre-linked.
      [?=&+%\w]*      # Allow URL (query) remainder.
      (?:             # Group pre-linked alternatives.
        [\'"][^<>]*>  # Either inside a start tag,
      | </a>          # or inside <a> element text contents.
      )               # End recognized pre-linked alts.
    )                 # End negative lookahead assertion.
    [?=&+%\w-]*        # Consume any URL (query) remainder.
    ~ix';

    const Replace_Regex = 
    '<iframe width="853" height="480" src="http://www.youtube-nocookie.com/embed/$1" frameborder="0" allowfullscreen></iframe>';

	public function contains_key()
	{
		return preg_match(self::Search_Regex, $this->text);
	}


	public function parse()
	{
		$this->text = preg_replace(self::Search_Regex, self::Replace_Regex, $this->text);
		return $this->text;
	}
}