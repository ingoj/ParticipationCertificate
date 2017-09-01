<?php


/**
 * Interface ParticipationParser
 */

interface ParticipationParser{

	/**
	 * @param string $text
	 * @param array $replacements
	 * @return string
	 */


	public function parse($text, array $replacements = array());

}