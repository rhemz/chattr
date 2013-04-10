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
    '<object width="425" height="344">
    <param name="movie" value="http://www.youtube.com/v/$1?fs=1"</param>
    <param name="allowFullScreen" value="true"></param>
    <param name="allowScriptAccess" value="always"></param>
    <embed src="http://www.youtube.com/v/$1?fs=1"
        type="application/x-shockwave-flash" allowscriptaccess="always" width="425" height="344">
    </embed>
    </object>';



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